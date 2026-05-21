<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['key', 'name', 'description', 'category', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
