<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $internal_emp_id
 * @property string $source
 * @property string $external_emp_id
 * @property Employee $employee
 */
class EmployeeExternalMap extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['internal_emp_id', 'source', 'external_emp_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'internal_emp_id');
    }
}
