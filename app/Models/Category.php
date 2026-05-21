<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = ['company_id','name','type','active'];

    protected function casts(): array
    {
        return ['active'=>'boolean'];
    }


}
