const express = require("express");
const passport = require("passport");
const router = express.Router();
const controller = require("../controllers/authController");

router.get("/", (req, res) => res.send('ok'));
router.post("/login", controller.login);
router.get(
    "/check",
    passport.authenticate("jwt", { session: false }),
    controller.check
);

module.exports = router;
