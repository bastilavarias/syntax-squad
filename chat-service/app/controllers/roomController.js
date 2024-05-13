const { Router } = require("express");
const service = require("../services/roomService");

const create = async (request, response) => {
    try {
        const result = await service.create({
            body: request.body,
            user: request.user,
        });
        response.formatter.ok(result);
    } catch (error) {
        response.formatter.badRequest(error.message);
    }
};

const index = async (request, response) => {
    try {
        const result = await service.list({
            body: request.body,
            user: request.user,
            query: request.query,
            parameters: request.params,
        });
        response.formatter.ok(result);
    } catch (error) {
        response.formatter.badRequest(error.message);
    }
};

module.exports = { create, index };
