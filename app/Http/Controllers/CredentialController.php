<?php

namespace App\Http\Controllers;

use App\Http\Response;
use App\Models\Credential;

class CredentialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::success(Credential::all());
    }

    public function reset(Credential $credential)
    {
        $credential->password = password_hash("12345678", PASSWORD_DEFAULT);
        $credential->save();
        return Response::success($credential);
    }
}
