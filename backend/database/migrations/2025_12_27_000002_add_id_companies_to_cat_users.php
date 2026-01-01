<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('cat_users') && !Schema::hasColumn('cat_users', 'id_companies')) {
            Schema::table('cat_users', function (Blueprint $table) {
                $table->unsignedInteger('id_companies')->after('name');
                $table->foreign('id_companies')->references('id')->on('cat_companies');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cat_users') && Schema::hasColumn('cat_users', 'id_companies')) {
            Schema::table('cat_users', function (Blueprint $table) {
                $table->dropForeign(['id_companies']);
                $table->dropColumn('id_companies');
            });
        }
    }
};
