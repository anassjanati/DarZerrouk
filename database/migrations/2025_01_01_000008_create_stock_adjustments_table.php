<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['addition', 'subtraction']);
            $table->integer('quantity');
            $table->enum('reason', ['damage', 'theft', 'correction', 'return', 'other']);
            $table->text('notes')->nullable();
            $table->date('adjustment_date');
            $table->timestamps();
            
            $table->index('book_id');
            $table->index('user_id');
            $table->index('adjustment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
