<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'document', 'email', 'phone', 'responsible_name', 'slug', 'logo_url', 'primary_color', 'secondary_color', 'status'];

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            if (! $company->slug) {
                $company->slug = Str::slug($company->name);
            }
        });
    }

    public function companyUsers()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users')
            ->withPivot(['role', 'status', 'can_access_whatsapp', 'only_responsible_issues'])
            ->withTimestamps();
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function activeLicense()
    {
        return $this->hasOne(License::class)->whereIn('status', ['active', 'trial'])->latestOfMany();
    }

    public function condominiums()
    {
        return $this->hasMany(Condominium::class);
    }
}
