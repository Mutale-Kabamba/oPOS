<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->after('account_id')->constrained('suppliers')->nullOnDelete();
            $table->boolean('is_reconciled')->default(false)->after('payment_status');
            $table->timestamp('reconciled_at')->nullable()->after('is_reconciled');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn(['is_reconciled', 'reconciled_at']);
        });
    }
};