const express = require("express");
const http = require("http");
const morgan = require("morgan");
const { createWriteStream } = require("fs");
const { join } = require("path");
const api = require("./api");
const responseFilter = require("./middlewares/response-filter");
const bodyParser = require("body-parser");
const cors = require("cors");
const socketio = require("./socketio");
const ensureTokenIsValid = require("./middlewares/ensure-token-is-valid");

const application = express();
const server = http.createServer(application);
socketio.initialize(server);
application.use(cors());
application.use(
    morgan("combined", {
        stream: createWriteStream(join(__dirname, "access.log"), {
            flags: "a",
        }),
    }),
);
application.use(responseFilter());
application.use(ensureTokenIsValid());
application.use(bodyParser.json());
application.use(bodyParser.urlencoded({ extended: true }));
application.use("/api/chat", api);

const port = 3002;
server.listen(port, () => {
    console.log(`Service running at localhost:${port}`);
});
