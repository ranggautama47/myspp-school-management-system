<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Student extends Model
{
    protected $fillable = [
        'nis',
        'user_id',
        'classroom_id',
        'department_id',
        'academic_year_id',
        'gender',
        'birth_date',
        'phone',
        'address',
        'parent_name',
        'parent_phone',
        'status'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * BUSINESS LOGIC & OBSERVERS
     */
    protected static function booted()
    {
        static::saving(function ($student) {
            // 1. Auto-Inherit Department & Academic Year dari Classroom
            if ($student->classroom_id) {
                $classroom = Classroom::find($student->classroom_id);
                if ($classroom) {
                    $student->department_id = $classroom->department_id;
                    $student->academic_year_id = $classroom->academic_year_id;
                }
            }

            // 2. Validasi Kapasitas Kelas (Classroom Capacity Check)
            if ($student->isDirty('classroom_id') && $student->status === 'active') {
                $classroom = Classroom::find($student->classroom_id);
                $currentStudentsCount = self::where('classroom_id', $student->classroom_id)
                    ->where('status', 'active')
                    ->where('id', '!=', $student->id)
                    ->count();

                if ($currentStudentsCount >= $classroom->capacity) {
                    throw ValidationException::withMessages([
                        'classroom_id' => "Classroom {$classroom->name} is at maximum capacity ({$classroom->capacity} students)."
                    ]);
                }
            }
        });
    }

    /**
     * RELATIONS
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
