const { Router } = require("express");
const service = require("../services/authService");

const login = async (request, response) => {
    try {
        const payload = { code: request.body.code };
        const result = await service.login(payload);
        response.formatter.ok(result);
    } catch (error) {
        response.formatter.badRequest(error.message);
    }
};

const check = async (request, response) => {
    try {
        const result = await service.check(request.headers);
        response.formatter.ok(result);
    } catch (error) {
        console.log(error);
        response.formatter.badRequest(error.message);
    }
};

module.exports = { login, check };
