<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $room_id
 * @property integer $plan_id
 * @property string $work_date
 * @property string $start_time
 * @property string $end_time
 * @property string $work_status
 * @property Room $room
 * @property Plan $plan
 * @property WorkAssignment[] $workAssignments
 * @property WorkEvent[] $workEvents
 */
class Work extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['room_id', 'plan_id', 'work_date', 'start_time', 'end_time', 'work_status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo('App\Models\Room');
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
        return $this->hasMany('App\Models\WorkAssignment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workEvents()
    {
        return $this->hasMany('App\Models\WorkEvent');
    }
}
