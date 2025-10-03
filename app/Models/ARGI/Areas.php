<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $created
 * @property string $modified
 */
class Areas extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'name', 'created', 'modified'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';
}
