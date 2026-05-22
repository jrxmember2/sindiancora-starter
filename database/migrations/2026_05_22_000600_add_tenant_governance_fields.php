<?php

use App\Models\CompanyUser;
use App\Models\Condominium;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->index();
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->boolean('is_primary')->default(false)->index();
        });

        Schema::table('condominiums', function (Blueprint $table) {
            $table->string('document_digits', 20)->nullable()->index();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        CompanyUser::query()
            ->orderBy('company_id')
            ->orderByRaw("case when role = 'admin' then 0 else 1 end")
            ->orderBy('id')
            ->get()
            ->groupBy('company_id')
            ->each(function ($memberships) {
                $primary = $memberships
                    ->first(fn (CompanyUser $membership) => $membership->role === 'admin' && $membership->status === 'active')
                    ?? $memberships->first(fn (CompanyUser $membership) => $membership->status === 'active')
                    ?? $memberships->first();

                if ($primary) {
                    $primary->forceFill(['is_primary' => true])->save();
                }
            });

        Condominium::query()
            ->withoutGlobalScopes()
            ->get()
            ->each(function (Condominium $condominium) {
                $documentDigits = preg_replace('/\D+/', '', (string) $condominium->document) ?: null;

                $condominium->forceFill(['document_digits' => $documentDigits])->save();
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        Schema::table('condominiums', function (Blueprint $table) {
            $table->dropColumn('document_digits');
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->dropColumn('is_primary');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};
