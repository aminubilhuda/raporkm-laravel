<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'jabatan', 'nama', 'ptk_id', 'username', 'email', 'password',
    'kontak', 'id_tugas_tambahan', 'foto', 'moto', 'fcm_token', 'device_name',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    protected $table = 'users';

    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    public function findForPassport(string $username): self
    {
        return $this->where('username', $username)->first();
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthNameAttribute(): string
    {
        return $this->nama;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isTU(): bool
    {
        return $this->hasRole('TU') || $this->jabatan === 2;
    }

    public function isGuru(): bool
    {
        return $this->hasRole('Guru') || $this->jabatan === 3;
    }

    public function isKepsek(): bool
    {
        return $this->hasRole('Kepsek') || $this->jabatan === 4;
    }

    public function canAccess(string $permission): bool
    {
        return $this->can($permission);
    }

    public function getRoleName(): ?string
    {
        return $this->roles->first()?->name ?? match ($this->jabatan) {
            2 => 'TU',
            3 => 'Guru',
            4 => 'Kepsek',
            default => null,
        };
    }

    public function ptk()
    {
        return $this->hasOne(Ptk::class);
    }

    public function kelasWali()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_wali')
            ->withPivot('tahun_pelajaran_id', 'semester_id')
            ->withTimestamps();
    }

    public function mapelKelas()
    {
        return $this->hasMany(MapelKelas::class);
    }

    public function pembinaEskul()
    {
        return $this->belongsToMany(Eskul::class, 'pembina_eskul')
            ->withPivot('tahun_pelajaran_id')
            ->withTimestamps();
    }

    public function piketHarian()
    {
        return $this->hasMany(PiketHarian::class);
    }

    public function catatanWali()
    {
        return $this->hasMany(CatatanWali::class);
    }

    public function kepalaSekolah()
    {
        return $this->hasMany(KepalaSekolah::class);
    }

    public function pwaTokens()
    {
        return $this->hasMany(PwaToken::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function rememberTokens()
    {
        return $this->hasMany(RememberToken::class);
    }
}
