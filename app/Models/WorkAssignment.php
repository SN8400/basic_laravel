<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $work_id
 * @property integer $emp_id
 * @property integer $from_plan_item_id
 * @property integer $room_id
 * @property string $pos_code
 * @property string $current_status
 * @property string $current_status_since
 * @property Employee $employee
 * @property PlanItem $planItem
 * @property Work $work
 * @property WorkEvent[] $workEvents
 * @property WorkSession[] $workSessions
 */
class WorkAssignment extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['work_id', 'emp_id', 'from_plan_item_id', 'room_id', 'pos_code', 'current_status', 'current_status_since'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'emp_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planItem()
    {
        return $this->belongsTo('App\Models\PlanItem', 'from_plan_item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function work()
    {
        return $this->belongsTo('App\Models\Work');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workEvents()
    {
        return $this->hasMany('App\Models\WorkEvent', 'assignment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workSessions()
    {
        return $this->hasMany('App\Models\WorkSession', 'assignment_id');
    }
}
