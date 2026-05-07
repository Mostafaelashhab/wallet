<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 16); // cash, bank, wallet, card, savings
            $table->string('institution')->nullable();
            $table->string('currency', 3)->default('EGP');
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->string('color', 7)->default('#FF6B35');
            $table->string('icon', 8)->default('💳');
            $table->boolean('include_in_total')->default(true);
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'archived_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
