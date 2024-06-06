const { Server } = require("socket.io");

let io;
let _socket;

const initialize = (server) => {
    if (!io) {
        io = new Server(server, {
            cors: {
                origin: process.env.WEB_CLIENT_ENDPOINT,
            },
        });
        io.on("connection", (socket) => {
            _socket = socket;
        });
    }
    return io;
};

const getIO = () => {
    if (!io) {
        throw new Error(
            "Socket.IO not initialized. Call initializeSocket first."
        );
    }
    return io;
};
const getSocket = () => {
    if (!_socket) {
        throw new Error(
            "Socket.IO socket not initialized. Call initializeSocket first."
        );
    }
    return _socket;
};

module.exports = { initialize, getIO, getSocket };
