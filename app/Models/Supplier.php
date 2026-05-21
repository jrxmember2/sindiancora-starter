<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = ['company_id','name','document','email','phone','mobile','responsible_name','website','rating','cep','street','number','complement','district','city','state','country','notes','active'];

    protected function casts(): array
    {
        return ['active'=>'boolean','rating'=>'integer'];
    }


}
