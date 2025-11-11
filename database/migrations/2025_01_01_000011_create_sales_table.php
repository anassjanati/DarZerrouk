<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 20)->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('sale_date');
            $table->decimal('subtotal', 12, 2);
            $table->enum('discount_type', ['percentage', 'fixed', 'points'])->nullable();
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(20.00);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->integer('points_earned')->default(0);
            $table->integer('points_redeemed')->default(0);
            $table->enum('payment_status', ['completed', 'pending', 'refunded'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('invoice_number');
            $table->index('customer_id');
            $table->index('user_id');
            $table->index('sale_date');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
