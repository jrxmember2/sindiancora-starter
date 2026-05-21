<?php

namespace App\Models\Concerns;

use App\Models\Scopes\CompanyScope;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (! $model->company_id && app()->bound('currentCompany') && app('currentCompany')) {
                $model->company_id = app('currentCompany')->id;
            }
        });
    }
}
