<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $tradename
 * @property string $common_name
 * @property float $size
 * @property integer $unit_id
 * @property string $pur_of_use
 * @property string $RM_Group
 * @property string $created
 * @property string $modified
 */
class InputItem extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'code', 'tradename', 'common_name', 'size', 'unit_id', 'pur_of_use', 'RM_Group', 'created', 'modified'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';
}
