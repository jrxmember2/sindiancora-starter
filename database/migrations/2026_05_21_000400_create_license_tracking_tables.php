<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('license_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->string('change_type')->index();
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('license_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('license_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('active_condominiums')->default(0);
            $table->unsignedInteger('active_internal_users')->default(0);
            $table->unsignedInteger('storage_used_mb')->default(0);
            $table->unsignedInteger('whatsapp_instances_used')->default(0);
            $table->unsignedInteger('ai_credits_used_month')->default(0);
            $table->timestamps();

            $table->unique('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_usage');
        Schema::dropIfExists('license_history');
    }
};
