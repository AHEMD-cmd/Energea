# PROGRESS

## Phase status

| Phase | Status |
| --- | --- |
| 0 — Analysis (read-only) | ✅ Complete — `docs/ANALYSIS.md` written; approved |
| 1 — Scaffold & config | ✅ Complete — Laravel 13.38.0 + Filament 5.10.3 + packages; locales/middleware/config; MySQL `energea` DB migrated; tests green |
| 2 — Schema, models, import | ⏳ Pending |
| 3 — Filament panel | ⏳ Pending |
| 4 — Blade conversion, routes, i18n | ⏳ Pending |
| 5 — Quote form, uploads, mail | ⏳ Pending |
| 6 — SEO, performance, security, backup, docs | ⏳ Pending |
| 7 — Verification & final report | ⏳ Pending |

## Key decisions (Phase 0)

- Frontend source lives at `D:\laravel\Energea-Frontend` (outside the Laravel app; never modified).
- Bilingual records: array-cast JSON `{en,ar}` columns + small `HasTranslations` trait (no spatie/laravel-translatable).
- Laravel: latest stable that supports PHP 8.3 — the brief's "laravel 10" line conflicts with its own "latest stable, do not pin from memory" rule; resolved in favour of latest stable (installed 13.x), logged in ASSUMPTIONS.md.
- Filament: latest stable major for that Laravel; docs of the installed major consulted before coding.
- Import: idempotent `energea:import-preview` reading JSONC `data.js`; images copied unchanged to the public disk; Filament uploads get WebP/responsive treatment only on save.
- Contact/footer phone + email values come from SiteSetting (grep rule: no hard-coded phones/emails in views).
- Certificates seeded with original image bytes (contract: supplied images unchanged).

## Key decisions (Phase 1)

- Laravel **13.38.0** + Filament **5.10.3** (both latest stable; docs of installed majors consulted before Filament coding in Phase 3).
- Packages: `spatie/laravel-backup` ^10.3 (config published), `laravel-lang/lang` ^15.37 (`lang:add ar` installed), `intervention/image` ^4.4, `laravel/boost` (dev tooling from the skeleton's AGENTS.md).
- `ext-zip` enabled in Laragon `php.ini` (hard requirement of spatie/laravel-backup; local machine change).
- Local dev DB: Laragon MySQL 8.4.3 (started, root/no password), database `energea` utf8mb4; tests use SQLite `:memory:`.
- `config/energea.php` (locales, admin_path, frontend_path) + `config/quote.php` (cities, throttles, attachment rules, ClamAV flag) + Turnstile keys in `config/services.php`.
- Middleware appended to web group: `SetLocale` (route locale → session → default, `URL::defaults(['locale'])`), `ForceHttps` (production, honours `X-Forwarded-Proto`), `SecurityHeaders` (nosniff, frame SAMEORIGIN/DENY on admin, referrer-policy, permissions-policy, `X-Robots-Tag: noindex` outside production). The Filament panel stack gets `ForceHttps` + `SecurityHeaders` too.
- `config/app.php` timezone from `APP_TIMEZONE` (Asia/Riyadh in both env files); `Model::preventLazyLoading()` outside production.
- Verified: `/up` 200, `/` 200 with headers, `/admin` 302 → login; `php artisan test` green after Pint.

## Next step

Phase 2: migrations/models/factories for the full schema, `HasTranslations` trait, observers, `energea:import-preview` command (JSONC `data.js` → DB + images), seeders, smoke-run the importer.
