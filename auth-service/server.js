const express = require("express");
const morgan = require("morgan");
const { createWriteStream } = require("fs");
const { join } = require("path");
const api = require("./api");
const responseFilter = require("./middlewares/response-filter");
const bodyParser = require("body-parser");
const cors = require("cors");
const passport = require("passport");
const jwtPassport = require("./passport");

const server = express();

server.use(cors());
server.use(
    morgan("combined", {
        stream: createWriteStream(join(__dirname, "access.log"), {
            flags: "a",
        }),
    }),
);
server.use(responseFilter());
server.use(bodyParser.json());
server.use(bodyParser.urlencoded({ extended: true }));
server.use("/api/auth", api);
server.use(passport.initialize());
jwtPassport(passport);

const port = 2000;
server.listen(port, () => {
    console.log(`Service running at localhost:${port}`);
});
