<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $emp_code
 * @property string $full_name
 * @property EmployeeExternalMap[] $employeeExternalMaps
 * @property RoomDefault[] $roomDefaults
 * @property PlanItem[] $planItems
 * @property WorkAssignment[] $workAssignments
 * @property WorkEvent[] $workEvents
 */
class Employee extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['emp_code', 'full_name'];
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeExternalMaps()
    {
        return $this->hasMany('App\Models\EmployeeExternalMap', 'internal_emp_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomDefaults()
    {
        return $this->hasMany('App\Models\RoomDefault', 'emp_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function planItems()
    {
        return $this->hasMany('App\Models\PlanItem', 'emp_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workAssignments()
    {
        return $this->hasMany('App\Models\WorkAssignment', 'emp_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workEvents()
    {
        return $this->hasMany('App\Models\WorkEvent', 'emp_id');
    }
}
