<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    protected $fillable = ['company_id', 'user_id', 'role', 'status', 'can_access_whatsapp', 'only_responsible_issues'];

    protected function casts(): array
    {
        return ['can_access_whatsapp' => 'boolean', 'only_responsible_issues' => 'boolean'];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
