<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Condominium extends Model
{
    use BelongsToCompany, HasFactory;

    protected $table = 'condominiums';

    protected static function booted(): void
    {
        static::saving(function (Condominium $condominium) {
            $digits = preg_replace('/\D+/', '', (string) $condominium->document);
            $condominium->document_digits = $digits !== '' ? $digits : null;
        });

        static::created(function (Condominium $condominium) {
            if (! $condominium->company_id) {
                return;
            }

            $hasPrincipalLink = $condominium->companyLinks()
                ->where('company_id', $condominium->company_id)
                ->exists();

            if ($hasPrincipalLink) {
                return;
            }

            $condominium->companyLinks()->create([
                'company_id' => $condominium->company_id,
                'relationship_type' => 'principal',
                'status' => 'active',
                'starts_at' => $condominium->created_at ?? now(),
                'created_at' => $condominium->created_at ?? now(),
                'updated_at' => $condominium->updated_at ?? now(),
            ]);
        });
    }

    protected $fillable = [
        'company_id',
        'name',
        'document',
        'document_digits',
        'email',
        'phone',
        'status',
        'slug',
        'cep',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state',
        'mandate_start',
        'mandate_end',
        'administrator_name',
        'logo_url',
    ];

    protected function casts(): array
    {
        return [
            'mandate_start' => 'date',
            'mandate_end' => 'date',
        ];
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->resolveLogoUrl($value),
        );
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function companyLinks()
    {
        return $this->hasMany(CompanyCondominium::class);
    }

    public function primaryCompanyLink()
    {
        return $this->hasOne(CompanyCondominium::class)
            ->where('relationship_type', 'principal')
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_condominiums')
            ->withPivot(['id', 'relationship_type', 'status', 'starts_at', 'ends_at', 'linked_by_user_id', 'approved_by_user_id', 'notes'])
            ->withTimestamps();
    }

    public function linkRequests()
    {
        return $this->hasMany(CondominiumLinkRequest::class);
    }

    public function companyUsers()
    {
        return $this->belongsToMany(CompanyUser::class, 'user_condominiums')
            ->withTimestamps();
    }

    protected function resolveLogoUrl(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/storage/'])) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $query = static::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->where($field ?? $this->getRouteKeyName(), $value);

        $user = request()?->user();
        $company = app()->bound('currentCompany') ? app('currentCompany') : null;

        if ($user && ! $user->isSuperAdmin() && $company) {
            $query->whereHas('companyLinks', function ($linkQuery) use ($company) {
                $linkQuery
                    ->where('company_id', $company->id)
                    ->where('status', 'active');
            });
        }

        return $query->firstOrFail();
    }
}
