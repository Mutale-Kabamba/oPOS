# Finance Book System User Manual

Version: 1.1
Updated: 2026-04-07

## 1. Purpose
This manual explains how to use the Finance Book system for both supported roles:
- Admin
- Accountant

The system is used to:
- Record and manage financial transactions
- Review financial dashboards
- Generate PDF reports
- Manage users, accounts, categories, suppliers, and posting rules (Admin only)
- Track user activity (Admin only)
- Track supplier liabilities and record partial debt payments

## 2. Access and Security

### 2.1 Login
- Open the system login page.
- Enter your email and password.
- Select Sign In.

### 2.2 Forgot Password
- On the login page, select Forgot Password.
- Enter your account email.
- Open the reset link from your inbox and set a new password.

### 2.3 Email Verification
- Verification and resend routes exist in the system.
- Depending on deployment configuration, verification may or may not be required before normal use.

### 2.4 Account Active Status
- Only active users can access protected pages.
- If a user is deactivated, the system logs them out and blocks access.

## 3. Role Overview

### 3.1 Admin
Admin can:
- View Admin Dashboard with full-system totals
- View and edit all users' transactions
- Create/edit/delete transactions
- Manage Chart of Accounts (COA)
- Manage Categories
- Manage Suppliers
- Configure posting rules for settlement and contra accounts
- Manage Users (create, edit, deactivate, delete)
- Access Settings and User Activity logs
- Export activity and transaction PDFs
- Access all report screens

### 3.2 Accountant
Accountant can:
- View Accountant Dashboard with own monthly totals
- Create transactions
- Edit/delete own transactions
- View transaction reports (own records)
- Export transaction and financial report PDFs (scoped to own data)
- Review supplier balances and record supplier debt payments against own debts
- Reconcile own asset-account transactions
- Access and update own profile/password/photo

## 4. Main Navigation

The sidebar and mobile bottom navigation include:
- Dashboard
- Ledger
- Reports
- Settings

Additional sidebar widgets:
- Live Time
- Statutory Reminders (PAYE, Turnover Tax, NAPSA) with dynamic due-date status

## 5. Accountant User Guide

### 5.1 Accountant Dashboard
Screen: Dashboard (Accounting Workspace)

Key cards:
- Monthly Income
- Direct Costs (COGS)
- Operating Profit

Recent Transactions panel:
- Shows latest entries (preview list)
- Actions: Edit, Delete
- Buttons: Export PDF, View Full Log

### 5.2 Record New Entry
Path: Dashboard -> + Record Entry

Required fields:
- Transaction Type
  - Money In (Income)
  - Money Out (Direct/COGS)
  - Money Out (General/Expense)
  - Valuables (Asset)
  - Debts (Liability)
- Account
- Amount
- Date
- Payment Status
- Supplier for debt entries
- Optional description

How to save:
1. Fill all required fields.
2. Confirm values.
3. Select Save/Record.

System behavior:
- Entry is saved under your user account.
- Only matching ledger account types are shown for the chosen transaction type.
- Debt entries are always created with Payment Status = Pending.
- Debt payment status is updated automatically when partial or full payments are recorded.
- Audit log entry is created.

### 5.3 Edit Transaction
Path: Ledger or Dashboard Recent Transactions -> Edit

Rules:
- Accountant can edit only own transactions.
- Debt payment status cannot be edited directly on debt records.
- Payment child entries are created from the Suppliers Balance report rather than from the normal edit form.

How to edit:
1. Open Edit.
2. Update fields.
3. Save changes.

### 5.4 Delete Transaction
Path: Ledger or Dashboard Recent Transactions -> Delete

Behavior:
- Styled confirmation popup appears.
- Select Delete to continue, or Cancel to stop.

Rules:
- Accountant can delete only own transactions.

### 5.5 Ledger (Transaction Report)
Path: Ledger

Available tools:
- Search by description/account/user/amount-like values
- Filter by date range (From/To)
- Pagination for long lists
- Export PDF by type:
  - Full PDF
  - Income PDF
  - Expenses PDF

Columns:
- Date
- Account
- Type
- Amount
- Status
- User
- Actions (Edit/Delete where permitted)

### 5.6 Reports
Path: Reports

You can open:
- Income Statement
- Balance Sheet
- Trial Balance
- Suppliers Balance Report
- Sales Report
- Reconciliation Report

Income Statement includes:
- Total Income
- Direct Costs (COGS)
- Gross Profit
- General Expenses
- Net Profit
- Monthly breakdown summary

Balance Sheet includes:
- Total Valuables (Assets)
- Total Debts (Liabilities)
- Equity
- Equation check status

Trial Balance includes:
- Debit totals by account
- Credit totals by account
- Difference check

Suppliers Balance Report includes:
- Aging buckets by supplier
- Outstanding debt detail lines
- Original, paid, and remaining balances per debt
- Inline payment recording form for partial or full settlement

Sales Report includes:
- Total sales for the selected period
- Daily summary
- Monthly summary
- Category breakdown
- User breakdown

Reconciliation Report includes:
- Asset account selector
- Uncleared movement list
- Cleared and uncleared balances
- Statement ending balance variance check
- Reconcile action for selected rows

Both support PDF export.

### 5.7 Supplier Debt Payments
Path: Reports -> Suppliers Balance Report

Use this screen to settle supplier debts in stages.

Workflow:
1. Open Suppliers Balance Report.
2. Review the Outstanding Supplier Debts table.
3. Locate the supplier debt line you want to settle.
4. Enter payment date and amount.
5. Optionally add a payment note.
6. Select Record.

System behavior:
- The system creates a linked payment entry under the original debt.
- If the payment is less than the remaining balance, the debt becomes Partially Paid.
- If the payment clears the remaining balance, the debt becomes Paid.
- You cannot record a payment greater than the remaining balance.
- The aging totals are recalculated from the remaining balance, not the original debt amount.

### 5.8 Profile Management
Path: Settings -> Profile

You can:
- Update name and email
- Upload profile photo (PNG/JPG/WEBP, max 2MB)
- Change password
- Delete your own account (password confirmation required)

## 6. Admin User Guide

### 6.1 Admin Dashboard
Screen: Dashboard (Admin Console)

Key cards:
- Total Income (system-wide)
- Direct Costs (COGS)
- Operating Profit

Quick actions:
- + Add Rule (Category create)
- Manage COA

Recent Transactions:
- Shows latest entries (preview list)
- Actions: Edit, Delete
- Buttons: Export PDF, View Ledger

### 6.2 Ledger and Reports
Path: Ledger / Reports

Admin permissions:
- View all transactions from all users
- Edit/delete any transaction
- Generate all report PDFs
- View all supplier liabilities and payment progress
- Reconcile any eligible transactions

### 6.3 Settings Hub
Path: Settings

Cards include:
- Chart of Accounts
- Categories
- Suppliers
- User Management
- Profile

Posting Rules section:
- Settlement Account
- Contra Asset Account
- Save Posting Rules action

Recent User Activity section:
- Shows latest audit activity
- Export User Activity PDF

### 6.4 Chart of Accounts (COA)
Path: Settings -> Chart of Accounts

Admin can:
- Create account
- Edit account
- Activate/deactivate account
- Delete account (only when no transactions exist)

Account fields:
- Code (unique)
- Name
- Type: asset, liability, income, cogs, expense
- Group: valuables, debts, money_in, direct_costs, general_costs
- Active status

### 6.5 Categories
Path: Settings -> Categories

Admin can:
- Create category
- Edit category
- Delete category

Category fields:
- Name
- Type: income or expense
- Description (optional)

### 6.6 Suppliers
Path: Settings -> Suppliers

Admin can:
- Create supplier
- Edit supplier
- Activate/deactivate supplier
- Search suppliers by name/contact/details
- Filter suppliers by status
- Browse paginated supplier lists

Supplier fields:
- Name
- Contact Person
- Phone
- Email
- Address
- Notes
- Active status

### 6.7 Posting Rules
Path: Settings -> Posting Rules

Purpose:
- Controls the default settlement account used in journal postings.
- Controls the contra asset account used when the primary account matches settlement.

Admin can:
- Change the settlement account
- Change the contra asset account
- Save updates without code changes

### 6.8 User Management
Path: Settings -> User Management

Admin can:
- Create users
- Edit users
- Deactivate users
- Delete users

Rules:
- Role must be admin or accountant.
- Admin cannot delete own account from User Management screen.
- Deactivated users are blocked on next request and logged out.

### 6.9 Activity Monitoring
Path: Settings -> Recent User Activity

Activity data includes:
- Date/time
- User
- Description/action

Exports:
- User Activity Report PDF

## 7. Confirmation Popup Behavior

For delete/deactivate actions, the app uses a styled confirmation modal.

Modal features:
- Title and message based on action
- Variant color (danger/warning)
- Primary confirm button and Cancel
- Click outside or Esc to close

## 8. PDF Export Behavior

Supported export areas:
- Transaction reports
- Income statement
- Balance sheet
- Trial balance
- Suppliers balance report
- Sales report
- Reconciliation report
- User activity report

Footer branding:
- Logo + Powered By: Ori Studio Limited
- KT4C System V1.26.1
- Standardized footer layout across reports

Note:
- Hover effects in footer links may not appear in all PDF viewers.

## 9. Data and Access Rules Summary

- Accountant data scope:
  - Dashboard totals: own monthly records only
  - Transaction list/report scope: own records only
  - Edit/delete: own records only
  - Supplier balance and reconciliation scope: own eligible records only

- Admin data scope:
  - Full-system view
  - Can manage all transactions, users, accounts, categories, suppliers, and posting rules

## 10. Common Tasks Quick Reference

### 10.1 Accountant: Record a transaction
1. Dashboard -> + Record Entry
2. Complete fields
3. Save

### 10.2 Accountant/Admin: Export filtered transaction report
1. Ledger
2. Set search/date filters
3. Select report type
4. Export PDF

### 10.3 Admin: Create a new user
1. Settings -> User Management -> Create
2. Enter user details and role
3. Save

### 10.4 Accountant/Admin: Record a supplier debt payment
1. Reports -> Suppliers Balance Report
2. Find the outstanding debt row
3. Enter payment date and amount
4. Select Record

### 10.5 Admin: Maintain posting rules
1. Settings
2. Open Posting Rules section
3. Select settlement and contra accounts
4. Save changes

### 10.6 Admin: Deactivate a user
1. Settings -> User Management
2. Select Deactivate
3. Confirm in modal

### 10.7 Admin: Add COA account
1. Settings -> Chart of Accounts -> Create
2. Enter code/name/type/group
3. Save

## 11. Troubleshooting

### 11.1 "Your account has been deactivated"
Cause:
- User was set to inactive by admin.

Resolution:
- Ask an admin to reactivate the user.

### 11.2 Cannot delete account in COA
Cause:
- Account has existing transactions.

Resolution:
- Keep account and deactivate, or reclassify related data before deletion.

### 11.3 No transactions shown in accountant report
Cause:
- Filters/date range do not include your records, or no records exist.

Resolution:
- Widen date range and clear search text.

### 11.4 Cannot record supplier payment
Cause:
- The amount entered is greater than the remaining balance.
- The selected debt has already been fully paid.

Resolution:
- Refresh the Suppliers Balance Report.
- Check the Remaining column.
- Enter an amount less than or equal to the remaining balance.

### 11.5 PDF looks different across devices
Cause:
- PDF viewers differ in CSS support.

Resolution:
- Open with a standards-compliant viewer and regenerate if needed.

## 12. Best Practices

- Use specific descriptions when recording entries.
- Record supplier payments from the Suppliers Balance report so debt status stays accurate.
- Reconcile entries daily to keep dashboards accurate.
- Keep user roles minimal (least privilege).
- Deactivate users instead of deleting where historical integrity matters.
- Export key reports regularly for offline audit records.

## 13. Admin Handover Checklist

When onboarding a new user:
1. Create user with correct role.
2. Verify login works.
3. Confirm user can access expected menus only.
4. Ask user to set/update password and profile.
5. Confirm first transaction entry and report export.

---
End of manual.
