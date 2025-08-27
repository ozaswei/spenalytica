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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('categoryId')->nullable(); // null => uncategorized budget
            $table->decimal('amount', 15, 2); // budget amount
            $table->enum('period', ['monthly', 'weekly'])->default('monthly'); // for future flexibility
            $table->date('start_date')->nullable(); // when budget starts
            $table->boolean('active')->default(true);
            $table->timestamps();

            // optional: add FKs later if you want
            $table->index('userId');
            $table->index('categoryId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
