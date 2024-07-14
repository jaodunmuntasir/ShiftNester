<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_boolean'];

    protected $casts = [
        'is_boolean' => 'boolean',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class)->withPivot('rating', 'has_skill')->withTimestamps();
    }
}
