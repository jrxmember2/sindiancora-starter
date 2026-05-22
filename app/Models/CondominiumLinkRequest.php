<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondominiumLinkRequest extends Model
{
    protected $fillable = [
        'condominium_id',
        'requesting_company_id',
        'current_primary_company_id',
        'requested_by_user_id',
        'status',
        'decision_type',
        'responded_by_user_id',
        'resolved_at',
        'request_notes',
        'decision_notes',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }

    public function requestingCompany()
    {
        return $this->belongsTo(Company::class, 'requesting_company_id');
    }

    public function currentPrimaryCompany()
    {
        return $this->belongsTo(Company::class, 'current_primary_company_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by_user_id');
    }
}
