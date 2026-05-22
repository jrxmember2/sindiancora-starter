<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'avatar_url', 'status', 'is_superadmin', 'must_change_password'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_superadmin' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_users')
            ->withPivot(['role', 'status', 'can_access_whatsapp', 'only_responsible_issues'])
            ->withTimestamps();
    }

    public function companyUsers()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function activeCompanyUserFor(Company $company): ?CompanyUser
    {
        return $this->companyUsers()
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->first();
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_superadmin;
    }

    public function requiresPasswordChange(): bool
    {
        return (bool) $this->must_change_password;
    }
}
