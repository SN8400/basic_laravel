<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $plan_id
 * @property integer $emp_id
 * @property string $pos_code
 * @property Employee $employee
 * @property Plan $plan
 * @property WorkAssignment[] $workAssignments
 */
class PlanItem extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['plan_id', 'emp_id', 'pos_code'];

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
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workAssignments()
    {
        return $this->hasMany('App\Models\WorkAssignment', 'from_plan_item_id');
    }
}
