<?php

namespace App\Http\Controllers;

use App\Http\Response;
use App\Models\Driver;
use App\Models\DriverStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DriverStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "filter"    => Rule::in(["theLastSevenData"])
        ]);
        if ($validator->fails()) return Response::errors($validator);

        $result = DriverStatus::with("driver");
        switch ($request->filter) {
            case "theLastSevenData":
                $result->orderBy("id", "desc")->limit(7);
                break;
        }

        return Response::success($result->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function getOne(Driver $driver)
    {
        $result = Driver::with("status")->find($driver->id);
        return Response::success($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Driver $driver)
    {
        // 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DriverStatus  $driverStatus
     * @return \Illuminate\Http\Response
     */
    public function show(DriverStatus $driverStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DriverStatus  $driverStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DriverStatus $driverStatus)
    {
        // 
    }

    public function upsert(Request $request, Driver $driver)
    {
        $validator = Validator::make($request->all(), [
            "blink_count"   => "required|integer",
            "confidence"    => "required|numeric",
            "eye_status"    => [
                "required",
                Rule::in(["Terbuka", "Tertutup"])
            ],
            "state_status"  => [
                "required",
                Rule::in(["Lelah", "Normal"])
            ]
        ]);
        if ($validator->fails()) return Response::errors($validator);

        $data = $validator->validate();
        $data["session"] = $driver->session;
        $data["driver_id"] = $driver->id;

        $driverStatus = DriverStatus::where("driver_id", $driver->id)
            ->where("session", $driver->session)->orderBy("id", "desc")
            ->first();

        if (!$driverStatus) {
            $driverStatus = new DriverStatus($data);
            $driverStatus->save();

            return Response::success($driverStatus);
        }

        if ((int) $request->blink_count > (int) $driverStatus->blink_count) {
            $driverStatus->blink_count = $request->blink_count;
            $driverStatus->save();

            return Response::success($driverStatus);
        }

        if ((int) $request->blink_count === 0) {
            $driver->session += 1;
            $driver->save();

            $driverStatus = new DriverStatus($data);
            $driverStatus->session = $driver->session;
            $driverStatus->save();

            return Response::success($driverStatus);
        }

        return Response::success($driverStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DriverStatus  $driverStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(DriverStatus $driverStatus)
    {
        //
    }
}
