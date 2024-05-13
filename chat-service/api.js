const { Router } = require("express");
const router = Router();
const roomRoute = require("./app/routes/roomRoute");
const conversationRoute = require("./app/routes/conversationRoute");

router.use("/room", roomRoute);
router.use("/conversation", conversationRoute);

module.exports = router;
