<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $standard_name
 * @property string $details
 * @property string $chemical_type
 * @property string $MRLs
 * @property string $major_type
 * @property string $type_code
 * @property string $created
 * @property string $modified
 * @property integer $rate
 */
class StandardCode extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'standard_code';

    /**
     * @var array
     */
    protected $fillable = ['standard_name', 'details', 'chemical_type', 'MRLs', 'major_type', 'type_code', 'created', 'modified', 'rate'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';
}
