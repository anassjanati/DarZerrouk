<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->foreignId('publisher_id')->nullable()->constrained()->onDelete('set null');
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('language', 20);
            $table->string('edition', 50)->nullable();
            $table->year('publication_year')->nullable();
            $table->integer('pages')->nullable();
            $table->enum('format', ['hardcover', 'paperback', 'ebook']);
            $table->enum('condition', ['new', 'used_like_new', 'used_good'])->default('new');
            $table->string('cover_image')->nullable();
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->integer('reorder_level')->default(5);
            $table->string('shelf_location', 50)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('isbn');
            $table->index('barcode');
            $table->index('category_id');
            $table->index('author_id');
            $table->index('is_active');
            $table->index('stock_quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
