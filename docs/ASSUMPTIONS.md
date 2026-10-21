# ASSUMPTIONS & logged conflicts

Brief wins over the handoff where they conflict. Each conflict/assumption is logged here.

## Conflicts

1. **Frontend folder name** — brief/handoff refer to `./frontend-source/`; the actual folder on this machine is `D:\laravel\Energea-Frontend` (stated at the end of the brief). Treated as the frontend source; left untouched, outside `public/`.
2. **`data-limit` variants on Home/About** — brief claims Home/About use `data-limit` variants of the certificates/clients grids. Inspection shows **no such blocks exist**: the only data grids are on `/certificates` (`data-certificates`) and `/clients` (`data-clients`), both unlimited. Blade partials will still accept an optional limit; no page uses it.
3. **Laravel version** — brief's last line says "start by make laravel 10 project"; the stack section requires "latest stable Laravel and Filament that work with the installed PHP" and forbids pinning versions from memory. Laravel 10 is EOL and unsupported by Filament 4. Resolved: **latest stable Laravel + latest stable Filament**. Installed: Laravel 13.38.0 + Filament 5.10.3 (PHP 8.3.33).
4. **`data.js` is JSONC** — contains `//` comments; importer strips line comments before decoding (handoff calls it JSON).

## Assumptions (sensible defaults, no blocking questions)

1. **Stat counters & Home/About copy stay in lang files** — not in the contract's editable list; values (30+, 15+, 8+, 24/7) copied verbatim.
2. **Footer brand paragraph is editable** (SiteSetting `footer_text`, seeded with the verbatim preview copy) because the brief lists "footer text {en,ar}" in SiteSetting; footer office lines ("Saudi Arabia", "Jeddah · Riyadh · Khobar") and column headings stay in lang files.
3. **Footer/contact phones, emails, offices come from SiteSetting** (contract: contact details + addresses editable; grep rule: no hard-coded phones/emails in views). Seeded with the exact preview values so rendered output is identical.
4. **Social links**: stored in SiteSetting (`social_links`) to satisfy "links" in the contract, but **not rendered** — the approved design has no social-link slot and redesigns are forbidden.
5. **Project sector**: `sector_id` is admin-only categorization (no public filter UI exists in the preview), per the brief.
6. **`logo_code` for new clients**: auto-generated sequential 3-digit code (next free), editable; existing codes preserved (they drive `.logo-card-NNN` CSS).
7. **Steps/facts defaults**: the six services without `steps`/`facts` in `data.js` store NULL; the Blade detail view falls back to the exact default texts hard-coded in `main.js`.
8. **Parties**: only name+logo pairs are imported (14 of the existing entries); name-only entries (e.g. Riyadh Airports, SYSKA, all of Jabal Omar) are dropped, matching the preview's render rule.
9. **Images**: imported files keep their original bytes/formats (certificates explicitly required unchanged); WebP + responsive variants are generated only when an admin uploads/replaces an image through Filament.
10. **`/` redirect**: 301 to `/en` always (no geo-detection); language choice is up to the visitor via the switcher.
11. **Root `/index.html`** legacy redirect goes to `/en`.
12. **CSP**: skipped initially (GA4 + Turnstile + inline scripts would break); documented in the security notes in Phase 6.
13. **ClamAV**: config flag `quote.clamav.enabled`, off by default; the hook scans attachments when enabled and rejects on detection.
14. **Min-fill-time token**: hidden `form_ts` field set on GET; submissions faster than ~3 s are rejected (spam layer alongside honeypot + challenge + throttle).
15. **Turnstile vs math captcha**: Turnstile used only when both keys are present in `.env`; otherwise the session math captcha reusing the existing verification-panel markup, with a refresh endpoint.
16. **Localized 404s**: `errors/404.blade.php` renders in the current locale with the app layout.
17. **Admin path**: `/admin` by default, overridable via env (Filament panel path).
18. **Trusted proxies**: not configured in Phase 1 � ootstrap/app.php cannot read env/config reliably (the middleware callback runs before configuration is cached). Loop-safe ForceHttps honours X-Forwarded-Proto; the exact 	rustProxies value for the production host is a Phase 6 launch-checklist item.
19. **Year in copyright**: dynamic server-side (`© {{ now()->year }} ENERGEA`).
20. **Social links admin UI**: `social_links` remains in SiteSetting (empty array from the preview) but has no admin form field because the handoff defines no shape for social entries; revisit only if real links are supplied.
21. **Admin UI language**: the Filament panel stays in English (default locale); bilingual *content* is edited through EN/AR tabs. The public site remains fully bilingual.
22. **Client names**: the preview stores client names in English only; Arabic names are optional in the admin, and the public site shows the same name in both locales.
