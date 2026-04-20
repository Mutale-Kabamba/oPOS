<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Map UI transaction type to account type.
     *
     * @var array<string, string>
     */
    public const TYPE_MAP = [
        'money_in' => 'income',
        'money_out_direct' => 'cogs',
        'money_out_general' => 'expense',
        'valuables' => 'asset',
        'debts' => 'liability',
    ];

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'transaction_type' => ['required', Rule::in(array_keys(self::TYPE_MAP))],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'payment_status' => ['required', Rule::in([
                Transaction::PAYMENT_STATUS_PENDING,
                Transaction::PAYMENT_STATUS_PARTIALLY_PAID,
                Transaction::PAYMENT_STATUS_PAID,
            ])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $transactionType = (string) $this->input('transaction_type');
            $expectedType = self::TYPE_MAP[$transactionType] ?? null;
            $accountId = (int) $this->input('account_id');

            if (! $expectedType || ! $accountId) {
                return;
            }

            $account = Account::query()->find($accountId);

            if (! $account) {
                return;
            }

            if ($account->type !== $expectedType) {
                $validator->errors()->add(
                    'account_id',
                    'Selected ledger account does not match the chosen transaction type.'
                );
            }

            $supplierId = $this->input('supplier_id');

            if ($expectedType === 'liability' && empty($supplierId)) {
                $validator->errors()->add('supplier_id', 'A supplier is required for debt transactions.');
            }

            if ($expectedType !== 'liability' && ! empty($supplierId)) {
                $validator->errors()->add('supplier_id', 'Suppliers can only be assigned to liability transactions.');
            }

            if ($expectedType !== 'liability' && $this->input('payment_status') === Transaction::PAYMENT_STATUS_PARTIALLY_PAID) {
                $validator->errors()->add('payment_status', 'Partially paid is only available for debt transactions.');
            }

            if (! empty($supplierId)) {
                $supplier = Supplier::query()->find((int) $supplierId);

                if (! $supplier || ! $supplier->is_active) {
                    $validator->errors()->add('supplier_id', 'Selected supplier is not available.');
                }
            }
        });
    }

    public function expectedAccountType(): string
    {
        return self::TYPE_MAP[(string) $this->input('transaction_type')];
    }
}
