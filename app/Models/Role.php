<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $created_at
 * @property string $updated_at
 * @property RoleUser[] $roleUsers
 */
class Role extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name','slug'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
     public function users()
    {
        return $this->belongsToMany(
            User::class,
            'role_users',
            'role_id',
            'user_id'
        );
    }
    
}
