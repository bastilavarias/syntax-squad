const express = require("express");
const router = express.Router();
const controller = require("../controllers/conversationController");

router.get("/:room_id", controller.index);
router.post("/", controller.create);
router.put("/read/:message_id", controller.read);

module.exports = router;
