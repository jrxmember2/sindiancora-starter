<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = ['company_id','condominium_id','title','document_type','amount','valid_until','renewal_date','status','available_to_residents','added_to_ai_assistant','observation','file_path','created_by'];

    protected function casts(): array
    {
        return ['amount'=>'decimal:2','valid_until'=>'date','renewal_date'=>'date','available_to_residents'=>'boolean','added_to_ai_assistant'=>'boolean'];
    }

    public function condominium() { return $this->belongsTo(Condominium::class); }
}
