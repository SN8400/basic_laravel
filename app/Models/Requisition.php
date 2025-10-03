<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $crop_id
 * @property integer $broker_id
 * @property integer $stock_id
 * @property string $inventory_code
 * @property string $inventory_type
 * @property string $inventory_status
 * @property string $request_date
 * @property integer $request_by
 * @property string $approved_date
 * @property integer $approved_by
 * @property string $created
 * @property string $modified
 */
class Requisition extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'crop_id', 
        'broker_id', 
        'inventory_code', 
        'inventory_type', 
        'inventory_status', 
        'request_date', 
        'request_by', 
        'approved_date', 
        'approved_by', 
        'created', 
        'modified'
    ];
    public $timestamps = false;
    protected $appends = ['broker_name'];

    public function crop()    { return $this->belongsTo(ARGI\Crop::class, 'crop_id'); }
    public function broker()  { return $this->belongsTo(ARGI\Broker::class, 'broker_id'); }
    public function getBrokerNameAttribute()
    {
        if (!$this->broker) {
            return null; // ป้องกัน error เวลาไม่มี broker
        }

        $init = $this->broker->init === '-' ? '' : $this->broker->init;
        return "{$init} {$this->broker->fname} {$this->broker->lname}";
    }

}
