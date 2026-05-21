<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condominium extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = ['company_id','name','document','email','phone','status','slug','cep','street','number','complement','district','city','state','mandate_start','mandate_end','administrator_name'];

    protected function casts(): array
    {
        return ['mandate_start'=>'date','mandate_end'=>'date'];
    }


}
