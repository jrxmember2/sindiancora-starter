<?php

namespace App\Services\Licensing;

use App\Models\Company;
use App\Models\License;
use App\Models\Module;

class LicenseGuard
{
    public function __construct(protected LicenseUsageService $usageService)
    {
    }

    public function currentLicense(?Company $company): ?License
    {
        if (! $company) {
            return null;
        }

        return $company->activeLicense()->first()
            ?? $company->licenses()->latest('starts_at')->latest('id')->first();
    }

    public function isActive(?Company $company): bool
    {
        return $this->status($company)['allows_access'];
    }

    public function isReadOnly(?Company $company): bool
    {
        return $this->status($company)['is_read_only'];
    }

    public function canAccessModule(?Company $company, string $moduleKey): bool
    {
        if (! $company) {
            return false;
        }

        $status = $this->status($company);

        if (! $status['allows_access']) {
            return false;
        }

        $license = $this->currentLicense($company);

        return $license?->modules()
            ->where('modules.key', $moduleKey)
            ->wherePivot('enabled', true)
            ->exists() ?? false;
    }

    public function canCreateCondominium(?Company $company): bool
    {
        return $this->canUseContractedResource(
            $company,
            'active_condominiums',
            fn (Company $company) => $company->condominiums()->withoutGlobalScopes()->where('status', 'active')->count(),
            'max_condominiums'
        );
    }

    public function canCreateInternalUser(?Company $company): bool
    {
        return $this->canUseContractedResource(
            $company,
            'active_internal_users',
            fn (Company $company) => $company->companyUsers()->where('status', 'active')->count(),
            'max_internal_users'
        );
    }

    public function canUseStorage(?Company $company, int|float $fileSizeMb = 0): bool
    {
        return $this->canUseContractedResource(
            $company,
            'storage_used_mb',
            fn (Company $company) => $company->licenseUsage()->value('storage_used_mb') ?? 0,
            'max_storage_mb',
            $fileSizeMb
        );
    }

    public function canUseAI(?Company $company, int $credits = 1): bool
    {
        return $this->canUseContractedResource(
            $company,
            'ai_credits_used_month',
            fn (Company $company) => $company->licenseUsage()->value('ai_credits_used_month') ?? 0,
            'monthly_ai_credits',
            $credits
        );
    }

    public function canUseWhatsApp(?Company $company, int $instances = 1): bool
    {
        return $this->canUseContractedResource(
            $company,
            'whatsapp_instances_used',
            fn (Company $company) => $company->licenseUsage()->value('whatsapp_instances_used') ?? 0,
            'max_whatsapp_instances',
            $instances
        );
    }

    public function moduleAccessMap(?Company $company): array
    {
        $activeKeys = Module::query()->where('active', true)->pluck('key')->all();
        $map = array_fill_keys($activeKeys, false);

        if (! $company) {
            return $map;
        }

        $license = $this->currentLicense($company);

        if (! $license || ! $this->status($company)['allows_access']) {
            return $map;
        }

        $enabledKeys = $license->modules()
            ->wherePivot('enabled', true)
            ->pluck('modules.key')
            ->all();

        foreach ($enabledKeys as $key) {
            $map[$key] = true;
        }

        return $map;
    }

    public function usage(?Company $company): array
    {
        $license = $this->currentLicense($company);
        $usageSnapshot = $company?->licenseUsage()->first();

        $usedCondominiums = $company
            ? $company->condominiums()->withoutGlobalScopes()->where('status', 'active')->count()
            : 0;

        $usedInternalUsers = $company
            ? $company->companyUsers()->where('status', 'active')->count()
            : 0;

        return [
            'condominiums' => $this->metric($usedCondominiums, $license?->max_condominiums ?? 0),
            'internal_users' => $this->metric($usedInternalUsers, $license?->max_internal_users ?? 0),
            'storage' => $this->metric($usageSnapshot?->storage_used_mb ?? 0, $license?->max_storage_mb ?? 0),
            'whatsapp' => $this->metric($usageSnapshot?->whatsapp_instances_used ?? 0, $license?->max_whatsapp_instances ?? 0),
            'ai' => $this->metric($usageSnapshot?->ai_credits_used_month ?? 0, $license?->monthly_ai_credits ?? 0),
            'synced_at' => $usageSnapshot?->updated_at?->toIso8601String(),
        ];
    }

    public function alerts(?Company $company): array
    {
        if (! $company) {
            return [];
        }

        $license = $this->currentLicense($company);
        $status = $this->status($company);
        $usage = $this->usage($company);
        $alerts = [];

        if ($license && $license->ends_at && $license->ends_at->isFuture() && now()->diffInDays($license->ends_at, false) <= 30) {
            $alerts[] = [
                'tone' => 'yellow',
                'title' => 'Licenca perto do vencimento',
                'message' => "A licenca atual vence em {$license->ends_at->format('d/m/Y')}.",
            ];
        }

        if ($license && in_array($license->financial_status, ['due', 'overdue'], true)) {
            $alerts[] = [
                'tone' => $license->financial_status === 'overdue' ? 'red' : 'yellow',
                'title' => 'Atencao ao financeiro',
                'message' => $license->financial_status === 'overdue'
                    ? 'A licenca esta com status financeiro em atraso.'
                    : 'A licenca esta com cobranca proxima ou pendente.',
            ];
        }

        if (! $status['allows_write'] && $status['allows_access']) {
            $alerts[] = [
                'tone' => 'yellow',
                'title' => 'Modo somente leitura',
                'message' => $status['message'],
            ];
        }

        if (! $status['allows_access']) {
            $alerts[] = [
                'tone' => 'red',
                'title' => 'Acesso contratual bloqueado',
                'message' => $status['message'],
            ];
        }

        foreach ([
            'condominiums' => 'Condominios',
            'internal_users' => 'Usuarios internos',
            'storage' => 'Storage',
            'whatsapp' => 'Instancias WhatsApp',
            'ai' => 'Creditos de IA',
        ] as $key => $label) {
            $metric = $usage[$key];

            if ($metric['limit'] <= 0) {
                continue;
            }

            if ($metric['percent'] >= 100) {
                $alerts[] = [
                    'tone' => 'red',
                    'title' => "Limite atingido: {$label}",
                    'message' => "O recurso {$label} atingiu o limite contratado.",
                ];
            } elseif ($metric['percent'] >= 80) {
                $alerts[] = [
                    'tone' => 'yellow',
                    'title' => "Limite proximo: {$label}",
                    'message' => "O recurso {$label} esta em {$metric['percent']}% do limite contratado.",
                ];
            }
        }

        return $alerts;
    }

    public function status(?Company $company): array
    {
        $license = $this->currentLicense($company);

        if (! $company || ! $license) {
            return [
                'code' => 'missing',
                'label' => 'Sem licenca',
                'message' => 'Nao existe licenca configurada para esta empresa.',
                'allows_access' => false,
                'allows_write' => false,
                'is_read_only' => false,
            ];
        }

        if ($license->auto_suspend_when_overdue && $license->financial_status === 'overdue') {
            return [
                'code' => 'overdue_suspended',
                'label' => 'Suspensa por inadimplencia',
                'message' => 'A licenca foi suspensa automaticamente por inadimplencia.',
                'allows_access' => false,
                'allows_write' => false,
                'is_read_only' => false,
            ];
        }

        if (in_array($license->status, ['blocked', 'suspended', 'canceled', 'pending'], true)) {
            return [
                'code' => $license->status,
                'label' => $this->statusLabel($license->status),
                'message' => 'A licenca atual nao permite operacao neste momento.',
                'allows_access' => false,
                'allows_write' => false,
                'is_read_only' => false,
            ];
        }

        if ($license->status === 'read_only') {
            return [
                'code' => 'read_only',
                'label' => 'Somente leitura',
                'message' => 'A licenca esta em modo somente leitura.',
                'allows_access' => true,
                'allows_write' => false,
                'is_read_only' => true,
            ];
        }

        if ($license->ends_at && $license->ends_at->isPast()) {
            if ($license->read_only_when_expired) {
                return [
                    'code' => 'expired_read_only',
                    'label' => 'Expirada - somente leitura',
                    'message' => 'A licenca venceu e a empresa esta operando em modo somente leitura.',
                    'allows_access' => true,
                    'allows_write' => false,
                    'is_read_only' => true,
                ];
            }

            return [
                'code' => 'expired',
                'label' => 'Expirada',
                'message' => 'A licenca venceu e o acesso operacional foi bloqueado.',
                'allows_access' => false,
                'allows_write' => false,
                'is_read_only' => false,
            ];
        }

        return [
            'code' => $license->status,
            'label' => $this->statusLabel($license->status),
            'message' => 'Licenca ativa para operacao.',
            'allows_access' => in_array($license->status, ['active', 'trial'], true),
            'allows_write' => in_array($license->status, ['active', 'trial'], true),
            'is_read_only' => false,
        ];
    }

    public function syncUsageSnapshot(?Company $company): void
    {
        if (! $company) {
            return;
        }

        $this->usageService->sync($company);
    }

    protected function canUseContractedResource(
        ?Company $company,
        string $metricKey,
        callable $usedResolver,
        string $limitKey,
        int|float $incoming = 1
    ): bool {
        if (! $company) {
            return false;
        }

        $status = $this->status($company);

        if (! $status['allows_write']) {
            return false;
        }

        $license = $this->currentLicense($company);

        if (! $license) {
            return false;
        }

        $used = $usedResolver($company);
        $limit = (int) ($license->{$limitKey} ?? 0);

        if (($used + $incoming) <= $limit) {
            return true;
        }

        return (bool) $license->allow_overage && ! $license->block_new_records_on_limit;
    }

    protected function metric(int|float $used, int|float $limit): array
    {
        $percent = $limit > 0 ? min(999, (int) round(($used / $limit) * 100)) : 0;

        return [
            'used' => $used,
            'limit' => $limit,
            'remaining' => max(0, $limit - $used),
            'percent' => $percent,
            'reached_limit' => $limit > 0 ? $used >= $limit : false,
        ];
    }

    protected function statusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Ativa',
            'trial' => 'Teste',
            'pending' => 'Pendente',
            'expired' => 'Expirada',
            'suspended' => 'Suspensa',
            'canceled' => 'Cancelada',
            'blocked' => 'Bloqueada',
            'read_only' => 'Somente leitura',
            default => ucfirst($status),
        };
    }
}
