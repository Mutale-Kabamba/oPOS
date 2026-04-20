<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('parent_transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->cascadeOnDelete();
        });

        $this->updatePaymentStatusEnum(['pending', 'partially_paid', 'paid'], 'pending');
    }

    public function down(): void
    {
        DB::table('transactions')
            ->where('payment_status', 'partially_paid')
            ->update(['payment_status' => 'pending']);

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_transaction_id');
        });

        $this->updatePaymentStatusEnum(['pending', 'paid'], 'pending');
    }

    private function updatePaymentStatusEnum(array $values, string $default): void
    {
        $driver = DB::connection()->getDriverName();
        $quotedValues = implode(', ', array_map(fn (string $value) => "'{$value}'", $values));

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN payment_status ENUM({$quotedValues}) NOT NULL DEFAULT '{$default}'");
        }
    }
};