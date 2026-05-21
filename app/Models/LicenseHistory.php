<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseHistory extends Model
{
    protected $table = 'license_history';

    public $timestamps = false;

    const UPDATED_AT = null;

    protected $fillable = ['license_id', 'changed_by', 'change_type', 'old_data', 'new_data', 'notes', 'created_at'];

    protected function casts(): array
    {
        return [
            'old_data' => 'array',
            'new_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
