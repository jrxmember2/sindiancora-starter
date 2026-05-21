<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'avatar_url', 'status', 'is_superadmin'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_superadmin' => 'boolean'];
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

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_superadmin;
    }
}
