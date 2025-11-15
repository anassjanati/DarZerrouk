<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('bon_de_commande_lines', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bon_de_commande_id')->constrained()->onDelete('cascade');
        $table->foreignId('book_id')->constrained()->onDelete('cascade');
        $table->integer('quantity');
        $table->decimal('cost_price', 12, 2)->default(0);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_de_commande_lines');
    }
};
