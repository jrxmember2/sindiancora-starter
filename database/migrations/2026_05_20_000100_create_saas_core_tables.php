<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document', 40)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->string('primary_color', 20)->nullable();
            $table->string('secondary_color', 20)->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('company_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('admin');
            $table->string('status')->default('active')->index();
            $table->boolean('can_access_whatsapp')->default(false);
            $table->boolean('only_responsible_issues')->default(false);
            $table->timestamps();
            $table->unique(['company_id', 'user_id']);
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->index();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->index();
            $table->string('status')->default('pending')->index();
            $table->string('financial_status')->default('current')->index();
            $table->string('billing_type')->default('monthly');
            $table->decimal('monthly_amount', 12, 2)->default(0);
            $table->decimal('setup_amount', 12, 2)->default(0);
            $table->unsignedTinyInteger('billing_day')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->date('renews_at')->nullable();
            $table->unsignedInteger('max_condominiums')->default(0);
            $table->unsignedInteger('max_internal_users')->default(0);
            $table->unsignedInteger('max_storage_mb')->default(1024);
            $table->unsignedInteger('max_whatsapp_instances')->default(0);
            $table->unsignedInteger('monthly_ai_credits')->default(0);
            $table->boolean('allow_overage')->default(false);
            $table->boolean('block_new_records_on_limit')->default(true);
            $table->boolean('read_only_when_expired')->default(true);
            $table->boolean('auto_suspend_when_overdue')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('license_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
            $table->unique(['license_id', 'module_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_modules');
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('company_users');
        Schema::dropIfExists('companies');
    }
};
