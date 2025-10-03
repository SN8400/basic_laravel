<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\ARGI\Crop;
use App\Models\ARGI\Broker;
use Illuminate\Support\Str;

class RequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requisitions = Requisition::with(['crop','broker'])->get();
        return response()->json([
            'message' => 'List',
            'data'  => $requisitions,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            
            $validated = $request->validate([
                'inventory_status'   => ['required','string','max:10'],
                'request_by'   => ['required','string','max:10'],
                'userId'   => ['required','integer'],
                'items' => 'required|array'
            ]);

            $crop = Crop::query()->orderByDesc('id')->first();
            $broker = Broker::query()->where('code', $validated['request_by'])->orderByDesc('id')->first();

            $docCode = 'INV-'.strtoupper(Str::random(4)).date('Ymd');  
            $requisition = Requisition::create([
                'crop_id'      => $crop->id,
                'broker_id'      => $broker->id,
                'inventory_code'      => $docCode,
                'inventory_type'      => 'เบิก',
                'inventory_status'    => $validated['inventory_status'],
                'request_date'      => now()->toDateString(),
                'request_by'      => $validated['userId'] 
            ]);

            foreach ($validated['items'] as $item) {
                $plan = RequisitionItem::create([
                    'requisition_id'  => $requisition->id,
                    'stock_id'  => $item['product_id'],
                    'qty_requested' => $item['qty_requested']
                ]);
            }

            return response()->json([
                'message' => 'Created',
                'data'    => $requisition,
            ], 201, [], JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Create failed',
                'error'   => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $requisitions = Requisition::with(['crop','broker'])->find($id)->get();
        return response()->json([
            'message' => 'List',
            'data'  => $requisitions,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $requisition = Requisition::find($id);
            $validated = $request->validate([
                'inventory_status'   => ['required','string','max:10'],
                'userId'   => ['required','integer'],
            ]);

            $requisition->inventory_status = $validated['inventory_status'] ?? $requisition->inventory_status;
            $requisition->approved_date = now()->toDateString();
            $requisition->approved_by = $validated['userId']; 
            $requisition->save();

            return response()->json([
                'message' => 'Update',
                'data'    => $requisition,
            ], 201, [], JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Create failed',
                'error'   => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function getByBroker(string $id)
    {
        $crop = Crop::query()->orderByDesc('id')->first();
        $broker = Broker::query()->where('code', $id)->orderByDesc('id')->first();

        $requisitions = Requisition::with(['crop','broker'])->where('broker_id',$broker->id)
        ->where('crop_id',$crop->id)
        ->orderByDesc('id')
        ->get();
        return response()->json([
            'message' => 'List',
            'data'  => $requisitions,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
