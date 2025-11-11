<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('expense_number', 20)->unique();
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'check']);
            $table->string('reference_number', 100)->nullable();
            $table->string('receipt_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('expense_number');
            $table->index('expense_category_id');
            $table->index('user_id');
            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
