<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sous_zones') && !Schema::hasColumn('sous_zones', 'created_at')) {
            Schema::table('sous_zones', function (Blueprint $table) {
                $table->timestamps();
            });
        }
        if (Schema::hasTable('sous_sous_zones') && !Schema::hasColumn('sous_sous_zones', 'created_at')) {
            Schema::table('sous_sous_zones', function (Blueprint $table) {
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sous_zones') && Schema::hasColumn('sous_zones', 'created_at')) {
            Schema::table('sous_zones', function (Blueprint $table) {
                $table->dropTimestamps();
            });
        }
        if (Schema::hasTable('sous_sous_zones') && Schema::hasColumn('sous_sous_zones', 'created_at')) {
            Schema::table('sous_sous_zones', function (Blueprint $table) {
                $table->dropTimestamps();
            });
        }
    }
};
