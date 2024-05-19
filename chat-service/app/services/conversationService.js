const database = require("../../database");
const websocket = require("../../socketio");
const { getIO } = require("../../socketio");
const timeHelper = require("../../helpers/timeHelper");

const list = async (payload) => {
    const roomID = payload.parameters.room_id;
    const page = parseInt(payload.query.page) || 1;
    const perPage = parseInt(payload.query.per_page) || 5;
    return await database("chat_messages")
        .where("room_id", roomID)
        .orderBy("id", "desc")
        .paginate({ perPage, currentPage: page })
        .then(
            async (messages) =>
                await Promise.all(
                    await messages.data
                        .reverse()
                        .map(
                            async (message) => await getBasicMessage(message.id)
                        )
                )
        );
};

const create = async (payload) => {
    if (!payload.body.message) {
        throw new Error("Invalid inputs.");
    }
    if (payload.body.message.length > 200) {
        throw new Error("Message too long!");
    }
    const room = await database("chat_rooms")
        .where("id", payload.body.room_id)
        .first();
    if (!room) {
        throw new Error("Room not found!");
    }
    let message = await database("chat_messages").insert({
        room_id: payload.body.room_id,
        user_id: payload.user.id,
        message: payload.body.message,
        read_by_sender: 1,
        created_at: timeHelper.getTimestamp(),
        updated_at: timeHelper.getTimestamp(),
    });
    message = await getBasicMessage(message[0]);

    const io = getIO();
    if (io) {
        io.emit(`room-${payload.body.room_id}`, message);
    }

    return message;
};

const read = async (payload) => {
    let message = await database("chat_messages")
        .where("id", payload.parameters.message_id)
        .first();
    if (!message) {
        throw new Error("Message not found!");
    }
    const updateData =
        payload.user.id === message.user_id
            ? { read_by_sender: 1 }
            : { read_by_receiver: 1 };
    const messageID = await database("chat_messages")
        .where("id", payload.parameters.message_id)
        .update(updateData);

    return messageID;
};

const getBasicMessage = async (messageID) => {
    return await database("chat_messages")
        .where("id", messageID)
        .first()
        .then(async (message) => {
            message.user = await database("users")
                .where("id", message.user_id)
                .first();
            return message;
        });
};

module.exports = { list, create, read };
