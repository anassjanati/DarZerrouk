<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bon_de_commandes', function (Blueprint $table) {
            $table->string('status')
                ->default('pending')
                ->change(); // already exists but ensure default

            $table->text('admin_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bon_de_commandes', function (Blueprint $table) {
            $table->dropColumn('admin_note');
        });
    }
};
