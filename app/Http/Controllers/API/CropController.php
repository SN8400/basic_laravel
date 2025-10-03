<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ARGI\Crop;
use App\Http\Requests\StoreCropRequest;
use App\Http\Requests\UpdateCropRequest;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $crop = Crop::query()->orderByDesc('id')->first();
        return response()->json([
            'message' => 'Last',
            'data'  => $crop,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCropRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Crop $crop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCropRequest $request, Crop $crop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crop $crop)
    {
        //
    }
}
