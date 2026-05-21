<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCondominium extends Model
{
    use HasFactory;

    protected $fillable = ['company_user_id', 'condominium_id'];

    public function companyUser()
    {
        return $this->belongsTo(CompanyUser::class);
    }

    public function condominium()
    {
        return $this->belongsTo(Condominium::class);
    }
}
