<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $stock_id
 * @property string $inventory_type
 * @property float $amount
 * @property integer $unit_id
 * @property string $remark
 */
class SupInventory extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['stock_id', 'inventory_type', 'amount', 'unit_id', 'remark'];
}
