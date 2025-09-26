<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property boolean $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property RoleUser[] $roleUsers
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    /**
     * @var array
     */
    protected $fillable = ['name','username','email','password','remember_token','is_active'];

    protected $hidden = ['password','remember_token'];

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_users', // pivot
            'user_id',                  // FK ของ User ใน pivot
            'role_id'                   // FK ของ Role ใน pivot
        );
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    // เช็คสิทธิ์แบบ lazy (ไม่ต้องพึ่งโหลดล่วงหน้า)
    public function hasRole($slugOrArray): bool
    {
        $need = (array) $slugOrArray;
        return $this->roles()->whereIn('slug', $need)->exists();
    }
}
