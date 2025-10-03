<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $role_id
 */
class UserRole extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'role_id'];
}
