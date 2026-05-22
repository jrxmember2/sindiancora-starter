<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    protected $fillable = ['company_id', 'user_id', 'role', 'status', 'can_access_whatsapp', 'only_responsible_issues', 'is_primary'];

    protected function casts(): array
    {
        return [
            'can_access_whatsapp' => 'boolean',
            'only_responsible_issues' => 'boolean',
            'is_primary' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condominiums()
    {
        return $this->belongsToMany(Condominium::class, 'user_condominiums')
            ->withTimestamps();
    }

    public function isPrimaryAdmin(): bool
    {
        return $this->is_primary && $this->role === 'admin' && $this->status === 'active';
    }
}
