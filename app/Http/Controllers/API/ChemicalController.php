<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ARGI\Chemical;
use App\Models\ARGI\Crop;
use App\Models\ARGI\UserFarmer;
use App\Models\ARGI\PlanSchedule;
use App\Models\ARGI\PlanScheduleDetail;
use App\Models\ARGI\Broker;
use Illuminate\Support\Facades\DB;

class ChemicalController extends Controller
{
    public function index()
    {
        $chemicals = Chemical::with(['unit','standard_code'])->get();
        return response()->json([
            'message' => 'Last',
            'data'  => $chemicals,
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
 
        $result = $this->summaryByFarmerCode($id);
        return response()->json([
            'message' => 'list',
            'data'    => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


public function summaryByFarmerCode(string $farmer_code)
{
    $crop = Crop::query()->latest('id')->first();
    if (!$crop) {
        return collect(); // ไม่มี crop ก็คืนคอลเลกชันว่างไปเลย
    }

    // 1) ดึง broker_id ที่เกี่ยวข้องแบบไม่ซ้ำ
    $brokerIds = UserFarmer::query()
        ->codePrefix($farmer_code)
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

        return [
            'broker_id'       => (int) $bid,
            'broker'          => $brokers->get((int)$bid), // ✅ ใช้ที่โหลดไว้แล้ว
            'plan_schedules'  => (int) data_get($counts->get($bid), 'plan_schedules', 0),
            'chemicals'       => $chemRows,
        ];
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
