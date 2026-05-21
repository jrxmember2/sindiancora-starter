<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseUsage extends Model
{
    protected $table = 'license_usage';

    protected $fillable = [
        'company_id',
        'license_id',
        'active_condominiums',
        'active_internal_users',
        'storage_used_mb',
        'whatsapp_instances_used',
        'ai_credits_used_month',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function license()
    {
        return $this->belongsTo(License::class);
    }
}
