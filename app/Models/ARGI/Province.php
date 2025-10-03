<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $th_name
 * @property string $en_name
 * @property string $created
 * @property string $modified
 */
class Province extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['th_name', 'en_name', 'created', 'modified'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';
}
