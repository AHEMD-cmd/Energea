# Phase 0 — Analysis (Energea backend)

Read-only inspection of the static preview at `D:\laravel\Energea-Frontend` (the brief calls it `./frontend-source/`; the actual folder on this machine is `D:\laravel\Energea-Frontend` — it is outside the Laravel app and will not be modified). Verified against `BACKEND-HANDOFF.md`, all 19 HTML files, `assets/js/data.js`, `assets/js/main.js` and `assets/css/style.css`.

## 1. Confirmed facts (re-verified)

| Claim | Result |
| --- | --- |
| 9 pages × EN/AR + root language chooser | Confirmed: `en|ar` × index, about, services, service, projects, project, clients, certificates, contact + root `index.html` (meta-refresh + JS redirect + language chooser) |
| EN/AR identical tag/class/id/name structure | **Verified programmatically** for all 9 pairs (tag+class+id+name sequences are byte-identical; only text/`lang`/`dir`/head-metadata differ) |
| `data.js` catalog counts | services=9, serviceDetails=9, projects=10, sectors=8, certifications=18, clients=38 — all confirmed; every referenced image exists on disk |
| EN/AR group & item counts match per service | Confirmed for all 9 services; steps/facts present only for `air-duct-cleaning`, `hvac-inspection`, `hvac-performance-testing` (3 steps + 4 facts each); the other 6 fall back to defaults hard-coded in `main.js` |
| Project parties | Legacy `client` key exists on 9/10 projects (ignore it). Importable parties (name **and** logo): 14 total — `makkah-haram` 3, `king-khalid-airport` 1, `king-abdulaziz-airport` 1, `damac-data-center` 2 (consultant SYSKA is name-only → skip), `yamama-cement` 1, `kaust` 2, `new-murabba` 3, `stc-data-center` 1. `sheybarah` and `jabal-omar` have **no** importable parties (all name-only) |
| `data.js` is JSONC | Contains `//` comments — the importer must strip line comments before `json_decode` |
| 140 `[dir=rtl]` CSS rules | Confirmed (140 matches); RTL fully driven by `<html dir="rtl">` |
| 8 `.logo-card-NNN` rules | Confirmed — codes 000, 004, 011, 036 (scale 1.32–1.55) and 014, 028, 039, 046 (scale 1.12). Client tiles must keep `logo-card-{logo_code}` |
| `body[data-page]` rules | Confirmed — only `data-page="clients"` (final-cta sizing); body also carries `data-language` and (home only) class `home-refined`; project detail adds class `project-detail-page` |
| Services listing spanning card | `main.js`: last card gets `service-listing-featured` (col-xl-12) only when `count % 3 == 1`. With 9 services nothing spans — reproduce the rule anyway |
| Projects pagination | 5 per page; markup = previous/next circular buttons + numbered pages, `aria-current="page"`, localized aria labels; chevrons swapped in AR (`chevronRight` for previous); a later CSS rule neutralises the earlier rotate, so render exactly what `main.js` builds |
| Quote form | Fields `name, company, email, phone, service` (slug), `location` (city key or `other`), `location_other`, `message`, `attachment` (accept pdf/doc/docx/xls/xlsx/dwg/dxf/jpg/jpeg/png/zip), honeypot `website`, `data-preview-only="true"` + `novalidate` in preview (removed in Blade), verification panel markup (math captcha + refresh button), `#quote` anchor on the section |
| Contact data (verbatim) | Electrical Testing: `+966 54 452 0490`, `Nader@energea.me`. Operations & Maintenance: `+966 55 629 4274`, `mubarak.rashid@energea.me`. General email: `hello@energea.me`. Offices: Riyadh / Jeddah / Al Khobar with `google.com/maps/search/?api=1&query=...` links. Footer direct lines = the two mobile numbers + general email |
| City list | `riyadh, jeddah, khobar, makkah, madinah, dammam, other` (EN: Riyadh, Jeddah, Al Khobar, Makkah, Madinah, Dammam, Other location) |
| Fonts | Local Inter (5 weights) + Tajawal (4 weights) woff2, `font-display: swap`; body uses Inter, `html[dir=rtl] body` uses Tajawal |
| Home-only preload | `field-panel-desktop.jpeg` preloaded with `fetchpriority="high"`; detail heroes also use `fetchpriority="high"` |
| Favicons | `favicon.ico` (16/32/48), `favicon.svg`, `apple-touch-icon.png` (180) at preview root; `social-icon-preview.jpg` is the 1200×630 OG image |
| Social media links | **None exist** in the preview (no Twitter/LinkedIn/etc. links anywhere; only OG/Twitter meta tags). No design slot for them |

### Differences from the brief found in Phase 0

1. **Frontend path** — brief says `./frontend-source/`; actual location is `D:\laravel\Energea-Frontend` (the brief also states this at the end). Handled; logged in ASSUMPTIONS.md.
2. **`data-limit` variants on Home/About do not exist** — the only data-driven grids are `#certList[data-certificates]` on the certificates page and `.clients-grid[data-clients]` on the clients page, both without `data-limit`. Home and About contain no certificate/client blocks. The Blade partials will still accept an optional `limit` (cheap, matches the JS capability), but no page uses it. Logged in ASSUMPTIONS.md.
3. **`data.js` contains JS comments** — not pure JSON; importer must strip `//` lines.
4. **Brief's final line says "make laravel 10 project"** while the stack section demands "latest stable Laravel … do not pin versions from memory" (Laravel 10 is EOL; Filament 4 does not support it). Resolution: latest stable Laravel (12.x on PHP 8.3) + matching Filament; logged in ASSUMPTIONS.md.

## 2. Route map

All public routes are locale-prefixed, constrained `where('locale', 'en|ar')`, and implemented in controllers (no closures). `URL::defaults(['locale' => …'])` set by a locale middleware so `route()` calls omit the locale parameter.

| Method/URL | Name | Controller action |
| --- | --- | --- |
| `GET /` | — | 301 → `/en` (locale from session/cookie if previously chosen, else `en`) |
| `GET /{locale}` | `home` | `HomeController@index` |
| `GET /{locale}/about` | `about` | `PageController@about` |
| `GET /{locale}/services` | `services.index` | `ServicesController@index` |
| `GET /{locale}/services/{slug}` | `services.show` | `ServicesController@show` (404 if unknown/unpublished) |
| `GET /{locale}/projects` | `projects.index` | `ProjectsController@index` (5/page, `?page=n`) |
| `GET /{locale}/projects/{slug}` | `projects.show` | `ProjectsController@show` |
| `GET /{locale}/clients` | `clients` | `ClientsController@index` |
| `GET /{locale}/certificates` | `certificates` | `CertificatesController@index` |
| `GET /{locale}/contact` | `contact` | `ContactController@show` |
| `POST /{locale}/contact` | `contact.store` | `ContactController@store` (throttled 5/min + 20/h per IP) |
| `GET /{locale}/contact/captcha` | `contact.captcha` | `ContactController@captcha` (new math question JSON) |
| `GET /sitemap.xml` | `sitemap` | `SeoController@sitemap` |
| `GET /robots.txt` | `robots` | `SeoController@robots` |
| `/admin` | Filament panel | from env `ADMIN_PATH` |

Legacy preview URLs (301):

- `/{locale}/index.html` → `/{locale}`; root `/index.html` → `/en`
- `/{locale}/about|services|projects|clients|certificates|contact.html` → clean route
- `/{locale}/service.html?slug=x` → `/{locale}/services/x` (missing/unknown slug → `/{locale}/services`)
- `/{locale}/project.html?slug=x` → `/{locale}/projects/x` (unknown → `/{locale}/projects`)
- `/{locale}/projects.html?page=n` → `/{locale}/projects?page=n`
- Language switch = same named route + params with `locale` swapped, preserving `?page=`; works on detail pages (slug kept).

One slug per record, shared by EN/AR. Unpublished or unknown detail record → localized 404 page.

## 3. View / partial map

```
resources/views/
  layouts/app.blade.php            html[lang,dir], body[data-page][data-language] (+ extra classes), header/footer includes, scripts
  partials/head.blade.php          $seo array: title, description, canonical, alternates{en,ar}, image_url + favicons, fonts preload, css, GA4, GSC
  partials/header.blade.php        scroll-progress + site-nav (from main.js header()); active link by route name
  partials/footer.blade.php        site-footer + floating-contact (from main.js footer()); dynamic © year
  partials/icon.blade.php          <x-icon name="..."/> — all 23 SVG paths from main.js, exact attributes
  partials/service-card.blade.php  listing card (number, image, icon by index, type/title/summary, featured span rule)
  partials/project-card.blade.php  project-stack-item (alternating is-reverse, index number, scope/systems dl)
  partials/sector-card.blade.php   home sector tile (code / NN, icon by index)
  partials/certificate-card.blade.php  document-card, opens image in new tab
  partials/client-card.blade.php   client-logo tile with logo-card-{code}
  partials/project-parties.blade.php   parties strip (client/contractor/consultant groups)
  partials/quote-banner.blade.php  end-of-list projects quote banner
  partials/pagination.blade.php    custom paginator view (markup from main.js, RTL chevrons)
  pages/{home,about,services,service-detail,projects,project-detail,clients,certificates,contact}.blade.php
  components/icon.blade.php        (implementation of <x-icon>)
  errors/404.blade.php             localized
  auth/… Filament default views (published only if needed)
```

Every visible string goes through `__()`/`@lang`; DB content through `{{ }}` only; `{!! !!}` only for lang-file strings that intentionally contain markup (e.g. `<br><em>` inside display titles). `dir="ltr"` spans kept around phones/emails. `main.js` is trimmed to UI-only behaviour (reveal, counters, progress, parallax, cursor, nav collapse, file-name label, other-location toggle, Turnstile/math form helpers); header/footer/listing/detail renderers, `data.js` dependency, preview submit interception and client-side captcha logic are removed. `data.js` is never loaded.

## 4. Translation-key plan

`lang/en/*.php` + `lang/ar/*.php` (copy extracted verbatim from the HTML — no rewriting). Arabic framework/validation strings via `laravel-lang/lang`.

| File | Content |
| --- | --- |
| `common.php` | Shared CTAs ("Request a quote"/"اطلب عرض سعر", "Talk to an engineer", "Energea", arrow labels), 404 copy, generic labels |
| `nav.php` | Nav items, brand aria-label, menu aria-labels, lang-switch aria-label |
| `footer.php` | Footer headings and lines (Company/Explore/Offices/Direct lines), office lines "Saudi Arabia"/"Jeddah · Riyadh · Khobar", bottom "Saudi Arabia · GCC"; brand paragraph comes from SiteSetting (seeded with the verbatim copy) |
| `home.php` | Hero, positioning, metrics (incl. `data-count` values), why-cards, industries head, quote CTA + SEO title/description |
| `about.php` | Full about copy (hero, snapshot, mission/vision/values, metrics) + SEO |
| `services.php` | Listing head + SEO |
| `service.php` | Detail labels (SERVICE OVERVIEW, Scope/Systems/Works/Outputs, rule-list), **default steps/facts** (the six fallback texts from `main.js`) + generic detail SEO fallback |
| `projects.php` | Listing head, pagination aria labels, quote banner, SEO |
| `project.php` | Detail labels (PROJECT RECORD, Client/Contractor/Consultant, Location/Year/Our scope/Systems, parties aria) |
| `clients.php` / `certificates.php` | Page copy + SEO |
| `contact.php` | Form labels, select placeholder texts, city labels, verification panel, upload labels, success/validation strings + SEO |
| `validation.php`, `attributes.php`, `pagination.php`, `auth.php`, `passwords.php` | From laravel-lang (en + ar) |

City keys/order in `config('quote.cities')`; labels in `contact.php`. Static-page titles/descriptions copied from the 18 `<title>`/`meta name="description"` tags.

## 5. Database schema (MySQL/MariaDB-compatible, SQLite for tests)

All content tables: `id`, `is_published` (bool, default true), `sort_order` (int), timestamps. `{en,ar}` = JSON column `{"en": …, "ar": …}`.

- **services**: slug (unique), number ("01"), image, title, type, summary, overview, steps (JSON `{en:[≤3], ar:[≤3]}` or null), facts (JSON `{en:[≤4], ar:[≤4]}` or null), seo_title, seo_description
- **service_groups**: service_id FK cascade, title {en,ar}, sort_order
- **service_group_items**: service_group_id FK cascade, text {en,ar}, sort_order
- **sectors**: code (unique, e.g. "AV"), title {en,ar}, text {en,ar}
- **projects**: slug (unique), sector_id FK nullOnDelete, image, year (string), title, location, scope, systems, seo_title, seo_description
- **project_parties**: project_id FK cascade, party_type enum `client|contractor|consultant`, name {en,ar}, logo, sort_order — public only renders when name (current locale, fallback en) **and** logo exist
- **clients**: name ({en required, ar optional → fallback en}), logo, logo_code (string "000", kept from preview; auto-generated 3-digit sequence for new records; drives `.logo-card-NNN` CSS)
- **certificates**: name ({en required, ar optional}), type {en,ar}, image (certificate/document image, seeded unchanged)
- **quote_requests**: locale, name, company, email, phone, service_id (FK null), location_key, location_other, message, attachment_path (private disk), attachment_original_name, status enum `new|read|handled`, mail_sent_at, mail_error (text, nullable), timestamps. No IP storage.
- **site_settings**: singleton row (id=1): company_name, footer_text {en,ar}, general_email, quote_recipients (JSON array of emails), departments (JSON array `{name:{en,ar}, phone, email}`), offices (JSON array `{name:{en,ar}, map_url}`), social_links (JSON, stored only — see ASSUMPTIONS), og_image (default OG image path), ga4_measurement_id, search_console_token
- **users**: Laravel defaults + `is_admin` bool; Filament `canAccessPanel` = active + is_admin; first admin via `php artisan energea:make-admin` (no credentials in repo)

Bilingual approach: **array-cast JSON columns** (no spatie/laravel-translatable) — binds directly to Filament tab/repeater state; shared trait `HasTranslations` with `translate('field')` accessor falling back to English.

Caching: published catalogs (services+groups+items, sectors, projects+parties, clients, certificates, site settings) cached via `Cache::remember` with keys flushed by model observers (also flush sitemap-related cache). `Model::preventLazyLoading()` outside production; eager loads in controllers.

## 6. Filament resource list (panel `/admin`)

| Resource / page | Notes |
| --- | --- |
| Services | Slug auto from EN title (unique, editable, helper text about URL change); number auto-filled from position (editable); image upload; tabs EN/العربية (dir=rtl) for title/type/summary/overview + SEO fields; groups Repeater → items Repeater; steps/facts Repeaters (≤3/≤4); publish toggle; drag-reorder by sort_order |
| Projects | Slug, year, image, sector select (nullable), parties Repeater (party_type select, bilingual name, logo); publish toggle; reorder |
| Sectors | code, bilingual title/text; publish; reorder |
| Clients | bilingual name (EN required), logo upload, logo_code (auto-generated, editable); publish; reorder |
| Certificates | name (EN required), bilingual type, image (seeded unchanged); publish; reorder |
| Quote Requests | table (status badge, mail_error flag), view page, mark-read/mark-handled actions, delete; **no create**; nav badge = unread count |
| Users | admin only |
| Site Settings | singleton Filament page: company name, footer text, emails, departments repeater, offices repeater, social links, OG image, GA4 ID, GSC token |

Reusable field helper: `BilingualField`/`TranslatedTextInput` (EN + AR side-by-side or in tabs; AR input `dir="rtl"`). Image fields: jpg/png/webp, size limit, converted to WebP + responsive widths on save via Intervention Image v3 (fallback: own GD wrapper if incompatible); old files deleted on replace/delete. Bilingual alt text derived from title with optional override (services/projects). Reorder: Filament drag-and-drop writing `sort_order`. Observers flush caches.

## 7. Import mapping (`php artisan energea:import-preview`, idempotent)

Reads `D:\laravel\Energea-Frontend\assets\js\data.js` (path configurable via `config('energea.frontend_path')`), strips `//` comments, `json_decode`s, then upserts by slug/code (updateOrCreate — re-running changes nothing):

- `services[i]` → Service (sort_order=i, is_published=true) + detail from `serviceDetails[slug]`: overview, groups (EN/AR arrays zipped by index into ServiceGroup + items — **fail loudly if group/item counts diverge**), steps/facts (only when present; else null → Blade uses lang defaults). Image copied unchanged to the `public` disk (`services/{slug}.{ext}`).
- `projects[i]` → Project + ProjectParties from `clients|contractors|consultants` arrays, **only entries with both `name` and `src`** (the legacy single `client` key is ignored); image → `projects/{slug}.webp|…` unchanged.
- `sectors[i]` → Sector (code kept).
- `clients[i]` → Client (name, logo_code = `c[1]`, ext default `png`; logo → `clients/logo-{code}.{ext}` unchanged; sort_order=i).
- `certifications[i]` → Certificate (image `assets/images/certificates/certificate-{page}.{format|jpg}` copied unchanged — required to stay unchanged).
- SiteSetting seeded once with the verbatim footer/contact values (phones, emails, office names + map URLs, footer brand text, OG image = `social-icon-preview.jpg`).
- Verification after import: counts 9/8/10/38/18, EN/AR group+item parity per service, all image files present; non-zero exit + message on any failure.

## 8. SEO, performance, security (implementation notes)

- `SeoData` service builds the `$seo` array exactly per the handoff Blade snippet; absolute URLs from `config('app.url')` (never request host; never `energea1.vercel.app`). Reciprocal hreflang; `x-default` = en; per-page canonical including `?page=n` on projects listing.
- Service/project SEO: `seo_*` override → else "title | Energea" + summary (service) / "location · year · scope" (project), as `main.js` does.
- sitemap.xml: all published localized routes + record routes with hreflang alternates + lastmod (content updated_at); robots.txt: production allows + disallows `/admin` + sitemap link; non-production `Disallow: /` + `X-Robots-Tag: noindex` header.
- JSON-LD Organization from SiteSetting values only. GA4 snippet only when `APP_ENV=production` **and** ID set; GSC verification meta when token set.
- Every `<img>` localized alt (decorative → `alt=""`); explicit width/height; `loading="lazy"` except heroes (`fetchpriority="high"`); imported originals keep exact preview bytes (visual parity); Filament uploads generate WebP + srcset. Font preload: Inter-400 (LTR) / Tajawal-400 (RTL). Sample cache headers for `/assets` in `.htaccess` + nginx snippet (docs).
- Quote form: FormRequest w/ localized messages + attribute names; honeypot `website`; min-fill-time token; throttle 5/min + 20/h per IP; Turnstile when `TURNSTILE_SITE_KEY`/`SECRET` set, else session math captcha (verification-panel markup + refresh endpoint). Attachment: extension allowlist **and** MIME sniff (dwg/dxf special-cased), double-extension + executable rejection, random filename, private disk `storage/app/private/attachments`, admin-only download; ClamAV behind `config('quote.clamav.enabled')` (off).
- PRG flow: save QuoteRequest → attempt mail (recipients from SiteSetting, fallback `MAIL_TO_ADDRESS`; Reply-To submitter; plain+HTML, all escaped) → on failure keep record + `mail_error` + still redirect success → Filament flags it. Success = localized flash after redirect, scroll to `#quote`.
- Security: security-headers middleware (nosniff, frame `SAMEORIGIN`/deny for admin, referrer-policy, permissions-policy), force HTTPS + secure cookies in production, login throttling (Filament default), strong passwords (Laravel ≥12 default), 2FA if supported by installed Filament version, `preventLazyLoading` outside production, complete `.env.example`, no secrets in repo. CSP skipped initially (GA4 + Turnstile + inline scripts) — documented.
- Backup: `spatie/laravel-backup` for DB + `storage/app` (public + private disks); `docs/BACKUP-RESTORE.md` with verified restore procedure; post-handover scheduling explicitly hosting/maintenance dependent.

## 9. Tests (Phase 7 gate)

- Route × locale 200 for all 9 pages; unknown/unpublished slug → 404 (localized); `/` → `/en` 301; all legacy `.html` redirects; language-switch targets (incl. `?page=` preservation and detail slugs).
- Canonical + hreflang reciprocity for every page type; sitemap lists only published records; robots by environment.
- Quote form: happy path (Mail::fake, record stored, redirect success), validation (each field + `location_other` required when `other` + bad service slug), honeypot rejected, throttle 429, attachment rules (type, size, double extension), mail-failure path stores `mail_error` and still succeeds for the visitor.
- Filament smoke tests: list/create/edit/reorder/publish for each resource; quote-request badge; settings page.
- Grep checks: no `.html` links, no `data.js`/`ENERGEA_DATA`, no `data-preview-only`, no hard-coded phones/emails in views, no `{!! !!}` on DB content.
- Visual parity: Playwright (Chromium + WebKit, Edge if available) at 1440/768/390 vs static preview for 9×2 pages; if no browser runs in this environment → DOM/class-level diff + documented explanation (fallback decided in Phase 7).

## 10. Risks & open questions

| Risk / question | Mitigation / decision |
| --- | --- |
| Filament major-version API drift (v3 vs v4) | Install latest stable that supports PHP 8.3, then read the **installed** version's docs before writing any Filament code |
| Laravel version conflict inside the brief ("laravel 10" vs "latest stable") | Use latest stable (12.x); log in ASSUMPTIONS.md |
| Intervention Image / Spatie package compatibility | Verify at install; if incompatible, implement a small GD-based WebP/resizer instead of downgrading the framework |
| Playwright/WebKit on this Windows host | Attempt install in Phase 7; fall back to DOM-level diff and state so in the final report |
| ClamAV on Windows | Config flag, off by default; hook documented, not required |
| `data-limit` partials unused | Build partials with optional limit; no page renders them (matches preview) |
| Social links have no design slot | Store in SiteSetting (contract: "links"), do not render; documented |
| MySQL for local dev | Laragon MySQL listening on 3306; tests use SQLite `:memory:` |

## 11. Next step

Await approval of this analysis, then Phase 1: scaffold latest stable Laravel in a temp dir, move into `D:\laravel\energea` root (leaving `D:\laravel\Energea-Frontend` untouched and outside `public/`), install Filament + packages, set up locales/middleware/config, git init + feature branch.
