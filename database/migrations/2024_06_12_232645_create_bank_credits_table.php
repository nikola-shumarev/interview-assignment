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
        Schema::create('bank_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->constrained('consumers')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('remaining_amount', 7, 2);
            $table->decimal('interest_rate', 5, 2)->default(7.9);
            $table->date('last_interest_applied')->nullable();
            $table->date('due_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_credits');
    }
};
