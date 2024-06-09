require("dotenv").config();

const superagent = require("superagent");

const ensureTokenIsValid = () => async (request, response, next) => {
    const token = request.get("Authorization");
    if (!token) {
        return response.formatter.unauthorized(
            "You're not allowed to access this resource.",
        );
    }
    try {
        const result = await superagent
            .get(`${process.env.API_GATEWAY_ENDPOINT}/api/auth/check`)
            .set("Authorization", token)
            .set("user-agent", "node-js");
        request.user = result.body.data.user;
        next();
    } catch (error) {
        console.log(error);
        return response.formatter.unauthorized(error);
    }
};

module.exports = ensureTokenIsValid;
