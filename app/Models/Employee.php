<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'department_id', 'designation_id', 'hire_date', 'user_id'];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class)->withPivot('rating', 'has_skill')->withTimestamps();
    }

    public function shiftPreferences()
    {
        return $this->hasMany(ShiftPreference::class);
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'shift_preferences')
                    ->withPivot('preference_level');
    }
}
