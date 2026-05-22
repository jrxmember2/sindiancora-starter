<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCondominium extends Model
{
    protected $table = 'company_condominiums';

    protected $fillable = [
        'company_id',
        'condominium_id',
        'relationship_type',
        'status',
        'linked_by_user_id',
        'approved_by_user_id',
        'starts_at',
        'ends_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }

    public function linkedBy()
    {
        return $this->belongsTo(User::class, 'linked_by_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
