<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRequirement extends Model
{
    use HasFactory;

    protected $fillable = ['shift_id', 'department_id', 'designation_id', 'employee_count'];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
