<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostingRulesSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_accountant_can_update_posting_rule_settings(): void
    {
        $accountant = User::factory()->create([
            'role' => 'accountant',
            'is_active' => true,
        ]);

        $settlement = Account::query()->updateOrCreate(
            ['code' => '1100'],
            ['name' => 'Cash', 'type' => 'asset', 'group_name' => 'valuables', 'is_active' => true]
        );

        $contra = Account::query()->updateOrCreate(
            ['code' => '1199'],
            ['name' => 'Clearing', 'type' => 'asset', 'group_name' => 'valuables', 'is_active' => true]
        );

        $response = $this->actingAs($accountant)->patch(route('admin.settings.posting-rules'), [
            'settlement_account_id' => $settlement->id,
            'contra_account_id' => $contra->id,
        ]);

        $response->assertRedirect(route('accounting.settings'));
        $this->assertDatabaseHas('system_settings', [
            'key' => 'posting_rules.default_settlement_account_id',
            'value' => (string) $settlement->id,
        ]);
        $this->assertDatabaseHas('system_settings', [
            'key' => 'posting_rules.default_contra_account_id',
            'value' => (string) $contra->id,
        ]);

        $this->assertSame((string) $settlement->id, SystemSetting::query()->where('key', 'posting_rules.default_settlement_account_id')->value('value'));
    }
}