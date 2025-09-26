<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $work_id
 * @property integer $assignment_id
 * @property integer $emp_id
 * @property string $pos_code
 * @property string $status
 * @property string $source
 * @property string $event_time
 * @property string $received_at
 * @property string $idempotency_key
 * @property string $payload
 * @property Employee $employee
 * @property Work $work
 * @property WorkAssignment $workAssignment
 */
class WorkEvent extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['work_id', 'assignment_id', 'emp_id', 'pos_code', 'status', 'source', 'event_time', 'received_at', 'idempotency_key', 'payload'];

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
    public function work()
    {
        return $this->belongsTo('App\Models\Work');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workAssignment()
    {
        return $this->belongsTo('App\Models\WorkAssignment', 'assignment_id');
    }
}
