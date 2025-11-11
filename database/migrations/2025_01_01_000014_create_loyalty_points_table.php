<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['earned', 'redeemed', 'expired', 'adjusted']);
            $table->integer('points');
            $table->string('description')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();
            
            $table->index('customer_id');
            $table->index('sale_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
