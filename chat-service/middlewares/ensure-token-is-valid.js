require("dotenv").config();

const superagent = require("superagent");

const ensureTokenIsValid = () => async (request, response, next) => {
    const token = request.get("Authorization");
    if (!token) {
        return response.formatter.unauthorized(
            "You're not allowed to access this resource."
        );
    }
    try {
        const authServiceEndpoint = `${process.env.AUTH_SERVICE_ENDPOINT}`;
        const result = await superagent
            .get(`${authServiceEndpoint}/api/auth`)
            .set("Authorization", token)
            .set("user-agent", "node-js");
        request.user = result.body.data;
        next();
    } catch (error) {
        return response.formatter.unauthorized(error);
    }
};

module.exports = ensureTokenIsValid;
