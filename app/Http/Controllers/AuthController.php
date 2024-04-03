<?php

namespace App\Http\Controllers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Response;
use App\Models\Credential;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username"  => "required|string",
            "password"  => "required|string"
        ]);
        if ($validator->fails()) return Response::errors($validator);

        $credential = Credential::where("username", $request->username)->first();

        $failsMessage = "Username atau Password salah!";
        if (!$credential) return Response::message($failsMessage);

        $passwordVerified = password_verify($request->password, $credential->password);
        if (!$passwordVerified) return Response::message($failsMessage);

        if ($credential->role === "admin") {
            $payload = $credential->toArray();
            unset($payload["username"]);
        } else {
            $driverCredential = Credential::with("driver")->find($credential->id);
            $payload = $driverCredential->toArray();
        }

        $auth = $this->generateToken($payload);
        return Response::success($auth);
    }

    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "refresh_token"  => "required|string"
        ]);
        if ($validator->fails()) return Response::errors($validator);

        try {
            $payload = JWT::decode($request->refresh_token, new Key(env("REFRESH_JWT_KEY"), "HS256"));
            return $this->generateToken((array) $payload);
        } catch (Exception $e) {
            return Response::message($e->getMessage());
        }
    }

    private function generateToken(array $payload)
    {
        try {
            $now = time();

            $accessToken = function () use ($payload, $now) {
                $payload["exp"] = $now + 10;
                return JWT::encode($payload, env("SECRET_JWT_KEY"), "HS256");
            };

            $refreshToken = function () use ($payload, $now) {
                $payload["exp"] = $now + 604800;
                return JWT::encode($payload, env("REFRESH_JWT_KEY"), "HS256");
            };

            $payload["exp"] = $now + 10;
            $result = [
                "access_token"  => $accessToken(),
                "refresh_token" => $refreshToken(),
                "payload"       => $payload
            ];

            return $result;
        } catch (Exception $e) {
            return Response::message($e->getMessage());
        }
    }
}
