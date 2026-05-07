<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EGP');
            $table->string('method', 24)->default('cash'); // cash, instapay, vcash, bank, other
            $table->string('reference')->nullable();
            $table->dateTime('paid_at');
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['payer_id','payee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
