const service = require("../services/conversationService");

const index = async (request, response) => {
    try {
        const result = await service.list({
            user: request.user,
            parameters: request.params,
            query: request.query,
        });
        response.formatter.ok({ data: result });
    } catch (error) {
        response.formatter.badRequest(error.message);
    }
};

const create = async (request, response) => {
    try {
        const result = await service.create({
            user: request.user,
            body: request.body,
        });
        response.formatter.ok(result);
    } catch (error) {
        response.formatter.badRequest(error.message);
    }
};
const read = async (request, response) => {
    try {
        const result = await service.read({
            user: request.user,
            body: request.body,
            parameters: request.params,
        });
        response.formatter.ok(result);
    } catch (error) {
        response.formatter.badRequest(error.message);
    }
};

module.exports = { index, create, read };
