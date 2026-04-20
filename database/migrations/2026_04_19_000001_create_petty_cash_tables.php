<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('allocated_by')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('month', 7); // e.g. 2026-04
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month']);
        });

        Schema::create('petty_cash_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->string('category')->nullable();
            $table->date('expense_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_expenses');
        Schema::dropIfExists('petty_cash_allocations');
    }
};
