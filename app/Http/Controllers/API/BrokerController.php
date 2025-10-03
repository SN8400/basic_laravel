<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ARGI\Broker;
use App\Models\ARGI\UserFarmer;
use App\Models\ARGI\Crop;

class BrokerController extends Controller
{
    public function index()
    {
        $uid = auth()->user()->user_link_id;
        $crop = Crop::query()->orderByDesc('id')->first();


        $brokerIds = UserFarmer::query()
            ->where('crop_id', $crop->id)
            ->where(function ($q) use ($uid) {
                $q->where('user_id', $uid)
                ->orWhere('manager_id', $uid)
                ->orWhere('review_id', $uid);
            })
            ->whereNotNull('broker_id')
            ->distinct()
            ->orderBy('broker_id')   
            ->pluck('broker_id');    

        $brokers = Broker::whereIn('id', $brokerIds)->get();
        return response()->json([
            'message' => 'Last',
            'data'  => $brokers,
            'uid'  => $uid,
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
