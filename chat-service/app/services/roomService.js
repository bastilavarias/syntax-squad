const database = require("../../database");
const { getSocket } = require("../../websocket");
const timeHelper = require("../../helpers/timeHelper");

const create = async (payload) => {
    if (!payload.body.message && !payload.body.member_id) {
        throw new Error("Invalid inputs.");
    }
    if (payload.body.message.length > 200) {
        throw new Error("Message too long!");
    }
    let roomID = null;
    const memberIDs = [payload.user.id, payload.body.member_id];
    const existingRoom = await database("chat_room_members")
        .select("room_id")
        .whereIn("user_id", memberIDs)
        .groupBy("room_id")
        .first();
    if (existingRoom) {
        roomID = existingRoom.room_id;
        await database("chat_messages").insert({
            room_id: roomID,
            user_id: payload.user.id,
            message: payload.body.message,
            created_at: timeHelper.getTimestamp(),
            updated_at: timeHelper.getTimestamp(),
        });
    } else {
        const roomDB = await database("chat_rooms").insert({
            created_at: timeHelper.getTimestamp(),
            updated_at: timeHelper.getTimestamp(),
        });
        roomID = roomDB[0];
        await database("chat_messages").insert({
            room_id: roomID,
            user_id: payload.user.id,
            message: payload.body.message,
            created_at: timeHelper.getTimestamp(),
            updated_at: timeHelper.getTimestamp(),
        });
        for (const memberID of memberIDs) {
            await database("chat_room_members").insert({
                room_id: roomID,
                user_id: memberID,
                created_at: timeHelper.getTimestamp(),
                updated_at: timeHelper.getTimestamp(),
            });
        }
    }
    const foundRoom = await getBasicRoom(roomID);
    const socket = getSocket();
    for (const memberID of memberIDs) {
        if (socket) {
            socket.emit(`user-${memberID}`, foundRoom);
        }
    }

    return foundRoom;
};

const list = async (payload) => {
    const page = parseInt(payload.query.page) || 1;
    const perPage = parseInt(payload.query.per_page) || 5;

    return await database("chat_room_members")
        .select("chat_room_members.room_id")
        .select(
            database.raw(
                "MAX(chat_messages.created_at) as latest_message_created_at"
            )
        )
        .join("chat_messages", function () {
            this.on("chat_messages.room_id", "=", "chat_room_members.room_id");
        })
        .whereIn("chat_room_members.user_id", [payload.user.id])
        .groupBy("chat_room_members.room_id")
        .orderBy("latest_message_created_at", "desc")
        .paginate({ perPage, currentPage: page })
        .then(
            async (rooms) =>
                await Promise.all(
                    await rooms.data.map(async (room) => {
                        return await getBasicRoom(room.room_id);
                    })
                )
        );
};

const get = async (payload) => {
    const room = await getBasicRoom(payload.parameters.room_id);
    if (!room) {
        throw new Error("Room not found!");
    }
    return room;
};

const getBasicRoom = async (roomID) => {
    const room = await database("chat_rooms")
        .where("chat_rooms.id", roomID)
        .first();
    if (!room) {
        return null;
    }
    room.members = await database("chat_room_members")
        .where("room_id", roomID)
        .then(async (members) => {
            return await Promise.all(
                await members.map(async (member) => {
                    member.user = await database("users")
                        .where("id", member.user_id)
                        .first();

                    return member;
                })
            );
        });
    room.latest_message = await database("chat_messages")
        .where("room_id", room.id)
        .orderBy("id", "desc")
        .first()
        .then(async (message) => {
            message.user = await database("users")
                .where("id", message.user_id)
                .first();
            return message;
        });

    return room;
};

module.exports = { create, list, get };
