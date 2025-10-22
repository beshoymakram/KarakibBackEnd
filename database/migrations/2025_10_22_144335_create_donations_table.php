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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donation_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->enum('fund_name', ['Education Fund', 'Reforestation', 'Community Health']);
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
