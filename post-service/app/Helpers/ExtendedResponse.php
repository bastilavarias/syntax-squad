<?php

namespace App\Helpers;
use Illuminate\Pagination\Paginator;

/**
 * Usage
 *  This is a simple Response Class that allows you to method-chain
 *  The creation of response as well as creating a unified response format
 *  The end of your chain must always end with the generate() function
 */
class ExtendedResponse
{
    protected $data = [];

    protected $code = 200;

    protected $success = true;

    protected $message = '';

    protected $slug = '';

    protected $pagination = [];

    public function __construct($data = null, $message = null)
    {
        if (is_null($data) === false) {
            $this->data($data);
        }

        if (is_null($message) === false) {
            $this->message($message);
        }
        return $this;
    }

    public function code(int $code): ExtendedResponse
    {
        $this->code = $code;

        return $this;
    }

    // generic success code
    public function success($code = 200): ExtendedResponse
    {
        $this->code = $code;
        $this->success = true;

        return $this;
    }

    // generic failure code
    public function failed($code = 400): ExtendedResponse
    {
        $this->code = $code;
        $this->success = false;

        return $this;
    }

    // lacks authentication method
    // if auth middleware is not activated by default
    public function unathorized(): ExtendedResponse
    {
        $this->code = 401;
        $this->success = false;

        return $this;
    }

    // user permission specific errors
    public function forbidden(): ExtendedResponse
    {
        $this->code = 403;
        $this->success = false;

        return $this;
    }

    // model search related errors
    public function notFound(): ExtendedResponse
    {
        $this->code = 404;
        $this->success = false;

        return $this;
    }

    // set a custom slug
    public function slug(string $value): ExtendedResponse
    {
        $this->slug = $value;

        return $this;
    }

    public function message(string $value): ExtendedResponse
    {
        if ($this->slug == '') {
            // set slug too
            $this->slug = \Str::slug($value, '_');
        }

        $this->message = $this->translateMessage($value);

        return $this;
    }

    // implement a message translator based on slug given
    protected function translateMessage($fallback)
    {
        return $fallback;
    }

    public function data($value): ExtendedResponse
    {
        if ($value instanceof Paginator) {
            // convert pagination to array
            $pagination = $value->toArray();
            $data = $pagination['data'];
            unset($pagination['data']);

            // separate them on two different array keys to create uniformity
            $this->pagination = $pagination;
            $this->data = $data;
        } else {
            $this->data = $value;
        }

        return $this;
    }

    public function generate()
    {
        return $this->generateResponse();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function generateResponse()
    {
        return response()->json(
            [
                'success' => $this->success,
                'code' => $this->code,
                'slug' => $this->slug,
                'message' => $this->message,
                'data' => $this->data,
                'pagination' => $this->pagination,
            ],
            $this->code
        );
    }
}
