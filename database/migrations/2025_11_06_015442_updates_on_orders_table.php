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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'assigned', 'delivered', 'completed', 'cancelled'])
                ->default('pending')
                ->change();
            $table->boolean('is_paid')->default(false)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'assigned', 'collected', 'completed', 'cancelled'])
                ->default('pending')
                ->change();
            $table->dropColumn('is_paid');
        });
    }
};
