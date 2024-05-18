const express = require("express");
const router = express.Router();
const controller = require("../controllers/roomController");

router.post("/", controller.create);
router.get("/:room_id", controller.get);
router.get("/", controller.index);

module.exports = router;
