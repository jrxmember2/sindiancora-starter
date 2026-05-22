<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AuditLogger
{
    public function record(
        string $action,
        ?User $actor,
        Model|string|null $auditable = null,
        ?Company $company = null,
        array $oldValues = [],
        array $newValues = [],
    ): void {
        $resolvedCompany = $company
            ?? (app()->bound('currentCompany') ? app('currentCompany') : null);

        AuditLog::query()->create([
            'company_id' => $resolvedCompany?->id,
            'user_id' => $actor?->id,
            'action' => $action,
            'auditable_type' => $this->auditableType($auditable),
            'auditable_id' => $this->auditableId($auditable),
            'old_values' => $this->sanitizePayload($oldValues) ?: null,
            'new_values' => $this->sanitizePayload($newValues) ?: null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    protected function auditableType(Model|string|null $auditable): ?string
    {
        if ($auditable instanceof Model) {
            return $auditable->getMorphClass();
        }

        return is_string($auditable) ? $auditable : null;
    }

    protected function auditableId(Model|string|null $auditable): ?int
    {
        if ($auditable instanceof Model && $auditable->getKey()) {
            return (int) $auditable->getKey();
        }

        return null;
    }

    protected function sanitizePayload(array $payload): array
    {
        return Arr::except($payload, [
            'password',
            'password_confirmation',
            'remember_token',
        ]);
    }
}
