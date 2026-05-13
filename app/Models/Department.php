<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'semester',
        'cost',
    ];

    protected function casts(): array
    {
        return [
            'semester'   => 'integer',
            'cost'       => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }

    // =========================================
    // RELATIONSHIPS
    // =========================================

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // =========================================
    // SCOPES
    // =========================================

    // Urutkan berdasarkan nama lalu semester — berguna untuk dropdown
    public function scopeOrdered($query)
    {
        return $query->orderBy('name')->orderBy('semester');
    }

    public function scopeBySemester($query, int $semester)
    {
        return $query->where('semester', $semester);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    /** Format biaya: Rp 2.500.000 */
    public function getFormattedCostAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->cost, 0, ',', '.');
    }

    /** Label untuk dropdown Filament: "Teknik Informatika — Semester 3" */
    public function getLabelAttribute(): string
    {
        return "{$this->name} — Semester {$this->semester}";
    }
}
