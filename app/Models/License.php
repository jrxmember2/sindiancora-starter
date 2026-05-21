<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'company_id', 'contract_number', 'status', 'financial_status', 'billing_type',
        'monthly_amount', 'setup_amount', 'billing_day', 'starts_at', 'ends_at', 'renews_at',
        'max_condominiums', 'max_internal_users', 'max_storage_mb', 'max_whatsapp_instances',
        'monthly_ai_credits', 'allow_overage', 'block_new_records_on_limit', 'read_only_when_expired',
        'auto_suspend_when_overdue', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date', 'ends_at' => 'date', 'renews_at' => 'date',
            'monthly_amount' => 'decimal:2', 'setup_amount' => 'decimal:2',
            'allow_overage' => 'boolean', 'block_new_records_on_limit' => 'boolean',
            'read_only_when_expired' => 'boolean', 'auto_suspend_when_overdue' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'license_modules')
            ->withPivot(['enabled'])
            ->withTimestamps();
    }
}
