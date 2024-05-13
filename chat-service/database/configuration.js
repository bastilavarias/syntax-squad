require("dotenv").config();

const client = "mysql2";
const connection = {
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    port: process.env.DB_PORT,
};

const configs = {
    client,
    connection,
    migrations: {
        directory: __dirname + "/src/db/migrations",
    },
    seeds: {
        directory: __dirname + "/src/db/seeds",
    },
};

module.exports = {
    local: {
        ...configs,
    },

    production: {
        ...configs,
    },
};
