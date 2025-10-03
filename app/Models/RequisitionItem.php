<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $requisition_id
 * @property integer $stock_id
 * @property float $qty_requested
 * @property float $qty_approved
 * @property string $remark
 */
class RequisitionItem extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'requisition_id', 
        'stock_id', 
        'qty_requested', 
        'qty_approved', 
        'remark'
    ];
    public $timestamps = false;

    public function requisition()    { return $this->belongsTo(Requisition::class, 'requisition_id'); }
    public function stock()  { return $this->belongsTo(SupStock::class, 'stock_id'); }

}
