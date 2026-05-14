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
    protected static function booted()
    {
        static::saving(function ($academicYear) {
            if ($academicYear->is_active) {
                // Deactivate all other years
                static::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            }
        });
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
