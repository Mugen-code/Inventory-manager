<?php

if (!function_exists('sendSuccessResponse')) {
    function sendSuccessResponse($message, $data = null)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $response;
    }
}

if (!function_exists('sendErrorResponse')) {
    function sendErrorResponse($message, $errors = null, $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}