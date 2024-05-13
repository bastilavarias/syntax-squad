const { response } = require("express");
const githubService = require("./githubService");
const jsonWebToken = require("jsonwebtoken");
const database = require("../../database");

const login = async ({ code }) => {
    const githubUser = await githubService.getUser(code);
    let foundUser = await database("users")
        .where("provider_id", githubUser.id)
        .first();
    const dbPayload = {
        username: githubUser.login || null,
        provider_id: githubUser.id || null,
        node_id: githubUser.node_id || null,
        avatar_url: githubUser.avatar_url || null,
        name: githubUser.name || null,
        bio: githubUser.bio || null,
        email: githubUser.email || null,
    };
    const timestamp = new Date().toISOString().slice(0, 19).replace("T", " ");
    if (foundUser) {
        await database("users")
            .where({ provider_id: githubUser.id })
            .update({
                ...dbPayload,
                updated_at: timestamp,
            });
    } else {
        await database("users").insert({
            ...dbPayload,
            created_at: timestamp,
            updated_at: timestamp,
        });
    }
    foundUser = await database("users")
        .where("provider_id", githubUser.id)
        .first();
    const token = signToken(foundUser);
    return {
        user: foundUser,
        token,
    };
};

const check = async (payload) => {
    const token = payload.authorization.split(" ")[1] || null;
    if (!token) {
        throw new Error("Invalid token!");
    }

    return jsonWebToken.verify(token, process.env.AUTH_SECRET_KEY);
};

const signToken = (user) => {
    return jsonWebToken.sign(user, process.env.AUTH_SECRET_KEY, {
        expiresIn: 60 * 60, // 1HR
    });
};

module.exports = {
    login,
    check,
};
