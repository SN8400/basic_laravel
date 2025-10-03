<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ARGI\PlanSchedule;


class PlanScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $planSchedules = PlanSchedule::all();
        $planSchedules = PlanSchedule::where('crop_id', '27')->get();
        return response()->json([
            'message' => 'List',
            'data'  => $planSchedules,
        ], 200, [], JSON_UNESCAPED_UNICODE);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
