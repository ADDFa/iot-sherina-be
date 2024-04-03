<?php

namespace App\Http;

class Response
{
    public static function success($data, int $status = 200)
    {
        return response()->json($data, $status);
    }

    public static function errors(\Illuminate\Validation\Validator $valiators, int $status = 400)
    {
        return response()->json(["errors" => $valiators->errors()], $status);
    }

    public static function message(string $message, int $status = 400)
    {
        return response()->json(["message" => $message], $status);
    }
}
