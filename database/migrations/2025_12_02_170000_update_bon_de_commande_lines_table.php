<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bon_de_commande_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('bon_de_commande_lines', 'selling_price')) {
                $table->decimal('selling_price', 12, 2)->default(0)->after('cost_price');
            }
        });

        Schema::table('bon_de_commandes', function (Blueprint $table) {
            if (!Schema::hasColumn('bon_de_commandes', 'status')) {
                $table->enum('status', ['pending', 'validated'])->default('pending')->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bon_de_commande_lines', function (Blueprint $table) {
            if (Schema::hasColumn('bon_de_commande_lines', 'selling_price')) {
                $table->dropColumn('selling_price');
            }
        });
    }
};
