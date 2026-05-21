<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_condominiums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condominium_id')->constrained('condominiums')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['company_user_id', 'condominium_id']);
            $table->index(['condominium_id', 'company_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_condominiums');
    }
};
