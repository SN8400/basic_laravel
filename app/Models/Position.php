<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $room_id
 * @property string $pos_name
 * @property string $pos_code
 * @property integer $pos_x
 * @property integer $pos_y
 * @property integer $pos_w
 * @property integer $pos_h
 * @property Room $room
 */
class Position extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['room_id', 'pos_name', 'pos_code', 'pos_x', 'pos_y', 'pos_w', 'pos_h'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }
}
