<?php

namespace App\Models\ARGI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property integer $id
 * @property integer $crop_id
 * @property integer $user_id
 * @property integer $manager_id
 * @property integer $review_id
 * @property integer $broker_id
 * @property integer $area_id
 * @property integer $farmer_id
 * @property integer $head_id
 * @property string $sowing_city
 * @property string $farmer_code
 * @property string $status
 * @property integer $createdBy
 * @property integer $modifiedBy
 * @property string $created
 * @property string $modified
 * @property string $custom1
 * @property string $custom2
 * @property string $custom3
 * @property string $custom4
 * @property string $custom5
 */
class UserFarmer extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'user_farmer';

    /**
     * @var array
     */
    protected $fillable = [
        'crop_id', 
        'user_id', 
        'manager_id', 
        'review_id', 
        'broker_id', 
        'area_id', 
        'farmer_id', 
        'head_id', 
        'sowing_city', 
        'farmer_code', 
        'status', 
        'createdBy', 
        'modifiedBy', 
        'created', 
        'modified', 
        'custom1', 
        'custom2', 
        'custom3', 
        'custom4', 
        'custom5', 
        'code_first', 
        'code_last'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv2';

    public function scopeByUserId(string $userId)
    {
        $uid = (int) $userId; // หรือ auth()->id()

        $brokerIds = UserFarmer::query()
            ->where(function ($q) use ($uid) {
                $q->where('user_id', $uid)
                ->orWhere('manager_id', $uid)
                ->orWhere('review_id', $uid);
            })
            ->whereNotNull('broker_id')
            ->distinct()
            ->orderBy('broker_id')   
            ->pluck('broker_id');    

        return $brokerIds;
    }


    public function scopeCodePrefix($q, string $code)
    {
        return $q->where('farmer_code', 'like', $code.'-%');
    }

    public function scopeDedupBy(Builder $q, array $cols, string $orderCol = 'id', string $dir = 'DESC'): Builder
    {
        $table = $this->getTable();
        $part  = implode(', ', array_map(fn($c) => "$table.$c", $cols));

        // สร้างซับคิวรีหา id ที่ชนะต่อกลุ่มด้วย ROW_NUMBER()
        $base = $this->newQuery()
            ->from($table)
            ->select("$table.id")
            ->selectRaw("ROW_NUMBER() OVER (PARTITION BY $part ORDER BY $table.$orderCol $dir) AS rn");

        // join กลับตารางหลักเพื่อได้ทุกคอลัมน์ของแถวที่ rn=1
        return $q->from("$table")
            ->joinSub($base, 'g', "g.id", '=', "$table.id")
            ->where('g.rn', 1)
            ->select("$table.*");
    }

}
