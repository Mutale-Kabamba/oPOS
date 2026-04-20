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
        Schema::create('statutory_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->string('reminder_key');
            $table->date('due_date');
            $table->unsignedTinyInteger('offset_days');
            $table->text('recipients')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['reminder_key', 'due_date', 'offset_days'], 'statutory_reminders_unique_send');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statutory_reminder_logs');
    }
};
