const environment = process.env.NODE_ENV || "production";
const config = require("./configuration")[environment];
module.exports = require("knex")(config);
