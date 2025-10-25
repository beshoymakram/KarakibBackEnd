<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the actual foreign key names
        $foreignKeys = $this->getForeignKeys('cart_items');

        Schema::table('cart_items', function (Blueprint $table) use ($foreignKeys) {
            // Drop foreign keys if they exist
            foreach ($foreignKeys as $foreignKey) {
                if (strpos($foreignKey, 'product_id') !== false) {
                    DB::statement("ALTER TABLE cart_items DROP FOREIGN KEY {$foreignKey}");
                }
                if (strpos($foreignKey, 'user_id') !== false) {
                    DB::statement("ALTER TABLE cart_items DROP FOREIGN KEY {$foreignKey}");
                }
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            // Drop the composite unique index
            $table->dropUnique(['user_id', 'product_id']);

            // Drop the product_id column
            $table->dropColumn('product_id');

            // Add polymorphic columns
            $table->morphs('cartable');

            // Re-add user_id foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['user_id']);

            // Remove polymorphic columns
            $table->dropMorphs('cartable');

            // Restore product_id column
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Restore original unique index
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Get foreign key names for a table
     */
    private function getForeignKeys($table)
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '{$table}'
            AND CONSTRAINT_NAME != 'PRIMARY'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        return array_map(function ($key) {
            return $key->CONSTRAINT_NAME;
        }, $foreignKeys);
    }
};
