<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'image',
        'scan_ijazah',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'deleted_at'        => 'datetime',
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

    public function scopeAdmins($query)
    {
        return $query->role(UserRole::Admin->value);
    }

    public function scopeStudents($query)
    {
        return $query->role(UserRole::Student->value);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    /**
     * URL gambar profil — compatible dengan Cloudinary & local storage.
     * Gunakan Storage::url() bukan asset() agar bisa ganti driver kapan saja.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? Storage::url($this->image)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=534AB7&color=fff';
    }

    // =========================================
    // HELPERS
    // =========================================

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin->value);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(UserRole::Student->value);
    }

    /**
     * Reuse relationship utama + scope pending.
     * Tidak pakai HasMany baru agar eager loading tetap konsisten.
     */
    public function unpaidTransactions()
    {
        return $this->transactions()->pending();
    }

    public function paidTransactions()
    {
        return $this->transactions()->paid();
    }
}
