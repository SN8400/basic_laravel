<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $room_id
 * @property string $job_name
 * @property string $job_desc
 * @property boolean $is_active
 * @property Room $room
 * @property PlanItem[] $planItems
 * @property Work[] $works
 */
class Plan extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['room_id', 'job_name', 'job_desc', 'is_active'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo('App\Models\Room');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planItems()
    {
        return $this->hasMany('App\Models\PlanItem');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function works()
    {
        return $this->hasMany('App\Models\Work');
    }
}
