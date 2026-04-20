# Accounting Rules Mapped to Chart of Accounts

This document maps the system posting rules to the Chart of Accounts (COA) used by finance-book.

## Posting Rule Accounts (System Settings)

- Settlement Account (default): `1100 - Cash / Bank`
- Contra Account (default): `1199 - Asset Clearing Account`

Notes:
- Settlement and contra are configurable from Admin Settings.
- Contra is used when the primary account matches settlement, to prevent posting both legs to the same account.

## Rule to COA Mapping

| Transaction Type | Typical Primary Account Type | Debit Leg | Credit Leg |
|---|---|---|---|
| `money_in` | Income | Settlement account (usually 1100) | Primary income account (e.g., 4100/4200) |
| `debts` | Liability recognition | Settlement account (usually 1100) | Primary liability account (e.g., 2100/2200) |
| `debt_payment` | Liability settlement | Primary liability account (e.g., 2100/2200) | Settlement account (usually 1100) |
| `money_out_direct` | COGS | Primary COGS account (e.g., 5100/5200/5300) | Settlement account (usually 1100) |
| `money_out_general` | Expense | Primary expense account (e.g., 6100/6200/6300/6400) | Settlement account (usually 1100) |
| `valuables` | Asset movement | Primary asset account (e.g., 1200/1210/1500/1600) | Settlement account (usually 1100) |

## Fallback / Contra Handling

When primary account equals settlement account:

- For `money_in` and `debts`:
  - Debit: contra account (`1199`)
  - Credit: primary account (same as settlement)
- For `money_out_direct`, `money_out_general`, and `valuables`:
  - Debit: primary account (same as settlement)
  - Credit: contra account (`1199`)
- For `debt_payment`:
  - Debit: primary liability account
  - Credit: contra account (`1199`) if primary equals settlement, otherwise settlement

## Seeded Chart of Accounts

### Assets
- `1100` Cash / Bank
- `1200` Inventory - Raw
- `1210` Inventory - Processed
- `1500` Machinery
- `1600` Vehicles
- `1199` Asset Clearing Account (system contra; created if missing)

### Liabilities
- `2100` Supplier Bills
- `2200` Statutory Payables (ZRA/NAPSA)

### Income
- `4100` Sales of Recycled Goods
- `4200` Collection Fees

### COGS
- `5100` Buying Waste
- `5200` Factory Wages
- `5300` Machine Power / Fuel

### Expenses
- `6100` Admin Salaries
- `6200` Marketing
- `6300` Repairs
- `6400` Depreciation

## Worked Examples

1. Sales recorded to 4100 (`money_in`) for K 1,250.00
   - Debit: 1100 Cash / Bank K 1,250.00
   - Credit: 4100 Sales of Recycled Goods K 1,250.00

2. Expense recorded to 6200 (`money_out_general`) for K 300.00
   - Debit: 6200 Marketing K 300.00
   - Credit: 1100 Cash / Bank K 300.00

3. Supplier bill payment on 2100 (`debt_payment`) for K 200.00
   - Debit: 2100 Supplier Bills K 200.00
   - Credit: 1100 Cash / Bank K 200.00

4. Asset move using settlement account itself (`valuables`, primary = 1100) for K 400.00
   - Debit: 1100 Cash / Bank K 400.00
   - Credit: 1199 Asset Clearing Account K 400.00

## Source of Truth in Code

- `app/Services/JournalPostingService.php` (account-pair resolution)
- `app/Services/PostingRuleService.php` (settlement/contra resolution and defaults)
- `database/seeders/DatabaseSeeder.php` (seeded COA)
- `database/migrations/2026_04_07_000012_create_system_settings_table.php` (posting-rule setting keys)
