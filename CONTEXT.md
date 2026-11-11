# CloneGuard Security Scanning plugin: working context

Living context for the 2.6 update. **Read this file first in every session, and update it at the end of every step** (the Session log and the checkboxes in the plan). The detailed step-by-step plan is in [docs/PLAN.md](docs/PLAN.md). Source brief: "CloneGuard WordPress Plugin: Developer Handoff" (Elyse, 3 Nov 2026).

Last updated: 2026-11-11 (Phase 1 step 3, Opus 5.5). Implementation sessions run on Opus 5.5.

## Goal

Ship version 2.6 to WordPress.org so the "not tested with the latest 3 major releases" warning disappears, then modernize (block + shared seal embed + CI), then marketing refresh with Elyse.

## Current status

| Phase | State |
|---|---|
| 0. Planning | Done 2026-11-11 |
| 1. Get current, ship 2.6 | In progress on branch `release/2.6` (steps 1, 1b, 2, 3 done; next: step 4) |
| 2. Modernize | Not started |
| 3. Marketing refresh (needs Elyse) | Not started |

## Verified facts (checked 2026-11-11, not assumptions)

- **WordPress.org copy = SVN trunk = version 2.5.** `readme.txt` uses `Stable tag: trunk`, and the downloaded zip's `main.php` is byte-identical to `trunk/main.php`. Tags exist for 1.0 through 2.5 (incl. 2.3.3.1). Download: `https://downloads.wordpress.org/plugin/clone-guard-security-scanning.zip`. SVN: `https://plugins.svn.wordpress.org/clone-guard-security-scanning/`.
- **Synced 2026-11-11:** branch `release/2.6` now holds the exact 2.5 trunk (commit `db7c35e`); `main` stays at the old copy until `release/2.6` is merged in step 8. SVN trunk and the zip have the same 67 files (the `.save` file is in both). Before the sync: **GitHub copy (this repo, branch `main`) is at 2.2.0 code with a 2.1.0 header** and `readme.txt` says `Tested up to: 5.6`. Files only in the 2.5 copy: `css/admin_ajax_form.css`, `css/admin_scan.css`, `views/admin_vulnerability_add_exception.php`, `views/admin_vulnerability_edit_exception.php`, several `img/*` screenshots and banners, and a stray `views/admin_overview.php.save` (do not carry over). Changed files: `main.php`, `classes/class-clone-guard-api.php`, `readme.txt`, several css/js/views.
- **2.5 readme header**: `Requires at least: 4.0`, `Tested up to: 6.5.2`, `Requires PHP: 5.6`, `Stable tag: trunk`.
- **Portal API**: plugin calls `https://{cgss_portal_url}/API/v1/...` with `Authorization: Basic {user token}` + `x-api-key`. `https://pciscan.clone-systems.com/API/v1/my_details` returns **401** unauthenticated (endpoint exists). The Go SDK (`clonesec/cg-scanning-sdk-go`) targets a **different, newer API**: prefix `/v2`, paths like `/user/me`, `/targets`, `/scans`, `/reports`, auth `Basic {access token}` or `Bearer {jwt}`, products `pci | vrms | pentest`, docs at `https://scanning-api.clone-systems.com/`. Do **not** migrate to v2 in Phase 1; v1 is what the views are written against.
- **Seal widget is broken today**: `https://seals.clone-systems.com/images/security-seal/certified1_lg.png` returns 404 (all 8 `certifiedN_lg.png` images return 404, each checked 2026-11-11 in step 2). `https://seals.clone-systems.com/report?dn=...` returns 200. `seals.clone-systems.com/` root now serves the corporate website shell.
- **Website seal embed (source: https://www.clone-systems.com/trust-seal, confirmed by owner).** The page generates this snippet:
  ```html
  <script src="https://www.clone-systems.com/generate-seal/get-seal.js"></script>
  <script>generateSecuritySeal('cs-secure-dark', 'lg', 'right', 'svg')</script>
  ```
  `generateSecuritySeal(icon, size, position, format)`:
  - `icon`: `cs-secure`, `cs-certified`, `cs-compliant`, `cs-pci-asv`, `cs-scanned`, each with `-dark` or `-light` (10 values).
  - `size`: `lg`, `md`, `sm`. The generator UI labels them 200x72, 175x63, 150x54, but `get-seal.js` actually renders 175x63, 150x54, 125x45. Trust the script.
  - `position`: `left` / `right` (fixed to a bottom corner, appended to `<body>`) or `relative` (inline: appended to `document.currentScript.parentNode`, i.e. wherever the inline call sits).
  - `format`: the generator emits `svg`. SVGs at `/generate-seal/images/svg/modified/{icon}_modified.svg` return 200.
  - The seal links to `https://www.clone-systems.com/report/?dn={window.location.hostname}`. The domain is taken from the browser, so **no domain field is needed** in the plugin.
  - `get-seal.js` derives its asset host from its own `src` via `document.currentScript`, and it also preloads a screenshot from `s.wordpress.com/mshots` when the browser is idle (must be disclosed in readme "External services").
  - The page states: "The seal only appears on domains with an active Clone Systems account and current scans."
  - **Implementation constraint**: inline mode needs the inline call to be a classic, synchronous script printed in place, and `get-seal.js` must already be loaded at that point. Do not add `defer`/`async`, do not load it in the footer, and do not wrap the call in `DOMContentLoaded` (that breaks `document.currentScript`).
- **Links**: old purchase URL `clone-systems.com/purchase-pci-compliance-scanning/` 301s to `https://www.clone-systems.com/pricing?mode=pci` (200). Feedback PDF `pciscan.clone-systems.com/downloads/ASV-Feedback-Form.pdf` returns 200 (still exists; keep).
- **PHP 8 risk already identified**: `main.php` assigns undeclared `$this->user_token` and `$this->api_key` (dynamic properties, deprecated in PHP 8.2). Expect more notices at runtime (undefined array keys etc.). Confirmed on wp-env PHP 8.4 (2026-11-11): with no credentials saved, opening the 5 plugin pages that redirect to Settings logs `Creation of dynamic property ... $user_token / $api_key` at `main.php` lines 1732-1733, 1754-1755, 1808-1809, 1856-1857, 1910-1911. No other notices and no fatals without credentials. **Fixed in step 3** (properties declared; see `docs/PHASE1-FINDINGS.md` > Status after step 3).
- **Security surface for Plugin Check**: in 2.5 `main.php` there are ~30 nonce checks, 162 raw `$_POST[...]` reads, ~100 `sanitize_*`/`strip_tags` calls, only **1** `current_user_can` call, and ~100 `echo $...` in views (many unescaped). Expect Plugin Check to flag escaping, sanitization, and capability checks.
- **Vendored libs**: Select2 4.0.13, jQuery datetimepicker (2020 copies) in `js/` and `css/`.
- **Local tooling on this machine**: Docker 29 and Node 22 are installed. PHP, `svn` and `wp` CLI are **not** installed. Use `@wordpress/env` (wp-env) via Docker for the test site, and the `svn` Docker image or `brew install svn` for the SVN commit (or the 10up GitHub Action from Phase 2).
- The main plugin class is `Clone_Guard_Security_Scanning` in `main.php` (global `$cloneGuardSecurityScanning`), API client `Clone_Guard_API`, widget `Clone_Guard_Widget` (id `clone_guard_widget`). Option keys: `cgss_portal_url`, `cgss_user_token`, `cgss_api_key`.

## Local environment (wp-env, set up 2026-11-11)

Two independent sites, each with its own containers and database. Both mount this repo as the plugin (folder `clone-guard-security-scanning`) and install Plugin Check from `https://downloads.wordpress.org/plugin/plugin-check.zip` (2.2.0 at setup). wp-env itself is pinned at `@wordpress/env` 12.1.0 in `package.json`.

| | Main site | Second pass |
|---|---|---|
| Config | `.wp-env.json` | `.wp-env.php82.json` |
| URL | http://localhost:8888/wp-admin | http://localhost:8890/wp-admin |
| WordPress | 7.1 (the `7.1` tag; `#7.1` resolves fine; newest 7.1.x on 2026-11-11 is 7.1.4) | same |
| PHP | 8.4 (8.4.27 at setup) | 8.2 (8.2.35 at setup) |
| Start / stop | `npm run env:start` / `npm run env:stop` | `npm run env:start:php82` / `npm run env:stop:php82` |
| WP-CLI | `npm run env:cli -- <args>` (e.g. `npm run env:cli -- plugin list`) | `npm run env:cli:php82 -- <args>` |

- Admin login: `admin` / `password` (wp-env defaults; local only).
- Debug constants: `WP_DEBUG`, `WP_DEBUG_LOG`, `SCRIPT_DEBUG` on, `WP_DEBUG_DISPLAY` off. Read the log with `npx wp-env run cli cat wp-content/debug.log`, clear it with `npx wp-env run cli sh -c ': > wp-content/debug.log'` (for the 8.2 site add `--config=.wp-env.php82.json` right after `wp-env`).
- Plugin Check CLI: `npm run plugin-check` (table output). Valid `--format` values are table, csv, json, ctrf and the `strict-*` variants (no `count`).
- PHP lint: `npm run lint:php` (php:8.4-cli Docker image; added now, first run is Step 3).
- `testsEnvironment: false` is set in both configs: wp-env 12 otherwise also starts a "tests" site on 8889 and prints a deprecation warning. The 8.2 site uses a separate config file instead, as wp-env 12 recommends.
- Credentials for the plugin go in each site's Settings page (each site has its own DB). As of step 2 both sites have them; to copy from `.env` without printing them: `awk '/^CGSS_USER_TOKEN=/{printf "%s", substr($0,index($0,"=")+1)}' .env | docker exec -i <cli container> wp option update cgss_user_token` (same for `cgss_api_key`, `cgss_portal_url`). Do not pass secrets as `wp-env run` arguments: wp-env echoes the full command.
- State left by step 2: both sites run Twenty Twenty-One with legacy `clone_guard_widget` instances in `sidebar-1` (8888: two, one with `size=106x42`; 8890: one). Keep them for step 5b's "old widget instances render the new seal without re-saving" check. An optional `.wp-env.override.json` / `.wp-env.php82.override.json` is git-ignored and dist-ignored.
- `npx wp-env destroy` wipes a site (DB included); `npx wp-env start --update` re-reads the config.
- First start takes about 3 to 5 minutes (WordPress clone + images); later starts take about 20 seconds.

## Decisions

| Topic | Decision | Source |
|---|---|---|
| Purchase link | `https://www.clone-systems.com/pricing?mode=pci` | Elyse, 3 Nov 2026 |
| Seal | Must use the same embed script as the website seals. Do not keep a separate image-and-link widget. | Elyse, 3 Nov 2026 |
| Seal embed | Use `https://www.clone-systems.com/generate-seal/get-seal.js` + `generateSecuritySeal(icon, size, position, 'svg')`, exactly as `/trust-seal` generates it. Widget, shortcode and block all call one PHP renderer. | Owner, 2026-11-11 |
| Discount code | **Keep `WORDPRESSNEW`**. Owner will make sure the coupon works in the cart. Rationale: it is already in the published readme and in every tag; changing it adds cart work and copy churn for no gain. | Planning 2026-11-11 |
| API version | 2.6 stays on `/API/v1`. A v2 migration is a separate, later project. | Planning 2026-11-11 |
| Source of truth going forward | GitHub `main`. SVN is a deploy target only (automated in Phase 2 with the 10up action). | Handoff doc |
| Version numbers | One number everywhere: plugin header `Version`, `$version` property, `readme.txt` `Stable tag`. 2.6 uses `Stable tag: 2.6` (not `trunk`) and an SVN tag `2.6`. | Planning 2026-11-11 |
| Minimum versions for 2.6 | `Requires PHP: 7.4`, `Requires at least: 5.9`, `Tested up to: 7.1`. | Owner approved 2026-11-11 |
| Local testing | **wp-env (Docker) is the primary environment** because Claude sessions can drive it from the terminal (WP-CLI, `debug.log`, Plugin Check CLI) and CI reuses the same config. Local (Flywheel) is fine for the owner's own manual click-through; if used, symlink the repo into the Local site's `wp-content/plugins/`. | Planning 2026-11-11 |
| Credentials handling | Never pasted into chat or committed. For the plugin itself, enter token and key on the local site's Settings page (stored in that site's DB only). For scripts that call the API directly (Plan step 4), use a git-ignored `.env` at the repo root with `CGSS_PORTAL_URL`, `CGSS_USER_TOKEN`, `CGSS_API_KEY`; `.env.example` (no values) is committed. `.env` is in `.gitignore` and `.distignore`. | Planning 2026-11-11 |
| Repo hygiene files | `CONTEXT.md`, `docs/`, `CLAUDE.md`, `.github/`, `.vscode/`, `.env*` must be excluded from the WordPress.org build via `.distignore` (added in Phase 1 step 1). | Planning 2026-11-11 |

## Open questions (blocking items marked with !)

1. ~~Seal embed script~~ Resolved 2026-11-11: comes from https://www.clone-systems.com/trust-seal (details under Verified facts).
2. ~~Test account~~ Resolved 2026-11-11: owner has the user token and API key. They go in the local Settings page and the git-ignored `.env`, never in chat.
3. ~~`platform-*` Docker stack~~ Resolved: unrelated project. Do not touch those containers; make sure wp-env ports do not clash with them.
4. ~~Has the v1 API changed since 2023?~~ Partly answered by step 2 (2026-11-11): `/API/v1` is alive and list/detail shapes still match the views, but these changed (details and file:line in `docs/PHASE1-FINDINGS.md`). **Questions for the portal team:**
   - a. `/reports/{id}` no longer returns `compliance` (the list `/reports` does). Was it removed on purpose, and where should the detail view read Pass/Fail from? (F5: detail page always shows Fail.)
   - b. Report file types: VRMS reports offer `executive_vrms`, `vulnerability_assessment`, `vulnerability_summary`, `scan_comparison`, `remediation`. What is the full current list per product (PCI, VRMS, penetration), and does `/reports/{id}.pdf?type=` accept all of them? (F6)
   - c. Notification `status_changed` is now an event name (`scan_completed`, `scan_started`, `scan_failed`, `scan_stopped`, `compliance_failed`, `high_severity_findings`, `kev_detected`, `report_generated`, `report_generation_failed`). Does v1 still accept `Done` / `Running` on create/update, and what does it map them to? (F7)
   - d. `/results` takes 10 to 21 s on uncached pages (0.5 s cached). Is that expected? The plugin will raise its timeout to 30 s in step 4 either way. (F2)
   - e. The remediation download is `.xlsx`, not `.xls`. Confirm, so the plugin can name it correctly. (F8)
5. WordPress.org `clonesupport` credentials: owner has the password. For the GitHub Action deploy (Phase 2) they must be stored as repo secrets `SVN_USERNAME` / `SVN_PASSWORD`.
6. ~~Requires at least 5.9~~ Approved by owner.
7. Should 2.6 already ship the new seal (pull Plan step 9 into Phase 1)? Recommended yes: the current widget shows 404 images on every live site, and the new embed is small. Default if not answered: yes.
8. Step 2 write actions not run (owner approved only Options create/edit/delete on 2026-11-11): product tab switch, report generate, scan create/update/delete, scan start/stop, vulnerability exceptions. Run them on a disposable account, or with the owner's go-ahead, during the step 3 re-run. Browser/JavaScript console behaviour was also not checked (no browser driven); needs a manual pass or a browser session.

## Session log

Append one entry per working session: date, model, what was done, what changed, what is next.

- **2026-11-11, Fable 5.1 (planning).** Read the handoff PDF. Verified the facts above by downloading the 2.5 zip, diffing it against this repo, probing the portal, seal server, website and Go SDK. Wrote this file, `docs/PLAN.md` and `CLAUDE.md`. Nothing in plugin code changed. Next: owner answers the open questions, then an Opus 5.5 session starts Phase 1 step 1 (SVN trunk sync).
- **2026-11-11, Opus 5.5 (planning follow-up).** Owner answered: seal code comes from `/trust-seal`; test credentials available; `platform-*` containers unrelated; WP 5.9 / PHP 7.4 floor approved. Reverse-engineered `/trust-seal` and `get-seal.js` (options, sizes, inline-mode constraint, mshots preload) and recorded them above. Decided wp-env as primary test env, `.env` for API script secrets. Rewrote Plan step 9 around the real embed and moved it into Phase 1 as step 5b (open question 7). No plugin code changed. Next: Phase 1 step 1.
- **2026-11-11, Opus 5.5 (Phase 1 step 1: sync trunk 2.5).** Created branch `release/2.6`. Downloaded the WordPress.org zip, confirmed `main.php` is byte-identical to SVN `trunk/main.php` and that trunk and the zip list the same 67 files. Committed the planning docs separately (`cf72682`), then replaced `main.php`, `readme.txt`, `classes/`, `css/`, `js/`, `img/`, `views/` with the 2.5 copy, left out `views/admin_overview.php.save`, and added `.gitignore`, `.distignore`, `.env.example` (no secrets; portal URL default only) in commit `db7c35e` "Sync with WordPress.org trunk (2.5)". No GitHub-only files existed, so nothing was deleted. Acceptance: `diff -rq` against the zip shows only meta files and the `.save` file; `main.php` shows `2.5` in the header and `$version`. Nothing in CONTEXT.md was wrong; only reworded the GitHub-copy fact to say it describes `main` before the sync. Branch is local only (not pushed). Next: step 1b (wp-env with WordPress 7.1, PHP 8.4, Plugin Check).
- **2026-11-11, Opus 5.5 (Phase 1 step 1b: wp-env).** Added `package.json` (`@wordpress/env` 12.1.0; scripts `env:start`, `env:stop`, `env:cli`, `lint:php`, `plugin-check`, plus `env:*:php82`), `package-lock.json`, `.wp-env.json` (WordPress `#7.1`, PHP 8.4, plugin + Plugin Check, debug constants) and `.wp-env.php82.json` (same, PHP 8.2, port 8890). Added the 8.2 config and its override file to `.distignore` / `.gitignore`. Deviations from the plan text: Plugin Check from `plugin-check.zip` instead of `plugin-check.latest-stable.zip` (the latter installs into an odd folder name); `testsEnvironment: false` because wp-env 12 otherwise starts an extra tests site on 8889 and deprecates it; second config file instead of `env.tests` (wp-env 12 recommendation). Acceptance on 8888: WordPress 7.1, PHP 8.4.27, plugin 2.5 active with no fatal, "CloneGuard Security" menu with Overview/Scans/Reports/Vulnerabilities/Options/Settings, Plugin Check under Tools (page loads 200), `wp plugin check` CLI runs. 8890 also verified (WordPress 7.1, PHP 8.2.35, both plugins active), then stopped. New fact: without credentials, 5 plugin pages redirect (302) to Settings and log only the known dynamic-property deprecations (lines recorded under Verified facts). `debug.log` cleared afterwards so Step 2 starts clean. Main site left running. Next: Step 2 (click-through with the real account), which needs the owner to enter portal URL, token and key on the Settings page of each site.
- **2026-11-11, Opus 5.5 (Phase 1 step 2: click-through).** Owner entered credentials on the 8888 Settings page and in `.env`. Found and fixed a credentials problem (values never printed): the token saved in Settings was cut off at 60 of 68 characters (portal returned 401), and the `.env` copy had a stray space where the line had wrapped; removed the space in `.env` and wrote the full token into both sites' options via stdin. Drove every admin page, form and read-only AJAX handler over logged-in HTTP on 8.4 and 8.2, collecting `debug.log` per request; owner approved only Options create/edit/delete for writes, which were run with "ZZ plugin test" entries that were all deleted and verified gone on the portal. Wrote `docs/PHASE1-FINDINGS.md` (F1-F11 plus notice table, page-by-page table, not-tested list). Headlines: any slow or failed API call crashes the page (no `is_wp_error` check, 10 s timeout; `/results` is often 10-21 s uncached), target create shows an error although it succeeds, report detail always shows Compliance Fail, report file types and notification status values changed on the portal side, seal images all 404. PHP notices are the same on 8.2 and 8.4. CONTEXT corrections: seal-image fact changed from "presumably" to confirmed; open question 4 answered in part and turned into five portal-team questions (4a-4e); added open question 8 (untested write actions, no browser pass). Local state: both sites have credentials and Twenty Twenty-One with legacy seal widgets (kept for step 5b); 8890 stopped, 8888 running. Next: step 3 (PHP 8 fixes), working from the notice table; F1/F2 belong to step 4 but the step 3 acceptance (empty `debug.log`) will need them, so expect step 3 and 4 to overlap on `api()`.
- **2026-11-11, Opus 5.5 (Phase 1 step 3: PHP 8 fixes).** `lint:php` (php:8.4-cli) passes on all 21 PHP files, before and after. Code commit `399e0a0` (5 files, 43+/41-, line-by-line): declared `$user_token`/`$api_key`; in the API client every getter now accepts an already-decoded 404/422 body and every `count($data[...])` is `!empty(...)` (removes the PHP 8 TypeErrors behind F1 and F3); fixed target create misreading `true['status']` (F4) and removed the undefined `$target_id`; scans list skips empty `next_time` (no more 1970 date) and the clock icon links to `cgss_options`; Options hosts column joins the first three hosts with `, ` (escaped); vulnerability view defaults missing exception text; one `count()` guard in `main.php`. PHP 8 removal/deprecation grep found nothing else. Re-ran the full read-only pass on 8.4 and 8.2: `debug.log` empty on every page when the portal responds normally (5 stale entries from 16:45 UTC, before the edits, came from someone browsing the site). Not done here, moved to Plan step 4 action 5 (added to the plan text): warnings on failure paths (`getUserDetails()` returning `null` when the portal is unreachable or stalls, unknown IDs in detail URLs) and showing an error instead of empty lists. New facts: `/results` page 2 took 28.8 s uncached; one 947 s request on 8.2 during a portal/network stall. No write actions were run this session. 8890 stopped, 8888 running. Next: step 4 (API contract doc, `api-probe.sh`, harden `api()` incl. the carried-over items; portal questions 4a-4e still open).
