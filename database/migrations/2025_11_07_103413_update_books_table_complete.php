<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Add new foreign keys (if not exist)
            if (!Schema::hasColumn('books', 'translator_id')) {
                $table->foreignId('translator_id')->nullable()->after('author_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('books', 'editor_id')) {
                $table->foreignId('editor_id')->nullable()->after('translator_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('books', 'publisher_id')) {
                $table->foreignId('publisher_id')->nullable()->after('editor_id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('books', 'zone_id')) {
                $table->foreignId('zone_id')->nullable()->after('publisher_id')->constrained()->onDelete('set null');
            }

            // Add edition details
            if (!Schema::hasColumn('books', 'edition_year')) {
                $table->year('edition_year')->nullable()->after('zone_id');
            }
            if (!Schema::hasColumn('books', 'edition_number')) {
                $table->string('edition_number')->nullable()->after('edition_year'); // 1st, 2nd, 3rd...
            }

            // Add dual pricing
            if (!Schema::hasColumn('books', 'price_1')) {
                $table->decimal('price_1', 10, 2)->default(0)->after('cost_price'); // Prix normal
            }
            if (!Schema::hasColumn('books', 'price_2')) {
                $table->decimal('price_2', 10, 2)->default(0)->after('price_1'); // Prix après remise
            }
            if (!Schema::hasColumn('books', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->nullable()->after('price_2');
            }

            // Rename selling_price to keep compatibility
            if (Schema::hasColumn('books', 'selling_price')) {
                $table->renameColumn('selling_price', 'selling_price_old');
            }

            // Add notes
            if (!Schema::hasColumn('books', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['translator_id']);
            $table->dropForeign(['editor_id']);
            $table->dropForeign(['publisher_id']);
            $table->dropForeign(['zone_id']);
            
            $table->dropColumn([
                'translator_id', 'editor_id', 'publisher_id', 'zone_id',
                'edition_year', 'edition_number',
                'price_1', 'price_2', 'discount_percentage', 'notes'
            ]);
        });
    }
};
