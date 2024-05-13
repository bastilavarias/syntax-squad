const database = require("../../database");
const websocket = require("../../websocket");
const { getSocket } = require("../../websocket");
const timeHelper = require("../../helpers/timeHelper");

const list = async (payload) => {
    const roomID = payload.parameters.room_id;
    const page = parseInt(payload.query.page) || 1;
    const perPage = parseInt(payload.query.per_page) || 5;
    const totalCountQuery = await database("chat_messages")
        .where("room_id", roomID)
        .count("id as total")
        .first();
    const total = parseInt(totalCountQuery.total);
    const offset = Math.max(total - page * perPage, 0);
    const messages = await database("chat_messages")
        .where("room_id", roomID)
        .orderBy("id", "asc")
        .limit(perPage)
        .offset(offset)
        .then(async (messages) => {
            return await Promise.all(
                await messages.map(async (message) => {
                    return await getBasicMessage(message.id);
                })
            );
        });

    return { messages, total };
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
        created_at: timeHelper.getTimestamp(),
        updated_at: timeHelper.getTimestamp(),
    });
    message = await getBasicMessage(message[0]);

    const socket = getSocket();
    if (socket) {
        socket.emit(`room-${payload.body.room_id}`, message);
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
    message = getBasicMessage(messageID);
    const socket = getSocket();
    if (socket) {
        socket.emit(`room-${payload.body.message.room_id}`, message);
    }

    return message;
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
