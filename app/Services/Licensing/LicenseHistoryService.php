<?php

namespace App\Services\Licensing;

use App\Models\License;
use App\Models\LicenseHistory;
use App\Models\User;

class LicenseHistoryService
{
    public function record(
        License $license,
        ?User $actor,
        string $changeType,
        array $oldData = [],
        array $newData = [],
        ?string $notes = null
    ): LicenseHistory {
        return LicenseHistory::query()->create([
            'license_id' => $license->id,
            'changed_by' => $actor?->id,
            'change_type' => $changeType,
            'old_data' => empty($oldData) ? null : $oldData,
            'new_data' => empty($newData) ? null : $newData,
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }
}
