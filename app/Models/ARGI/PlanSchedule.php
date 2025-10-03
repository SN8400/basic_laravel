<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $crop_id
 * @property integer $input_item_id
 * @property string $code
 * @property string $main_code
 * @property string $name
 * @property string $gapdata
 * @property string $details
 * @property integer $day
 * @property string $created
 * @property string $modified
 * @property integer $area_id
 * @property integer $priority
 * @property integer $broker_id
 * @property string $can_review
 * @property string $can_photo
 */
class PlanSchedule extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['crop_id', 'input_item_id', 'code', 'main_code', 'name', 'gapdata', 'details', 'day', 'created', 'modified', 'area_id', 'priority', 'broker_id', 'can_review', 'can_photo'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';

    public function details()
    {
        return $this->hasMany(PlanScheduleDetail::class, 'plan_schedule_id');
    }

    // รายการสรุปต่อ (chemical_id, unit_id)
    public function detailsAgg()
    {
        return $this->details()
            ->select('plan_schedule_id','chemical_id','unit_id')
            ->selectRaw('SUM(CAST([value] AS decimal(18,4))) AS total_value')
            ->selectRaw('COUNT(*) AS items')
            ->groupBy('plan_schedule_id','chemical_id','unit_id');
    }

    public function chemicalsSummary()
    {
        return $this->details()
            ->select('plan_schedule_id', 'chemical_id', 'unit_id')
            ->selectRaw('SUM(CAST([value] AS decimal(18,4))) AS value')
            ->groupBy('plan_schedule_id', 'chemical_id', 'unit_id');
    }
}
