<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ARGI\UserFarmer;
use App\Models\ARGI\Crop;
use App\Models\ARGI\PlanSchedule;
use App\Models\ARGI\Broker;
use App\Models\ARGI\PlanScheduleDetail;
use Illuminate\Support\Facades\DB;

class UserFarmerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->summaryByFarmerCode('0403');
        return response()->json([
            'message' => 'list',
            'data'    => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function summaryByFarmerCode(string $farmer_code)
    {
        
        $crop = Crop::query()->orderByDesc('id')->first();

        $userFarmers = UserFarmer::query()
            ->codePrefix($farmer_code)
            ->where('crop_id', $crop->id)
            ->dedupBy(['crop_id','user_id','manager_id','review_id','broker_id','area_id'])
            ->orderByDesc('created')
            ->get(['broker_id']);

        $brokerIds = $userFarmers->pluck('broker_id')->unique()->values();


        /* 1) นับจำนวน PlanSchedule ต่อ broker */
        $counts = PlanSchedule::query()
            ->when($crop->id,   fn($q) => $q->where('crop_id', $crop->id))
            ->when($brokerIds->isNotEmpty(), fn($q) => $q->whereIn('broker_id', $brokerIds))
            ->groupBy('broker_id')
            ->select('broker_id', DB::raw('COUNT(*) AS plan_schedules'))
            ->get()
            ->keyBy('broker_id'); // => [broker_id => {broker_id, plan_schedules}]

        /* 2) สรุปเคมีต่อ broker: chemical_id + unit_id และ SUM(value) */
        $chem = PlanScheduleDetail::query() // <-- ใช้ connection ของโมเดล (sqlsrv2)
            ->from('dbo.plan_schedule_details as d')
            ->join('dbo.plan_schedules as ps', 'ps.id', '=', 'd.plan_schedule_id')
            ->leftJoin('dbo.chemicals as c', 'c.id', '=', 'd.chemical_id')
            ->leftJoin('dbo.units as u',     'u.id', '=', 'd.unit_id')
            ->when($crop->id, fn($q) => $q->where('ps.crop_id', $crop->id))
            ->when($brokerIds->isNotEmpty(), fn($q) => $q->whereIn('ps.broker_id', $brokerIds))
            ->groupBy('ps.broker_id', 'd.chemical_id', 'd.unit_id', 'c.name', 'u.name')
            ->select([
                'ps.broker_id',
                'd.chemical_id',
                'd.unit_id',
                'c.name as chemical_name',
                'u.name as unit_name',
                DB::raw('SUM(CAST([d].[value] AS decimal(18,4))) AS value'),
            ])
            ->orderBy('ps.broker_id')
            ->get()
            ->groupBy('broker_id')
            ->map(fn($rows) => $rows->map(fn($r) => [
                'chemical_id'   => (int) $r->chemical_id,
                'chemical_name' => $r->chemical_name,
                'unit_id'       => (int) $r->unit_id,
                'unit_name'     => $r->unit_name,
                'value'         => (float) $r->value,
            ])->values());

        /* 3) รวมผลลัพธ์ */
        $allBrokerIds = $counts->keys()->union($chem->keys())->values();

        $result = $allBrokerIds->map(function ($bid) use ($counts, $chem) {
            // แถวของ broker นี้ (อาจเป็น array หรือ object ปะปน)
            $rows = collect($chem->get($bid, collect()));

            $chemRows = $rows->map(function ($r) {
                return [
                    'chemical_id' => (int) data_get($r, 'chemical_id', 0),
                    'value'       => (float) data_get($r, 'value', 0),
                    'unit_id'     => (int) data_get($r, 'unit_id', 0),
                ];
            })->values();

            // กรณี $counts->get($bid) เป็น array ก็ใช้ data_get เช่นกัน
            $planSchedules = (int) data_get($counts->get($bid), 'plan_schedules', 0);
            $broker = Broker::find($bid);
            return [
                'broker_id'      => (int) $bid,
                'broker'      => $broker,
                'plan_schedules' => $planSchedules,
                'chemicals'      => $chemRows, // [{chemical_id, value, unit_id}, ...]
            ];
        });

        return $result;
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
