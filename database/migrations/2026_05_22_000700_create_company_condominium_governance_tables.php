<?php

use App\Models\Condominium;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_condominiums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condominium_id')->constrained('condominiums')->cascadeOnDelete();
            $table->string('relationship_type')->default('principal')->index();
            $table->string('status')->default('active')->index();
            $table->foreignId('linked_by_user_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->foreignId('approved_by_user_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'condominium_id']);
        });

        Schema::create('condominium_link_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('condominium_id')->constrained('condominiums')->cascadeOnDelete();
            $table->foreignId('requesting_company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('current_primary_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('requested_by_user_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->string('status')->default('pending')->index();
            $table->string('decision_type')->nullable()->index();
            $table->foreignId('responded_by_user_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('request_notes')->nullable();
            $table->text('decision_notes')->nullable();
            $table->timestamps();
        });

        Condominium::query()
            ->withoutGlobalScopes()
            ->get()
            ->each(function (Condominium $condominium) {
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

    public function down(): void
    {
        Schema::dropIfExists('condominium_link_requests');
        Schema::dropIfExists('company_condominiums');
    }
};
