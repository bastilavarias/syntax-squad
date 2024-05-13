const { Router } = require("express");
const router = Router();
const authRoute = require("./app/routes/authRoute");

router.use("/", authRoute);

module.exports = router;
