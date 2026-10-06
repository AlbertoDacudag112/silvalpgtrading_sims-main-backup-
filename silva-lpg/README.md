# Silva LPG Trading — Setup Notes

Drop these folders into your existing Laravel Breeze project (they mirror your app's structure —
`app/`, `resources/`, `routes/`) and overwrite the matching files.

## 1. Register the `role` middleware

Your `EnsureUserHasRole` class is included, but it still needs to be registered so `->middleware('role:admin')` works.

**Laravel 11+** — in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
})
```

**Laravel 10** — in `app/Http/Kernel.php`, inside `$routeMiddleware`:
```php
'role' => \App\Http\Middleware\EnsureUserHasRole::class,
```

## 2. Storage link (for delivery proof photos)

```bash
php artisan storage:link
```
Without this, `asset('storage/...')` in the order-show and delivery-show views will 404.

## 3. Demo accounts (already in your `silvalpg_db.sql` dump)

| Role     | Email                | Password (seeded hash — reset if unknown) |
|----------|-----------------------|---------------------------------------------|
| Admin    | owner@silvalpg.com    | *(bcrypt hash in dump — use `php artisan tinker` to reset if needed)* |
| Cashier  | cashier@silvalpg.com  | same as above |
| Delivery | delivery@silvalpg.com | same as above |

To reset a password quickly:
```bash
php artisan tinker
>>> \App\Models\User::where('email','owner@silvalpg.com')->update(['password' => bcrypt('password')]);
```

## 4. What was fixed / added

- **Model files** — the previous zip created the `app/Models/` folder but didn't actually write the
  `.php` files into it. If your live `User.php` doesn't already define `homeRoute()`, that's exactly why
  `Auth::user()->homeRoute()` in `routes/web.php` throws "Call to undefined method." All 8 models
  (`User`, `Customer`, `Product`, `Order`, `OrderItem`, `InventoryLog`, `Expense`, `DeliveryProof`) are
  now included — just overwrite your `app/Models/` folder with these.
- **Cashier `OrderController.php` and `ExpenseController.php`** — your pasted code had the two files
  concatenated together (the end of `ExpenseController::store()` was glued mid-string into
  `OrderController`). Both are rewritten clean here.
- **`layouts/app.blade.php`** — new left-sidebar shell (collapsible on mobile via Alpine.js), maroon/white
  theme, flash message banners (green/yellow/red), used by every dashboard/page via `<x-app-layout>`.
- **`layouts/partials/nav-*.blade.php`** — per-role sidebar links with active-state highlighting.
- **`components/stat-card.blade.php`, `components/badge.blade.php`** — the two components your existing
  views already reference but that didn't exist yet.
- **All missing views**: order create (with a live-calculating line-item builder that applies your
  cylinder-exchange rule — own cylinder = gas price only, no cylinder = +₱1,500), order list/detail with
  assign-to-delivery and cancel actions, inventory list/create/restock/logs, expense tracker, and a
  reports page with Chart.js (sales vs expenses trend, payment-method split, expenses by category).
- **Delivery order-show** — photo upload form that's *required* to mark an order "completed", matching
  your delivery-staff's sole responsibility in the business process.
- **`OrderService::cancelOrder`** — simplified one confusing inline expression
  (`$newStock - ($newStock - $item->quantity)`) to just `$item->quantity`; same value, clearer to read.
- **Payment rules enforced end-to-end**: `payment_method` is `cash`/`gcash` only at the validation layer,
  the controller layer, and the DB enum; `is_paid` is always `true` at order creation (no partial/down
  payment path exists anywhere in the UI or backend, matching your feasibility study).
- **Stock rules enforced**: `reorder_level` defaults to 20, `max_capacity` defaults to 50 (both editable
  per product), restocks are capped at `max_capacity`, and low-stock products get a yellow banner on every
  inventory view + the dashboards.

## 5. About the "w3.org" concern

`xmlns="http://www.w3.org/2000/svg"` inside your inline `<svg>` icons is just an XML namespace
declaration — it's not a live network request, so it doesn't need internet access and isn't an actual
error. If your editor/browser devtools flagged it, that's a false positive from a linter, not a real bug.

## 6. Premade accounts + delivery staff management (new)

- **`database/seeders/UserSeeder.php`** seeds the two *permanent* accounts:
  - `owner@silvalpg.com` / `Owner@12345` (admin)
  - `cashier@silvalpg.com` / `Cashier@12345` (cashier)
  Run `php artisan db:seed` (or `--class=UserSeeder`) after migrating. **Change these passwords
  before going live.**
- **Delivery staff are not seeded and cannot self-register.** `routes/auth.php` here has the
  `register` routes removed entirely — the only way a delivery account exists is if the Owner
  creates one from **Admin → Delivery Staff** (`Admin\DeliveryStaffController`):
  - **Create**: name, email, phone, temporary password — owner shares it with the staff member directly.
  - **Edit**: update details, reset password, or flip Active/Deactivated (deactivated = blocked from logging in).
  - **Delete**: permanently removes the account. Blocked with a validation error if the staff member
    currently has an order `out_for_delivery` (reassign it first); on delete, their `assigned_delivery_id`
    is nulled on past orders so delivery history isn't lost, then the account itself is removed.
- **`app/Http/Requests/Auth/LoginRequest.php`** — added an `is_active` check at the point of login
  (not just after), so a deactivated delivery account is rejected immediately with the same generic
  "these credentials don't match" message a wrong password gets (doesn't leak whether the email exists).
- You'll also want to remove the "Already registered? Register" link from Breeze's default
  `login.blade.php` / guest layout, since the `register` route no longer exists — leaving the link in
  will just 404.
- The `RegisteredUserController` Breeze generated is now unused; you can leave it (harmless dead code)
  or delete it.

## 7. Not included here

Migrations and seeders aren't included since your `silvalpg_db.sql` dump shows the schema is already
migrated and seeded with demo data — just import that dump (or keep your existing DB) and these files
will work against it as-is.
