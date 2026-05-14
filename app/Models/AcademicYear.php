<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('end_date', '>=', now());
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
