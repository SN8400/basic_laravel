<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupStock;
use App\Models\ARGI\Crop;
use App\Models\ARGI\Broker;

class SupStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sup_stocks = SupStock::with(['broker', 'chemical', 'crop', 'unit'])->get();
        return response()->json([
            'message' => 'List',
            'data'  => $sup_stocks,
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
        $crop = Crop::query()->orderByDesc('id')->first();
        $broker = Broker::query()->where('code', $id)->orderByDesc('id')->first();

        $sup_stocks = SupStock::with(['broker', 'chemical', 'crop', 'unit'])
        ->where('broker_id',$broker->id)
        ->where('crop_id',$crop->id)
        ->get();
        return response()->json([
            'message' => 'List',
            'broker' => $broker->id,
            'crop' => $crop->id,
            'data'  => $sup_stocks,
        ], 200, [], JSON_UNESCAPED_UNICODE);
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
