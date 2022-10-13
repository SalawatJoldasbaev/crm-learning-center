<?php

namespace App\Src;

class Response
{
    public static function data(array $data, int $code)
    {
        return response($data, $code);
    }

    public static function success(string $message = 'success', array $data = [], int $code = 200)
    {
        return response([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error(string $message = 'unknown error', array $data = [], int $code = 500)
    {
        return response([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
