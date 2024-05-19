const rateLimit = 500;
const interval = 60 * 1000;
const requestCounts = {};

setInterval(() => {
    Object.keys(requestCounts).forEach((ip) => {
        requestCounts[ip] = 0; // Reset request count for each IP address
    });
}, interval);

const rateLimiter = () => (request, response, next) => {
    const ip = request.ip;
    requestCounts[ip] = (requestCounts[ip] || 0) + 1;
    if (requestCounts[ip] > rateLimit) {
        return response.formatter.tooManyRequests("Rate limit exceeded.");
    }

    request.setTimeout(15000, () => {
        response.formatter.gatewayTimeout("Gateway Timeout");
        request.abort();
    });

    next();
};

module.exports = rateLimiter;
