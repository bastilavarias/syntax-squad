const methods = [
    {
        name: "ok",
        code: "200",
        message: "OK",
        isSuccess: true,
    },
    {
        name: "created",
        code: "201",
        message: "Created",
        isSuccess: true,
    },
    {
        name: "accepted",
        code: "202",
        message: "Accepted",
        isSuccess: true,
    },
    {
        name: "noContent",
        code: "204",
        message: "No Content",
        isSuccess: true,
    },
    {
        name: "badRequest",
        code: "400",
        message: "Bad Request",
        isSuccess: false,
    },
    {
        name: "unauthorized",
        code: "401",
        message: "Unauthorized",
        isSuccess: false,
    },
    {
        name: "forbidden",
        code: "403",
        message: "Forbidden",
        isSuccess: false,
    },
    {
        name: "notFound",
        code: "404",
        message: "Not Found",
        isSuccess: false,
    },
    {
        name: "methodNotAllowed",
        code: "405",
        message: "Method Not Allowed",
        isSuccess: false,
    },
    {
        name: "timeout",
        code: "408",
        message: "Timeout",
        isSuccess: false,
    },
    {
        name: "conflict",
        code: "409",
        message: "Conflict",
        isSuccess: false,
    },
    {
        name: "unprocess",
        code: "422",
        message: "Unprocessable Entity",
        isSuccess: false,
    },
    {
        name: "tooManyRequests",
        code: "429",
        message: "Too Many Requests",
        isSuccess: false,
    },
    {
        name: "serverError",
        code: "500",
        message: "Internal Server Error",
        isSuccess: false,
    },
    {
        name: "badGateway",
        code: "502",
        message: "Bad Gateway",
        isSuccess: false,
    },
    {
        name: "serviceUnavailable",
        code: "503",
        message: "Service Unavailable",
        isSuccess: false,
    },
    {
        name: "gatewayTimeout",
        code: "504",
        message: "Gateway Timeout",
        isSuccess: false,
    },
];

const responseEnhancer = () => (request, response, next) => {
    response.formatter = _generateFormatters(response);
    next();
};

const _generateFormatters = (response) => {
    const formatter = {};
    let responseBody = {};

    methods.map((method) => {
        if (method.isSuccess) {
            formatter[method.name] = (data, meta) => {
                responseBody = _generateSuccessResponse({ data, meta });
                response.status(parseInt(method.code)).json(responseBody);
            };
        } else {
            formatter[method.name] = (error, meta) => {
                responseBody = _generateErrorResponse({ error, meta });
                response.status(parseInt(method.code)).json(responseBody);
            };
        }
    });

    return formatter;
};

const _generateSuccessResponse = ({ data, meta }) => ({
    data,
    meta,
});

const _generateErrorResponse = ({ error, meta }) => ({
    error,
    meta,
});

module.exports = responseEnhancer;
