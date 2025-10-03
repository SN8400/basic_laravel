<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $code
 * @property string $init
 * @property string $fname
 * @property string $lname
 * @property string $citizenid
 * @property string $address1
 * @property string $address2
 * @property string $address3
 * @property string $sub_cities
 * @property integer $city_id
 * @property integer $province_id
 * @property string $loc
 * @property string $broker_color
 * @property integer $createdBy
 * @property integer $modifiedBy
 * @property string $created
 * @property string $modified
 */
class Broker extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'init', 'fname', 'lname', 'citizenid', 'address1', 'address2', 'address3', 'sub_cities', 'city_id', 'province_id', 'loc', 'broker_color', 'createdBy', 'modifiedBy', 'created', 'modified'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';
}
