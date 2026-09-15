# Card 08 Verification

Card 08 application services are implemented on `feature/card-08-application-services`.

Run:

```bash
docker compose exec app php artisan test tests/Unit/Card08ApplicationServicesTest.php tests/Feature/Card08BindingsTest.php
```

Then:

```bash
docker compose exec app php artisan test
```

The local test suite must be run before merge. Telegram Router and HORTON1 are read-only references for this card and are not modified by this branch.
