<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = ['company_id','condominium_id','category_id','supplier_id','responsible_user_id','subject','description','status','priority','origin','opened_at','deadline_at','finished_at','shared_with_residents','created_by','updated_by'];

    protected function casts(): array
    {
        return ['opened_at'=>'datetime','deadline_at'=>'datetime','finished_at'=>'datetime','shared_with_residents'=>'boolean'];
    }

    public function condominium() { return $this->belongsTo(Condominium::class); }
    public function updates() { return $this->hasMany(IssueUpdate::class); }
}
