<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'role',
        'phone',
        'password',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'pimpinan';
    }

    public function isMarketing(): bool
    {
        return $this->role === 'marketing';
    }

    public function isNasabah(): bool
    {
        return $this->role === 'debitur';
    }

    public function profile()
    {
        return $this->hasOne(NasabahProfile::class);
    }

    public function kprSubmissions()
    {
        return $this->hasMany(KprSubmission::class);
    }
}
