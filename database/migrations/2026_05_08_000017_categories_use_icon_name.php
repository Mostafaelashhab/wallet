<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('icon_name', 32)->default('other')->after('icon');
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->string('icon_name', 32)->default('party')->after('icon');
        });
        Schema::table('goals', function (Blueprint $table) {
            $table->string('icon_name', 32)->default('target')->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('categories', fn (Blueprint $t) => $t->dropColumn('icon_name'));
        Schema::table('groups', fn (Blueprint $t) => $t->dropColumn('icon_name'));
        Schema::table('goals', fn (Blueprint $t) => $t->dropColumn('icon_name'));
    }
};
