<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $details
 * @property string $sap_code
 * @property string $startdate
 * @property string $enddate
 * @property string $linkurl
 * @property integer $createdBy
 * @property integer $modifiedBy
 * @property string $created
 * @property string $modified
 * @property float $max_per_day
 */
class Crop extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'details', 'sap_code', 'startdate', 'enddate', 'linkurl', 'createdBy', 'modifiedBy', 'created', 'modified', 'max_per_day'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';
}
