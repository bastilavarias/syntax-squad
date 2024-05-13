<?php
if (!function_exists('customResponse')) {
    function customResponse($data = null, $message = null)
    {
        return new \App\Helpers\ExtendedResponse($data, $message);
    }
}
