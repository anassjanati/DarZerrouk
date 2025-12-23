<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('book_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');

                $table->foreignId('from_zone_id')->nullable()->constrained('zones')->nullOnDelete();
                $table->foreignId('to_zone_id')->nullable()->constrained('zones')->nullOnDelete();

                $table->foreignId('from_sous_zone_id')->nullable()->constrained('sous_zones')->nullOnDelete();
                $table->foreignId('to_sous_zone_id')->nullable()->constrained('sous_zones')->nullOnDelete();

                $table->foreignId('from_sous_sous_zone_id')->nullable()->constrained('sous_sous_zones')->nullOnDelete();
                $table->foreignId('to_sous_sous_zone_id')->nullable()->constrained('sous_sous_zones')->nullOnDelete();

                $table->integer('quantity');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['book_id', 'created_at']);
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
