<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EGP');
            $table->string('description');
            $table->dateTime('occurred_at');
            $table->string('split_type', 16)->default('equal'); // equal, exact, percent, shares
            $table->string('receipt_path')->nullable();
            $table->decimal('location_lat', 10, 7)->nullable();
            $table->decimal('location_lng', 10, 7)->nullable();
            $table->string('location_name')->nullable();
            $table->foreignId('recurring_id')->nullable();
            $table->timestamps();
            $table->index(['group_id','occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
