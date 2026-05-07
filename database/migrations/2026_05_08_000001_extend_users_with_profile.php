<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone', 32)->nullable()->after('avatar');
            $table->string('locale', 5)->default('ar')->after('phone');
            $table->string('currency', 3)->default('EGP')->after('locale');
            $table->string('instapay_handle')->nullable()->after('currency');
            $table->string('vodafone_cash')->nullable()->after('instapay_handle');
            $table->string('color', 7)->default('#FF6B35')->after('vodafone_cash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar','phone','locale','currency','instapay_handle','vodafone_cash','color']);
        });
    }
};
