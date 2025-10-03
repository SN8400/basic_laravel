<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $detail
 * @property string $created
 * @property string $modified
 */
class Unit extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'detail', 'created', 'modified'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';
}
