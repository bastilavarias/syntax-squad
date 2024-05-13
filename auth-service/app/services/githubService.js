const superagent = require("superagent");
require("dotenv").config();

const getToken = async (code) => {
    const url = "https://github.com/login/oauth/access_token";
    const response = await superagent.post(url).send({
        client_id: process.env.GITHUB_CLIENT_ID,
        client_secret: process.env.GITHUB_CLIENT_SECRET,
        code,
        accept: "json",
    });
    if (response.body.error_description) {
        throw new Error(response.body.error_description);
    }
    return response.body.access_token;
};

const getUser = async (code) => {
    const token = await getToken(code);
    const url = "https://api.github.com/user";
    const response = await superagent
        .get(url)
        .set("Authorization", `token ${token}`)
        .set("user-agent", "node-js");
    return response.body ? response.body : null;
};

module.exports = { getUser };
