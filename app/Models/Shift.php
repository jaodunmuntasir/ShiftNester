<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'required_employees',
        'is_published'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function requirements()
    {
        return $this->hasMany(ShiftRequirement::class);
    }

    public function preferences()
    {
        return $this->hasMany(ShiftPreference::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'shift_preferences')
                    ->withPivot('preference_level');
    }

    public function publishedShifts()
    {
        return $this->hasMany(PublishedShift::class);
    }
}