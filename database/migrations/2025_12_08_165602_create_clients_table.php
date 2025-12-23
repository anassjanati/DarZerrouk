<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique()->nullable();           // CLT-0001
        $table->string('name');
        $table->string('company_name')->nullable();
        $table->string('phone')->nullable();
        $table->string('whatsapp')->nullable();
        $table->string('email')->nullable();
        $table->string('city')->nullable();
        $table->string('address')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
