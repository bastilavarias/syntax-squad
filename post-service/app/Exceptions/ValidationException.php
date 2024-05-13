<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Str;

class ValidationException extends Exception
{
    protected $validator;

    public function render($request)
    {
        $errors = $this->validator->errors()->toArray();
        $key = array_key_first($errors);
        $message = $errors[$key][0];
        $slug = Str::slug($message, '_');

        return customResponse()
            ->data([])
            ->message($message)
            ->failed(422)
            ->generate();
    }

    /**
     * Create a new exception instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @param  \Symfony\Component\HttpFoundation\Response|null  $response
     * @param  string  $errorBag
     * @return void
     */
    public function __construct($validator)
    {
        $this->validator = $validator;
    }
}
