<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $province_id
 * @property string $th_name
 * @property string $en_name
 * @property string $created
 * @property string $modified
 */
class City extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['province_id', 'th_name', 'en_name', 'created', 'modified'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';
}
