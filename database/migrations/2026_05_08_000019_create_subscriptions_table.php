<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('icon_name', 32)->default('sparkles');
            $table->string('color', 7)->default('#6366F1');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EGP');
            $table->string('frequency', 12); // monthly, yearly, weekly
            $table->date('next_billing_at');
            $table->date('started_at')->nullable();
            $table->string('cancel_url')->nullable();
            $table->string('note')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('auto_log')->default(true); // auto create transaction on billing
            $table->timestamps();
            $table->index(['user_id', 'active', 'next_billing_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
