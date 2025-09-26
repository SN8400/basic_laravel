<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $assignment_id
 * @property string $status
 * @property string $started_at
 * @property string $ended_at
 * @property WorkAssignment $workAssignment
 */
class WorkSession extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['assignment_id', 'status', 'started_at', 'ended_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workAssignment()
    {
        return $this->belongsTo('App\Models\WorkAssignment', 'assignment_id');
    }
}
