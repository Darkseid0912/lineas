<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rel_role_permissions', function (Blueprint $table) {
            $table->unique(['id_role', 'id_module', 'id_permission'], 'rel_role_permissions_role_module_permission_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rel_role_permissions', function (Blueprint $table) {
            $table->dropUnique('rel_role_permissions_role_module_permission_unique');
        });
    }
};
