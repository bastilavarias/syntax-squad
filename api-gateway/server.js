require("dotenv").config();

const express = require("express");
const morgan = require("morgan");
const { createWriteStream } = require("fs");
const { join } = require("path");
const cors = require("cors");
const responseFilter = require("./middlewares/response-filter");
const rateLimiter = require("./middlewares/rate-limiter");
const proxy = require("express-http-proxy");
const url = require("url");

const server = express();

server.use(cors());
server.use(
    morgan("combined", {
        stream: createWriteStream(join(__dirname, "access.log"), {
            flags: "a",
        }),
    }),
);
server.disable("x-powered-by");
server.use(responseFilter());
server.use(rateLimiter());

server.use("/auth", proxy("http://localhost:8001"));
server.use("/chat", proxy("http://localhost:8002"));
server.use("/", proxy("http://localhost"));

const port = 3000;
server.listen(port, () => {
    console.log(`Service running at localhost:${port}`);
});
