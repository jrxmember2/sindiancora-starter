<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Scopes\CompanyScope;
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

    public function resolveRouteBinding($value, $field = null)
    {
        $query = static::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->where($field ?? $this->getRouteKeyName(), $value);

        $user = request()?->user();
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;

        if ($user && ! $user->isSuperAdmin() && $company) {
            $query->where(function ($inner) use ($company) {
                $inner
                    ->whereNull('condominium_id')
                    ->orWhereHas('condominium.companyLinks', function ($linkQuery) use ($company) {
                        $linkQuery
                            ->where('company_id', $company->id)
                            ->where('status', 'active');
                    });
            });
        }

        return $query->firstOrFail();
    }
}
