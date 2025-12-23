<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter is_active à sous_zones si manquant
        if (Schema::hasTable('sous_zones') && !Schema::hasColumn('sous_zones', 'is_active')) {
            Schema::table('sous_zones', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('code');
                $table->index('is_active');
            });
        }

        // Ajouter is_active à sous_sous_zones si manquant
        if (Schema::hasTable('sous_sous_zones') && !Schema::hasColumn('sous_sous_zones', 'is_active')) {
            Schema::table('sous_sous_zones', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('code');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sous_zones') && Schema::hasColumn('sous_zones', 'is_active')) {
            Schema::table('sous_zones', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasTable('sous_sous_zones') && Schema::hasColumn('sous_sous_zones', 'is_active')) {
            Schema::table('sous_sous_zones', function (Blueprint $table) {
                $table->dropIndex(['is_active']);
                $table->dropColumn('is_active');
            });
        }
    }
};
