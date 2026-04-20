<?php

namespace App\Services;

use App\Models\Account;
use App\Models\SystemSetting;

class PostingRuleService
{
    public function settlementAccount(): Account
    {
        $configuredId = $this->getInt('posting_rules.default_settlement_account_id');

        if ($configuredId) {
            $account = Account::query()->find($configuredId);

            if ($account) {
                return $account;
            }
        }

        return Account::query()
            ->where('code', '1100')
            ->first()
            ?? Account::query()->where('type', 'asset')->orderBy('code')->firstOrFail();
    }

    public function contraAccount(): Account
    {
        $configuredId = $this->getInt('posting_rules.default_contra_account_id');

        if ($configuredId) {
            $account = Account::query()->find($configuredId);

            if ($account) {
                return $account;
            }
        }

        $existing = Account::query()->where('code', '1199')->first();

        if ($existing) {
            return $existing;
        }

        return Account::query()->create([
            'code' => '1199',
            'name' => 'Asset Clearing Account',
            'type' => 'asset',
            'group_name' => 'valuables',
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        $settlementAccount = $this->settlementAccount();
        $contraAccount = $this->contraAccount();

        return [
            'settlement_account_id' => (int) $settlementAccount->id,
            'contra_account_id' => (int) $contraAccount->id,
            'settlement_account' => $settlementAccount,
            'contra_account' => $contraAccount,
        ];
    }

    public function update(int $settlementAccountId, int $contraAccountId): void
    {
        SystemSetting::query()->updateOrCreate(
            ['key' => 'posting_rules.default_settlement_account_id'],
            ['value' => (string) $settlementAccountId]
        );

        SystemSetting::query()->updateOrCreate(
            ['key' => 'posting_rules.default_contra_account_id'],
            ['value' => (string) $contraAccountId]
        );
    }

    private function getInt(string $key): ?int
    {
        $value = SystemSetting::query()->where('key', $key)->value('value');

        return is_numeric($value) ? (int) $value : null;
    }
}