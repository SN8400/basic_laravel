<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $room_id
 * @property integer $emp_id
 * @property string $pos_code
 * @property Room $room
 * @property Employee $employee
 */
class RoomDefault extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['room_id', 'emp_id', 'pos_code'];

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
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'emp_id');
    }
}
