<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $details
 * @property string $created
 * @property string $modified
 * @property string $formula_code
 * @property integer $standard_code_id
 * @property integer $unit_id
 * @property float $rate_per_land
 * @property integer $bigunit_id
 * @property float $package_per_bigunit
 * @property string $ctype
 */
class Chemical extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['code', 'name', 'details', 'created', 'modified', 'formula_code', 'standard_code_id', 'unit_id', 'rate_per_land', 'bigunit_id', 'package_per_bigunit', 'ctype'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function standard_code()
    {
        return $this->belongsTo(StandardCode::class, 'standard_code_id', 'id');
    }
}
