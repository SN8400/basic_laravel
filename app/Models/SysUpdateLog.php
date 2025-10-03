<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $sys_name
 * @property string $sys_status
 * @property string $created_at
 * @property string $updated_at
 */
class SysUpdateLog extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['sys_name', 'sys_status', 'created_at', 'updated_at'];
    public $timestamps = false;
}
