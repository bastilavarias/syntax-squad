let _io = null;
let _socket = null;

const initialize = (server) => {
    const io = require("socket.io")(server);
    io.on("connection", (socket) => {
        _io = io;
        _socket = socket;
    });
};

module.exports = {
    initialize,
    getIO: () => _io,
    getSocket: () => _socket,
};
