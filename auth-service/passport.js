require("dotenv").config();
const JwtStrategy = require("passport-jwt").Strategy,
    ExtractJwt = require("passport-jwt").ExtractJwt;
const opts = {};
opts.jwtFromRequest = ExtractJwt.fromAuthHeaderAsBearerToken();
const secretOrKey = process.env.AUTH_SECRET_KEY;
opts.secretOrKey = secretOrKey;

module.exports = (passport) =>
    passport.use(
        new JwtStrategy(opts, (jwt_payload, done) => done(null, jwt_payload))
    );
