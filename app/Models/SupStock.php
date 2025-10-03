<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $crop_id
 * @property integer $broker_id
 * @property integer $chemical_id
 * @property float $value
 * @property integer $unit_id
 */
class SupStock extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['crop_id', 'broker_id', 'chemical_id', 'value', 'unit_id'];
    public $timestamps = false;


    public function crop()    { return $this->belongsTo(ARGI\Crop::class, 'crop_id'); }
    public function broker()  { return $this->belongsTo(ARGI\Broker::class, 'broker_id'); }
    public function chemical(){ return $this->belongsTo(ARGI\Chemical::class, 'chemical_id'); }
    public function unit()    { return $this->belongsTo(ARGI\Unit::class, 'unit_id'); }
}
