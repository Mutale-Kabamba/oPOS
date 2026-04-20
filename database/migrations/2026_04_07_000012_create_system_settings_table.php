<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();
        $settlementAccountId = DB::table('accounts')->where('code', '1100')->value('id');
        $contraAccountId = DB::table('accounts')->where('code', '1199')->value('id');

        DB::table('system_settings')->insert([
            [
                'key' => 'posting_rules.default_settlement_account_id',
                'value' => $settlementAccountId ? (string) $settlementAccountId : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'posting_rules.default_contra_account_id',
                'value' => $contraAccountId ? (string) $contraAccountId : null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};