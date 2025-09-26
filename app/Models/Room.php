<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $image_path
 * @property string $description
 * @property Position[] $positions
 * @property RoomDefault[] $roomDefaults
 * @property Plan[] $plans
 * @property Work[] $works
 */
class Room extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'image_path', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function positions()
    {
        return $this->hasMany('App\Models\Position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomDefaults()
    {
        return $this->hasMany('App\Models\RoomDefault');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plans()
    {
        return $this->hasMany('App\Models\Plan');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function works()
    {
        return $this->hasMany('App\Models\Work');
    }
}
