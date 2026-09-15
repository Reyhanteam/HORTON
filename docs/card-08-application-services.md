# Card 08 — Application Services

Card 08 moves HORTON business rules out of Telegram controllers and infrastructure code.

## Boundaries

- `app/Contracts`: replaceable business integrations (pricing, discounts, payments, service providers).
- `app/DTOs`: immutable application inputs/results.
- `app/Services`: business workflows and domain-facing services.
- `app/Actions`: focused use cases such as discount redemption and referrals.
- `app/Events` and `app/Listeners`: application lifecycle integration.

Telegram routing remains the responsibility of `reyhanteam/laravel-telegram-bot-router`. No Telegram route or router code was changed by Card 08.

## Implemented

- Catalog pricing with active/default plan-price selection.
- Order creation with snapshot, invoice generation and transactional payment-state transition.
- Wallet ledger with row locking and insufficient-balance protection.
- Discount validation, limits and usage recording.
- Gift-code redemption with usage limits.
- Referral registration with duplicate protection.
- Replaceable payment-gateway contract and fake gateway.
- Replaceable service-provider contract and fake provider.
- Service lifecycle operations: create, renew, extend, capacity, disable, delete and status.
- Notification creation/read state.
- Support-ticket creation/closure.
- `UserRegistered` initialization event/listener for wallet, cashback and referral accounts.
- Application-container bindings for all replaceable contracts.

## Reliability rules

Application writes that affect balances, order state, discount usage or referral registration use database transactions and/or row locks where concurrent updates can change the result.

Payment and service integrations are abstracted behind contracts. Production providers can be added without changing Telegram controllers or order logic.

## Testing

Card 08 includes unit coverage for DTO calculations and fake integration contracts plus a Laravel feature test for application-container bindings. The full application test suite must still be executed in the project's normal Docker environment before merging.
