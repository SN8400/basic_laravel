<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SysUpdateLog;
use App\Models\SupStock;
use App\Models\ARGI\UserFarmer;
use App\Models\ARGI\Crop;
use App\Models\ARGI\PlanSchedule;
use App\Models\ARGI\Broker;
use App\Models\ARGI\PlanScheduleDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SysUpdateLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sys_logs = SysUpdateLog::all();
        return response()->json([
            'message' => 'List',
            'data'  => $sys_logs,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'sys_name'        => ['required','string','max:50'],
            ]);

            $sys_log = SysUpdateLog::create([
                'sys_name'      => $data['sys_name'],
                'sys_status'    => 'Waitting',
            ]);

            return response()->json([
                'message' => 'Created',
                'data'    => $sys_log,
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
        return $this->updateStock($id);
        // try {
        //     $sys_log = SysUpdateLog::find($id);
        //     if (!$sys_log) {
        //         return response()->json(['message' => 'Not found', 'data' => null], 404, [], JSON_UNESCAPED_UNICODE);
        //     }

        //     return response()->json([
        //         'message' => 'Detail',
        //         'data'    => $sys_log,
        //     ], 200, [], JSON_UNESCAPED_UNICODE);

        // } catch (Throwable $e) {
        //     return response()->json([
        //         'message' => 'Fetch failed',
        //         'error'   => $e->getMessage(),
        //     ], 500, [], JSON_UNESCAPED_UNICODE);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $sys_log = SysUpdateLog::find($id);
            if (!$sys_log) {
                return response()->json(['message' => 'Not found', 'data'=>null], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $data = $request->validate([
                'sys_name'      => ['required','string','max:50'],
                'sys_status'    => ['required','string','max:10'],
            ]);


            $sys_log->sys_name = $data['sys_name'] ?? $sys_log->sys_name;
            $sys_log->sys_status =  $data['sys_status'] ?? $sys_log->sys_status;
            $sys_log->save();

            return response()->json([
                'message' => 'Updated',
                'data'    => $sys_log,
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422, [], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Update failed',
                'error'   => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $sys_log = SysUpdateLog::find($id);
            if (!$sys_log) {
                return response()->json(['message' => 'Not found', 'data'=>null], 404, [], JSON_UNESCAPED_UNICODE);
            }
            $sys_log->delete();

            return response()->json(['message' => 'Deleted', 'data'=>null], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'ไม่สามารถลบได้เนื่องจากถูกใช้งานให้จุดอื่นๆอยู่',
                // 'message'   => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    
    public function updateStock(string $id)
    {
        try {
            
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

            foreach($brokers as $broker){
                            
                // 1) ดึง broker_id ที่เกี่ยวข้องแบบไม่ซ้ำ
                $brokerIds = UserFarmer::query()
                    ->codePrefix($broker->code)
                    ->where('crop_id', $crop->id)
                    // ถ้าต้องการเดดุปแถว ให้คง dedupBy ได้ แต่เอา broker_id แบบไม่ซ้ำ ใช้ distinct ก็พอ
                    ->whereNotNull('broker_id')
                    ->distinct()
                    ->orderBy('broker_id')
                    ->pluck('broker_id');   // => Collection<int>

                // เตรียม broker ล่วงหน้า กัน N+1
                $brokers = Broker::query()
                    ->whereIn('id', $brokerIds)
                    ->get()
                    ->keyBy('id'); // [id => Broker]

                // 2) นับจำนวน PlanSchedule ต่อ broker
                $counts = PlanSchedule::query()
                    ->when($crop->id, fn($q) => $q->where('crop_id', $crop->id))
                    ->when($brokerIds->isNotEmpty(), fn($q) => $q->whereIn('broker_id', $brokerIds))
                    ->groupBy('broker_id')
                    ->select('broker_id', DB::raw('COUNT(*) AS plan_schedules'))
                    ->get()
                    ->keyBy('broker_id'); // [broker_id => {broker_id, plan_schedules}]

                // 3) สรุปเคมีต่อ broker
                $chem = PlanScheduleDetail::query() // ใช้คอนเนคชันของโมเดลนี้ (เช่น sqlsrv2)
                    ->from('dbo.plan_schedule_details as d')
                    ->join('dbo.plan_schedules as ps', 'ps.id', '=', 'd.plan_schedule_id')
                    ->leftJoin('dbo.chemicals as c', 'c.id', '=', 'd.chemical_id')
                    ->leftJoin('dbo.units as      u', 'u.id', '=', 'd.unit_id')
                    ->leftJoin('dbo.standard_code as sc', 'sc.id', '=', 'c.standard_code_id')
                    ->when($crop->id, fn($q) => $q->where('ps.crop_id', $crop->id))
                    ->when($brokerIds->isNotEmpty(), fn($q) => $q->whereIn('ps.broker_id', $brokerIds))
                    ->groupBy(
                        'ps.broker_id',
                        'd.chemical_id',
                        'd.unit_id',
                        'c.name',
                        'c.code',
                        'u.name',
                        'sc.standard_name' // ✅ ต้องใส่ใน GROUP BY เพราะอยู่ใน select
                    )
                    ->select([
                        'ps.broker_id',
                        'd.chemical_id',
                        'd.unit_id',
                        'c.name as chemical_name',
                        'c.code as chemical_code',
                        'u.name as unit_name',
                        'sc.standard_name as standard_code',
                        DB::raw('SUM(CAST([d].[value] AS decimal(18,4))) AS value'),
                    ])
                    ->orderBy('ps.broker_id')
                    ->get()
                    ->groupBy('broker_id')
                    ->map(fn($rows) => $rows->map(fn($r) => [
                        'chemical_id'   => (int)   $r->chemical_id,
                        'chemical_name' => (string)$r->chemical_name,
                        'chemical_code' => (string)$r->chemical_code,
                        'unit_id'       => (int)   $r->unit_id,
                        'unit_name'     => (string)$r->unit_name,
                        'value'         => (float) $r->value,
                        'standard_code' => (string)$r->standard_code, // ✅ เก็บเป็น string
                    ])->values());

                // 4) รวมผลลัพธ์ (รวม key เผื่อบาง broker มีอย่างใดอย่างหนึ่ง)
                $allBrokerIds = $counts->keys()->union($chem->keys())->values();
                $bid = $allBrokerIds->first();
                $chemRows = collect($chem->get($bid, collect()))
                    ->map(fn($r) => [
                        'chemical_id'   => (int)   data_get($r, 'chemical_id', 0),
                        'value'         => (float) data_get($r, 'value', 0),
                        'unit_id'       => (int)   data_get($r, 'unit_id', 0),
                        'chemical_name' => (string) data_get($r, 'chemical_name', null),
                        'unit_name'     => (string) data_get($r, 'unit_name', null),
                        'standard_code' => (string) data_get($r, 'standard_code', null),
                        'chemical_code' => (string) data_get($r, 'chemical_code', null),
                    ])->values();

                
                        
                foreach($chemRows as $chemRow){
                    $sup_stock = SupStock::find($chemRow['chemical_id']);
                    if (!$sup_stock) {
                        $sup_stock = SupStock::create([
                            'crop_id'       => $crop->id,
                            'broker_id'     => $bid,
                            'chemical_id'   => $chemRow['chemical_id'],
                            'value'         => $chemRow['value'],
                            'unit_id'       => $chemRow['unit_id'],
                        ]);
                    }
                    else{
                        // Log::info('chemRow', [
                        //     'chemRow' => $chemRow,
                        // ]);
                        // Log::info('chemRow', [
                        //     'chemRow' => $chemRow->crop_id,
                        // ]);
                        // Log::info('chemRow', [
                        //     'chemRow' => $chemRow['crop_id'],
                        // ]);
                        $sup_stock->crop_id = $crop->id != $sup_stock->crop_id ? $crop->id : $sup_stock->crop_id;
                        $sup_stock->broker_id = $bid != $sup_stock->broker_id ? $bid : $sup_stock->broker_id;
                        $sup_stock->chemical_id = $chemRow['chemical_id'] != $sup_stock->chemical_id ? $chemRow['chemical_id'] : $sup_stock->chemical_id;
                        $sup_stock->value = $chemRow['value'] != $sup_stock->value ? $chemRow['value'] : $sup_stock->value;
                        $sup_stock->unit_id = $chemRow['unit_id'] != $sup_stock->unit_id ? $chemRow['unit_id'] : $sup_stock->unit_id;
                        $sup_stock->save();
                    }
                }
            }
            $sys_log = SysUpdateLog::find($id);
            $sys_log->updated_at =  now();
            $sys_log->save();
            return response()->json(['message' => 'update success', 'data'=>null], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'เกิดข้อผิดพลาด กรุณาตรวจสอบ' . $e->getMessage(),
                // 'message'   => $e->getMessage(),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

}
