<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('type')->after('password');

                // Add foreign key if catalog table exists
                if (Schema::hasTable('cat_users')) {
                    $table->foreign('type')
                        ->references('id')
                        ->on('cat_users')
                        ->cascadeOnUpdate();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'type')) {
            Schema::table('users', function (Blueprint $table) {
                // Drop FK if present, then the column
                try {
                    $table->dropForeign(['type']);
                } catch (\Throwable $e) {
                    // ignore if FK doesn't exist
                }
                $table->dropColumn('type');
            });
        }
    }
};
