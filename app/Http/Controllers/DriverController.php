<?php

namespace App\Http\Controllers;

use App\Http\Response;
use App\Models\Credential;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::success(Driver::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username"  => "required|unique:credentials,username",
            "password"  => "required|string|min:8",
            "name"      => "required|string"
        ]);
        if ($validator->fails()) return Response::errors($validator);

        return DB::transaction(function () use ($validator, $request) {
            $credentialData = $validator->safe(["username"]);
            $credentialData["password"] = password_hash($request->password, PASSWORD_DEFAULT);
            $credential = new Credential($credentialData);
            $credential->save();

            $driverData = $validator->safe(["name"]);
            $driverData["credential_id"] = $credential->id;
            $driver = new Driver($driverData);
            $driver->save();

            return Response::success($driver);
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Driver  $driverStatus
     * @return \Illuminate\Http\Response
     */
    public function show(Driver $driver)
    {
        return Response::success($driver);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Driver $driver)
    {
        $validator = Validator::make($request->all(), [
            "name"  => "required|string"
        ]);
        if ($validator->fails()) return Response::errors($validator);

        $driver->update($validator->validate());
        return Response::success($driver);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Driver $driver)
    {
        return DB::transaction(function () use ($driver) {
            $driver->delete();

            $credential = Credential::find($driver->credential_id);
            $credential->delete();

            return Response::success($driver);
        });
    }
}
