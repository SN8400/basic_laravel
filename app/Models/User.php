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
    protected $fillable = ['name','username','email','password','remember_token','is_active','user_link_id'];

    protected $hidden = ['password','remember_token'];

    public function getAuthIdentifierName()
    {
        return 'username';
    }
    public function getUserLinkIdAttribute($value)
    {
        return $value ?? env('DEFAULT_USER_ID');
    }
}
