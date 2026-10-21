# PROGRESS

## Phase status

| Phase | Status |
| --- | --- |
| 0 — Analysis (read-only) | ✅ Complete — `docs/ANALYSIS.md` written; approved |
| 1 — Scaffold & config | ✅ Complete — Laravel 13.38.0 + Filament 5.10.3 + packages; locales/middleware/config; MySQL `energea` DB migrated; tests green |
| 2 — Schema, models, import | ✅ Complete — 11 migrations, 10 models + enums/trait, 10 factories, `energea:import-preview` (9 services / 14 groups / 60 items / 8 sectors / 10 projects / 14 parties / 38 clients / 18 certificates) verified against MySQL and green in tests |
| 3 — Filament panel | ✅ Complete — Filament 5 resources (services + groups/items, projects + parties, sectors, clients, certificates), quote-request inbox, site-settings page, admin gate + `energea:make-admin`; 17 tests green; panel smoke-tested in browser |
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

## Key decisions (Phase 2)

- Bilingual JSON columns cast to `array`, read through `App\Models\Concerns\HasTranslations::translate()` (locale → `en` fallback → first value).
- Models use Laravel 13's `#[Fillable([...])]` attribute; enums: `PartyType` (client/contractor/consultant) and `QuoteRequestStatus` (new/read/handled).
- `projects.systems` is nullable — the preview's `sheybarah` project has no systems text; `sector_id` stays `null` after import (the preview data has no sector reference; sectors are admin-only categorisation).
- `energea:import-preview` parses the JSONC payload (object slice + line-comment strip), validates expected counts and EN/AR group/item parity, copies images byte-for-byte to the `public` disk, and upserts by natural keys (`slug`, `code`, `logo_code`, certificate page) so re-runs are idempotent. Preview-owned children (groups/items/parties) are synced with delete-extra.
- Party rows require both `name` and `src`; `jabal-omar`'s name-only party is skipped (14 importable parties, 13 distinct logo files because `logo-015.jpeg` is reused).
- `SiteSetting` is seeded once via `firstOrCreate(id: 1)` from the verified preview strings (footer, departments, offices, recipients); later admin edits are not overwritten by re-imports.
- Tests: `ContentFactoriesTest` (factories/enums/casts) and `ImportPreviewDataTest` (idempotent import with `Storage::fake`, missing-path failure, count-mismatch failure) — 8 tests / 101 assertions green; Pint clean.

## Key decisions (Phase 3)

- Panel resources live in `app/Filament/Resources/*` with separate `Schemas/*Form` + `Tables/*Table` files (Filament 5 generator structure); navigation: Content (services→projects→clients→certificates→sectors), Requests (quote requests), Settings (site settings).
- Bilingual editing uses EN/AR tabs built once in `App\Filament\Support\Bilingual` (Arabic fields get `dir="rtl"`); record titles are resolved through the `HasTranslations` trait.
- Images/logos upload to the `public` disk directories used by the importer (`services/`, `projects/`, `projects/parties/`, `clients/`, `certificates/`, `images/`), so imported and admin-uploaded files share the same paths.
- Service groups are edited through a relation manager whose item repeater is a Filament `relationship('items')` repeater (order column `sort_order`); project parties are a relation manager with a `PartyType` select + logo upload.
- Quote requests are read-only in the admin (no create/edit pages); status is changed inline via a `SelectColumn` (new/read/handled). The view page shows contact, message, attachment download, and mail delivery state.
- Site settings is a singleton Filament page (`SiteSetting::defaults()` shared with the importer); departments/offices are repeaters, quote recipients a tags input, social links intentionally not editable (see ASSUMPTIONS #20).
- Admin access: `User` implements `FilamentUser::canAccessPanel()` and requires `is_admin`; `energea:make-admin {email?} [--name=] [--password=]` promotes an existing user or creates one and prints the generated password. Non-admins get 403.
- Tests added: `AdminPanelTest` (guest redirect, non-admin 403, every panel page renders, make-admin command) and `FilamentFormsTest` (bilingual service save, relation managers render, group+items creation via action, site-settings save). Manual browser smoke test passed (login → dashboard → services list → service edit + relation manager).

## Next step

Phase 4: Blade conversion, public routes and i18n — layouts/partials, home/about/services/projects/clients/certificates/contact pages in EN/AR, asset copy from the preview, sitemap/SEO scaffolding as designed.
