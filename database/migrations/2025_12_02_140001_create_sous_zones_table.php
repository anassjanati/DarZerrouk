<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sous_zones')) {
            Schema::create('sous_zones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('zone_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('code');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('zone_id');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sous_zones');
    }
};
