<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('name_ar', 50)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('points_multiplier', 3, 2)->default(1.00);
            $table->decimal('min_purchase_amount', 10, 2)->default(0);
            $table->string('color', 7)->nullable();
            $table->text('benefits')->nullable();
            $table->timestamps();
            
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_tiers');
    }
};
