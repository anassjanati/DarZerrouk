<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_tier_id')->constrained()->onDelete('cascade');
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone', 20);
            $table->string('whatsapp', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->integer('total_points')->default(0);
            $table->decimal('total_purchases', 12, 2)->default(0);
            $table->date('last_purchase_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('phone');
            $table->index('email');
            $table->index('membership_tier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
