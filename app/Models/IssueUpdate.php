<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueUpdate extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = ['company_id','issue_id','user_id','description','visibility','old_status','new_status','old_responsible_id','new_responsible_id'];

    protected function casts(): array
    {
        return [];
    }


}
