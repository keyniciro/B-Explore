<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property int|null $tenant_id
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
// role & tenant_id SENGAJA tidak fillable: nilainya hanya boleh diisi server, bukan input pengguna.
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    public const ROLE_WISATAWAN = 'wisatawan';

    public const ROLE_MITRA = 'mitra';

    public const ROLE_ADMIN = 'admin';

    /**
     * Nilai bawaan model baru. Kolom DB-nya default 'customer', jadi tanpa ini user baru
     * yang dibuat lewat factory/seeder akan punya role yang tidak dikenal aplikasi.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_WISATAWAN,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** @return BelongsTo<Mitra, $this> */
    public function mitra(): BelongsTo
    {
        return $this->belongsTo(Mitra::class, 'tenant_id');
    }

    /**
     * Status akun secara umum. Tabel users belum punya kolom status, jadi semua akun aktif.
     * (Status MITRA dicek terpisah di isMitraAktif(), supaya mitra yang dinonaktifkan
     * tetap bisa memakai portal wisatawan.)
     */
    public function isActive(): bool
    {
        return true;
    }

    /**
     * Punya mitra yang berstatus bukan "nonaktif"? (kunci akses portal mitra)
     */
    public function isMitraAktif(): bool
    {
        return $this->role === self::ROLE_MITRA
            && $this->mitra !== null
            && $this->mitra->status !== 'nonaktif';
    }

    /**
     * Cek hak akses. Role MITRA = WISATAWAN + hak kelola mitranya sendiri (hierarki):
     *
     *   wisatawan -> dipenuhi oleh role wisatawan DAN mitra (mitra tetap bisa belanja/checkout)
     *   mitra     -> hanya role mitra yang tenant-nya aktif
     *   admin     -> hanya role admin
     *
     * Sengaja bukan sekadar membandingkan kolom role: status akun/tenant ikut diperiksa.
     */
    public function hasRole(string ...$roles): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        foreach ($roles as $role) {
            $granted = match ($role) {
                self::ROLE_WISATAWAN => in_array($this->role, [self::ROLE_WISATAWAN, self::ROLE_MITRA], true),
                self::ROLE_MITRA => $this->isMitraAktif(),
                default => $this->role === $role,
            };

            if ($granted) {
                return true;
            }
        }

        return false;
    }

    /**
     * Beranda setelah login / saat pengguna login membuka halaman tamu. Mitra juga mendarat
     * di dashboard wisatawan; dashboard mitra dibuka lewat menu "Kelola Mitra".
     */
    public function homeUrl(): string
    {
        return match ($this->role) {
            self::ROLE_MITRA, self::ROLE_WISATAWAN => route('dashboard'),
            default => route('home'),
        };
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
