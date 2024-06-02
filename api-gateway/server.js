require("dotenv").config();

const express = require("express");
const morgan = require("morgan");
const { createWriteStream } = require("fs");
const { join } = require("path");
const cors = require("cors");
const responseFilter = require("./middlewares/response-filter");
const rateLimiter = require("./middlewares/rate-limiter");
const proxy = require("express-http-proxy");

const server = express();

server.use(cors());
server.use(
    morgan("combined", {
        stream: createWriteStream(join(__dirname, "access.log"), {
            flags: "a",
        }),
    })
);
server.disable("x-powered-by");
server.use(responseFilter());
server.use(rateLimiter());

server.get("/", (_, response) => {
    response.send(
        "Welcome to the API Gateway of the SyntaxSquad microservices!"
    );
});

const services = [
    {
        route: "/auth",
        target: process.env.AUTH_SERVICE_ENDPOINT,
    },
    {
        route: "/post",
        target: process.env.POST_SERVICE_ENDPOINT,
    },
    {
        route: "/chat",
        target: process.env.CHAT_SERVICE_ENDPOINT,
    },
];
services.forEach(({ route, target }) => {
    try {
        console.log(target);
        server.use(route, proxy(target));
    } catch (e) {
        console.log(e);
    }
});

const port = 3000;
server.listen(port, () => {
    console.log(`Service running at localhost:${port}`);
});
