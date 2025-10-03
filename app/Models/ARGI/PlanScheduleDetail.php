<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $plan_schedule_id
 * @property integer $chemical_id
 * @property float $value
 * @property integer $unit_id
 * @property float $p_value
 * @property integer $p_unit_id
 * @property string $created
 * @property string $modified
 * @property string $name
 * @property integer $rate
 * @property string $ctype
 * @property integer $set_group
 */
class PlanScheduleDetail extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['plan_schedule_id', 'chemical_id', 'value', 'unit_id', 'p_value', 'p_unit_id', 'created', 'modified', 'name', 'rate', 'ctype', 'set_group'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';

    
    protected $casts = [
        'value' => 'float', // เผื่อ value เก็บเป็นตัวเลข
    ];

    public function chemical()
    {
        return $this->belongsTo(Chemical::class, 'chemical_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function pUnit()
    {
        return $this->belongsTo(Unit::class, 'p_unit_id', 'id');
    }
}
