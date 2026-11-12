# Implementation plan: CloneGuard Security Scanning 2.6 and beyond

Companion to [CONTEXT.md](../CONTEXT.md). Each step below is written so an Opus 5.5 session can execute it on its own. Every step ends the same way: run the step's checks, tick the checkbox here, append a Session log entry in `CONTEXT.md`, and commit on a branch (never directly on `main` without the owner asking).

Conventions for the implementing session:
- Work in this repo. Do not edit the WordPress.org SVN working copy by hand except in the release step.
- Keep changes minimal and reviewable in Phase 1. No refactors, no renames, no reformatting of files you are not fixing. The diff for 2.6 should be explainable line by line.
- Preserve option names (`cgss_portal_url`, `cgss_user_token`, `cgss_api_key`), the widget id `clone_guard_widget`, menu slugs and page hooks. Existing installs must keep working after the upgrade with no re-entry of credentials.
- Never put real tokens or API keys in files, commits, screenshots or `CONTEXT.md`. Use wp-env's `.wp-env.override.json` (git-ignored) or enter them in the Settings page.
- Branch naming: `release/2.6` for Phase 1, `feat/<topic>` for Phase 2.

## Kickoff prompt for an Opus 5.5 session

Paste this at the start of each implementation session:

> You are continuing the CloneGuard Security Scanning WordPress plugin update. Read `CONTEXT.md` and `docs/PLAN.md` first. Find the first unchecked step in the plan whose prerequisites are met, confirm from the Session log that nobody is mid-way through it, and execute it exactly as written. Do not start later steps. When done, run the step's acceptance checks, tick the checkbox in `docs/PLAN.md`, append a Session log entry to `CONTEXT.md` (date, model, what changed, what is next, any new facts or open questions), and commit on the step's branch. If anything in `CONTEXT.md` turns out to be wrong, correct it there and say so in the log.

---

## Phase 1: get it current and ship 2.6

Prerequisites: none for steps 1 and 1b. Step 2 onward needs the test account (open question 2).

### - [x] Step 1: Sync WordPress.org trunk (2.5) into GitHub

Goal: `main` matches the published 2.5 exactly, so all later work starts from the real code.

Actions:
1. Create branch `release/2.6` from `main`.
2. Download `https://downloads.wordpress.org/plugin/clone-guard-security-scanning.zip` into a fresh empty directory outside the repo and unzip it. Verify its `main.php` matches `https://plugins.svn.wordpress.org/clone-guard-security-scanning/trunk/main.php` (curl + diff). Also fetch `trunk/` file list from that URL and check there are no files in trunk that are missing from the zip.
3. Replace the plugin files in the repo with the 2.5 copy: delete `main.php`, `readme.txt`, `classes/`, `css/`, `js/`, `img/`, `views/` and copy the 2.5 versions in. Do **not** copy `views/admin_overview.php.save`. Keep `.git/`, `.vscode/`, `CONTEXT.md`, `CLAUDE.md`, `docs/`.
4. Add `.gitignore` (`.env`, `.wp-env.override.json`, `node_modules/`, `.DS_Store`, `*.zip`) and `.distignore` (`.git`, `.github`, `.vscode`, `.gitignore`, `.distignore`, `.env`, `.env.example`, `.wp-env.json`, `.wp-env.override.json`, `node_modules`, `package.json`, `package-lock.json`, `CONTEXT.md`, `CLAUDE.md`, `docs`, `tests`, `scripts`, `*.zip`). Add `.env.example` with empty `CGSS_PORTAL_URL=pciscan.clone-systems.com`, `CGSS_USER_TOKEN=`, `CGSS_API_KEY=`. Do not ignore `build/` (block build output ships).
5. Commit as "Sync with WordPress.org trunk (2.5)". Do not change any plugin code in this commit.

Acceptance: `diff -rq <unzipped 2.5 dir> <repo>` shows only the ignored/meta files and the `.save` file as differences. `grep -n "Version\|version = " main.php` shows `2.5` twice.

### - [x] Step 1b: Local test environment (wp-env, WordPress 7.1, PHP 8.4)

Done 2026-11-11. Deviations: Plugin Check comes from `plugin-check.zip` (the `latest-stable` URL installs into a folder named `plugin-check.latest-stable`); the PHP 8.2 pass uses a second config `.wp-env.php82.json` on port 8890; `testsEnvironment: false`. Details in `CONTEXT.md` > Local environment.

Goal: a reproducible local site with `WP_DEBUG` on, the plugin mounted, and Plugin Check installed.

Actions:
1. Add `package.json` with devDependency `@wordpress/env` and scripts `env:start`, `env:stop`, `env:cli` (`wp-env run cli wp`), `lint:php` (see step 3), `plugin-check`.
2. Add `.wp-env.json`:
   - `core`: `"WordPress/WordPress#7.1"` (fall back to the newest 7.1.x tag that wp-env resolves; record which in `CONTEXT.md`).
   - `phpVersion`: `"8.4"` (add a second environment or document switching to `"8.2"` for the second pass).
   - `plugins`: `[".", "https://downloads.wordpress.org/plugin/plugin-check.latest-stable.zip"]`.
   - `config`: `WP_DEBUG: true`, `WP_DEBUG_LOG: true`, `WP_DEBUG_DISPLAY: false`, `SCRIPT_DEBUG: true`.
3. `npx wp-env start`, confirm `http://localhost:8888/wp-admin` loads, activate the plugin, and check that the debug log path is readable with `npx wp-env run cli cat wp-content/debug.log`.
4. Document the exact commands in `CONTEXT.md` under a new "Local environment" heading (URL, admin user, how to read debug.log, how to switch PHP version).

Acceptance: plugin activates with no fatal; `CloneGuard Security` menu appears; Plugin Check appears under Tools.

### - [x] Step 2: Click-through with a real account and collect every notice

Done 2026-11-11 on 8.4 and 8.2. Results in `docs/PHASE1-FINDINGS.md`. Not run (owner decision or no browser): product tab switch, report generate, scan submit/start/stop, exceptions, JS console. See `CONTEXT.md` open question 8.

Prerequisite: test account (open question 2).

Actions:
1. Enter the portal URL, user token and API key on the Settings page (never commit them).
2. Visit, in order, and exercise every form and button that does not destroy data: Overview; Scans (list, view, tabs, start/stop if a disposable scan exists, create form, edit form); Reports (list, view, generate, download for each type); Vulnerabilities / Findings (list, detail, add exception, edit exception, bulk select); Options (targets, schedules, notifications: list, add, edit, delete a throwaway entry, bulk actions); Settings (save, change portal URL and change back). Add the CloneGuard Seal widget in Appearance > Widgets (or the Legacy Widget block) and load the front end.
3. After each page, dump new lines from `debug.log`. Collect every `Deprecated`, `Warning`, `Notice`, `Fatal` with file:line into `docs/PHASE1-FINDINGS.md`, grouped by page, with the triggering action.
4. For every API call made, note in the same file whether the response shape matched what the view expected (blank columns, "undefined index" warnings and empty tables are the signals). Compare any mismatch against the v1 behavior the code assumes, and record it as an open question for the portal team in `CONTEXT.md`.
5. Repeat the whole pass once with `phpVersion: "8.2"`.

Acceptance: `docs/PHASE1-FINDINGS.md` exists with a complete page-by-page table, and `CONTEXT.md` lists any endpoint whose behavior changed.

### - [x] Step 3: Fix PHP 8.x deprecations and warnings

Done 2026-11-11 (commit `399e0a0`). Normal-operation pass clean on 8.4 and 8.2; failure-path warnings moved to step 4 action 5. Details in `docs/PHASE1-FINDINGS.md` > Status after step 3.

Actions:
1. Add `lint:php` script: `docker run --rm -v "$PWD":/app -w /app php:8.4-cli sh -c 'find . -name "*.php" -not -path "./node_modules/*" -print0 | xargs -0 -n1 php -l'`. Run it; fix any syntax-level issue.
2. Declare `public $user_token = '';` and `public $api_key = '';` in `Clone_Guard_Security_Scanning` (and any other property assigned at runtime, in any class, including `Clone_Guard_API::$portal_url` / `$base_url` if undeclared). Do not use `#[AllowDynamicProperties]`.
3. Work through `docs/PHASE1-FINDINGS.md` top to bottom. Typical fixes: `isset()`/`??` for array keys, cast or guard before `count()`/`strlen()`/`implode()`, `(string)` before string functions that now reject null, `WP_Error` checks before `wp_remote_retrieve_*` consumers.
4. Grep the whole code base for patterns PHP 8 rejects or deprecates, regardless of whether the click-through hit them: `each(`, `create_function`, `FILTER_SANITIZE_STRING`, `money_format`, `{$var[` string interpolation, `strftime`, optional-before-required parameters, implicit float to int, `null` passed to `strlen/strpos/trim/htmlspecialchars/urlencode`.
5. Re-run the click-through on 8.4 and 8.2. `debug.log` must stay empty of plugin-originated entries.

Acceptance: zero lines mentioning `clone-guard-security-scanning` in `debug.log` after a full pass on 8.4 and 8.2; `lint:php` passes. Record the list of fixed items in the Session log.

### - [x] Step 4: Confirm the API client against the portal

Done 2026-11-11. `docs/API-V1-CONTRACT.md`, `scripts/api-probe.sh`, hardened `api()` and the carried-over failure paths. Open: F6 (unsupported report file types) and F7 (notification status values) wait for portal answers 4b and 4c; the write endpoints the owner did not approve remain untested (open question 8).

Actions:
1. For every public method in `classes/class-clone-guard-api.php`, list the endpoint, HTTP method and the response keys the views consume (from `main.php` and `views/`). Put the table in `docs/API-V1-CONTRACT.md`.
2. Mark each row as verified from step 2, or untested. For untested ones that are safe (GET only), add `scripts/api-probe.sh` that reads `.env` and calls each GET endpoint with curl, printing only HTTP status and top-level JSON keys (never the token, never full bodies, since bodies can contain customer data). Never call create, update, delete, start or stop endpoints from the script.
3. Harden `Clone_Guard_API::api()`: check `is_wp_error($res)` and return a structured error; set a `timeout` (30s) and `user-agent` (`CloneGuardSecurityScanning/{version}; {home_url}`); never `print_r`/`var_dump` responses.
4. Do **not** switch to API v2. If an endpoint turned out to be gone or changed, fix the view minimally and log it as a product question in `CONTEXT.md`.
5. Carried over from step 3 (failure paths; step 3 removed the fatals, these still log warnings or fail silently):
   - `getUserDetails()` returns `null` when `/my_details` fails, and every list page reads `getUserDetails()['app_type']` and `['organization']['available_products'][...]` (4 calls, 4 warnings per page, e.g. `main.php:411`, `526-529`, `643-646`, `760-763`). Call it once per request, cache it, and return an array with safe defaults.
   - On a transport error (timeout, DNS, wrong portal URL) the getters now return empty lists, so pages render empty with no explanation. Show an admin notice ("The scanning portal did not respond…") and keep the redirect to Settings only for 401.
   - Detail and edit routes with an unknown or deleted ID (`getScan`, `getReport`, `getResult`, `getTarget`, `getSchedule`, `getNotification` return `[]`) render the view with hundreds of undefined-key warnings (215 to 253 on the scan edit view). Show a "not found" notice and return before including the view.
   - `generateReportCheck()`: `strpos(...) !== -1` is always true (`strpos` returns `false`, never `-1`).

Acceptance: `docs/API-V1-CONTRACT.md` complete; every page renders real data with no blank primary columns; the failure-path checks above (portal URL set to `portal.invalid`, and a hand-typed unknown scan ID) log nothing in `debug.log`.

### - [x] Step 4b: Page speed (owner request 2026-11-11: performance in Phase 1)

Ticked 2026-11-11: owner accepted the result as is (first scan-form load per 15 minutes stays about 9 s; repeat loads about 3x faster). Parallel scan-form calls left for Phase 2.

Why: pages wait on several portal calls made one after another (measured after step 4: Overview 13.8 s, scan edit 9.0 s, scan create 8.1 s, Options 4.2 s, Scans 3.5 s, Reports 2.6 s). Settings, with no portal calls, loads in 0.03 s, so the time is all portal round trips.

Actions:
1. Credential check without a duplicate list call: `adminTemplateRedirect()` fetches the page's list (or three lists on Options) only to detect a 401, and the page then fetches it again. Check with the per-request cached `/my_details` call instead (`Clone_Guard_API::hasAccess()`), which every page makes anyway. Keep the redirect to Settings with `msg=access` on 401.
2. Overview: the compliance card needs `results_count.high_and_medium` from the last report of each scan on page 1 (about 10 `GET /reports/{id}` calls in a row). The scan list does not include the counts, the report list does not contain those reports (checked: 0 of 10 among the latest 50), and the reports are all `scan_run_status: Running`, so caching them is not safe. Fetch them in parallel with WordPress's bundled Requests library (`WpOrg\Requests\Requests::request_multiple`, or `Requests::request_multiple` before WordPress 6.2), falling back to one at a time for any that fail.
3. Cache the slow reference data used by the scan forms for 15 minutes in transients, keyed by portal URL + token: `getScanConfigs()` (about 4 s) and `getScanners()`. Clear both when the app type is switched.
4. Measure every page before and after on 8.4; re-run the full pass and failure paths on 8.4 and 8.2 (debug.log must stay empty).

Not in Phase 1: parallel requests for other pages, caching of lists that users edit (targets, schedules, notifications), and the portal-side `/results` slowness (question 4d).

Acceptance: Overview and the scan forms at least 2x faster; no page slower; same data shown; debug.log empty after the full pass and failure paths on 8.4 and 8.2.

Status 2026-11-11 (code done, commit below; not ticked because one acceptance item is open). Same portal state, before (step 4 code) / after, first load / repeat load: Overview 35.8 s (with a timeout error) / 17.6 s / 1.3 s; scan edit 9.9 s / 8.9-9.3 s / 3.3 s; scan create 8.6 s / about 6.5 s / 2.4 s; Options 4.6 / 2.4 s; Scans 4.1 / 2.0 s; Reports 2.7 / 1.5 s. No page slower; parallel and sequential report data identical; debug.log empty on 8.4 and 8.2. **Open:** the first scan-form load in each 15-minute window is not 2x faster (it still waits for the 4 s configs call plus five other calls one after another). Fix options: run the scan-form calls in parallel too, or accept and move on. Owner to decide.

### - [x] Step 5: Update links and Settings copy (interim)

Done 2026-11-11. Kept the existing feature claims and only removed the SAQ version (did not add the example "Unlimited rescans", an unconfirmed product claim; Phase 3 brings final copy). Acceptance grep run on the shipped files (the repo-wide grep also matches CONTEXT.md and docs/, which quote the old URL on purpose). Seal image URLs still 404 until step 5b.

Actions:
1. In `views/admin_settings.php`: change the Purchase a License `href` to `https://www.clone-systems.com/pricing?mode=pci`. Replace the feature list with neutral current wording that does not name a PCI DSS or SAQ version (Phase 3 brings the final copy): e.g. "PCI ASV scanning by an approved scanning vendor", "Unlimited rescans", "Online SAQ wizard", "IPv4 and IPv6 targets", "24x7 support". Remove the `v3.2`.
2. Keep `$feedback_url` (verified 200 on 2026-11-11). Add `rel="noopener"` to any `target="_blank"` link you touch.
3. Grep for every other hard-coded `clone-systems.com` URL and verify each with curl; replace any that redirect with the final URL.
4. In `readme.txt` keep the `WORDPRESSNEW` line as is (decision: keep).

Acceptance: `grep -rn "purchase-pci-compliance-scanning\|SAQ v3" .` returns nothing; all hard-coded URLs return 200 directly.

### - [x] Step 5b: Replace the seal with the website embed (moved from step 9)

Done 2026-11-11. Deviations from the actions below, each for a reason found in testing: (1) `render()` calls `wp_scripts()->do_items()` instead of `wp_print_scripts()`, which would also fire the global `wp_print_scripts` action in the middle of the page; (2) when the `<head>` scripts are not printed yet (always the case on block themes, which render the whole template before `<head>`), the seal enqueues `get-seal.js` for the `<head>` instead of printing it in place. In-place-only failed on Twenty Twenty-Five: blocks render before shortcodes, so a Legacy Widget block printed the script below two shortcode seals, which then threw "generateSecuritySeal is not defined". (3) A 2.5 instance with an empty size maps to `lg` (the old widget drew empty as 140x54). (4) Widget gets an optional title field. Known limitation (classic themes only): a Legacy Widget block in post content placed after a seal shortcode breaks that shortcode's seal; the post editor does not offer the Legacy Widget block, so it needs hand-written block markup.

Why in Phase 1: the current widget shows 404 images on every live site. See "Website seal embed" in `CONTEXT.md` for the full spec. Owner decision: one implementation, no image-and-link widget.

Actions:
1. New file `classes/class-clone-guard-seal.php` with:
   - constants for the allowed values: icons (10 `cs-*-dark|light`), sizes (`lg`, `md`, `sm`), positions (`relative`, `left`, `right`). Format is always `svg`.
   - `wp_register_script( 'cgss-get-seal', 'https://www.clone-systems.com/generate-seal/get-seal.js', [], null, false )` on `init`. Version `null` so no `?ver=` is appended; no `defer`/`async` strategy.
   - `public static function render( array $atts ): string` that whitelists each value (fallback `cs-secure-dark`, `md`, `relative`), then returns: the output of `wp_print_scripts( 'cgss-get-seal' )` captured with `ob_start()` (prints the tag in place the first time, nothing after, because WordPress marks it done), followed by `<span class="cgss-seal">` + `wp_print_inline_script_tag( "generateSecuritySeal('{icon}','{size}','{position}','svg');" )` + `</span>`. Values are whitelisted, and still pass through `esc_js`.
2. Rewrite `Clone_Guard_Widget`: keep class name and id `clone_guard_widget` so placed widgets survive. `widget()` echoes `$args['before_widget']`, optional title, `Clone_Guard_Seal::render()`, `$args['after_widget']`. `form()` offers icon (select with the 10 options, labels like "Secure, dark"), size, position, all escaped. `update()` whitelists. Legacy instances (`domain`/`image`/`size` like `140x54`) map to: icon `cs-secure-dark`, size `140x54`→`lg`, `106x42`→`md`, `82x32`→`sm`, position `relative`. Drop the domain field entirely.
3. Add shortcode `[cloneguard_seal icon="cs-secure-dark" size="md" position="relative"]` using `shortcode_atts` and the same renderer.
4. Delete the 404 image URLs and `css/admin_widgets.css` plus its enqueue, unless the new form needs a small style.
5. readme.txt: add an `== External services ==` section stating that the seal loads `get-seal.js` and images from www.clone-systems.com, links to the report page with the site's hostname, and that the script requests a screenshot of the site from s.wordpress.com mShots; link Clone Systems terms and privacy pages (verify their URLs with curl). Also mention the portal API calls to the configured portal URL.
6. Test on wp-env with Twenty Twenty-Five (block theme, via the Legacy Widget block and via the shortcode in a Shortcode block) and with a classic theme (Twenty Twenty-One, Appearance > Widgets). Check each position, two widgets on one page (script printed once), and that the image loads. On localhost the seal links to the report for `localhost`, which is expected.

Acceptance: exactly one code path renders the seal; no 404 requests in the browser network panel; old widget instances render the new seal without being re-saved.

### - [x] Step 6: Plugin Check clean-up (security and WordPress.org requirements)

Done 2026-11-12 (committed as c0aac33; handler-test fixes in a follow-up commit). Plugin Check on the shipped files (`scripts/plugin-check-dist.sh`): 322 errors / 383 warnings before, 1 error / 0 warnings after. The error is `stable_tag_mismatch` (readme 2.6, header 2.5), closed by step 7's version bump; how the last 125 warnings were cleared is in `docs/PLUGIN-CHECK-AFTER.txt`. Click-through (41 pages + 11 failure paths) clean on 8.4 and 8.2, and a visible-text comparison against the pre-step-6 code found two regressions that were fixed (report compliance HTML shown as text; notification options double-escaped). Handler test (owner allowed it): all 25 AJAX handlers called in-process on 8.4 and 8.2 as no-capability user, admin with a bad nonce, and admin with empty fields, with all outgoing HTTP blocked and logged. All refuse or stop at validation; only portal reads were attempted (my_details, scans); options and draft data unchanged. It found two PHP warnings already in 2.5 (exception create read `scanOrHost` without isset; scan draft save used an undefined `$app_type`, so drafts also dropped Scanner/Config for VRMS/penetration accounts), both fixed. Deviation: the plan's tag list and short description were applied with the smallest copy change (5 of the existing 10 tags; trailing "..." removed so the description is exactly 150 characters).

Actions:
1. Run Plugin Check from Tools > Plugin Check with all categories, and also via CLI: `npx wp-env run cli wp plugin check clone-guard-security-scanning --format=table`. Save the raw output to `docs/PLUGIN-CHECK-BEFORE.txt`.
2. Fix errors first, then warnings, in this order:
   - **Capability checks**: every admin page callback and every POST handler must start with `current_user_can('manage_options')` (the plugin is admin-only). Today there is one such check.
   - **Nonces**: every state-changing POST path must verify a nonce (`check_admin_referer`); confirm each of the ~30 existing checks actually guards the handler it sits in, and add missing ones.
   - **Input sanitization**: every `$_POST`/`$_GET` read goes through `sanitize_text_field`, `absint`, `sanitize_email`, `esc_url_raw` or `wp_unslash` + `sanitize_*` as appropriate. Arrays through `array_map`.
   - **Output escaping**: every `echo $...` in `views/*.php` and `main.php` wrapped in `esc_html`, `esc_attr`, `esc_url` or `wp_kses_post` (for the few places that intentionally output HTML).
   - **Enqueues**: scripts and styles only via `wp_enqueue_*`; no inline `<script src>`; remove `?ver=` hand-rolled query strings.
   - **Prefixing**: all global functions, options, transients, handles prefixed `cgss_`.
   - **readme.txt**: `Stable tag: 2.6`, `Tested up to: 7.1`, `Requires at least: 5.9`, `Requires PHP: 7.4`, a real `Plugin URI` (`https://github.com/clonesec/clone-guard-security-scanning`), tags max 5, short description max 150 chars.
   - **Plugin header**: add `Text Domain: clone-guard-security-scanning`, `License: GPLv2 or later`, `Requires at least`, `Requires PHP`, `Plugin URI`.
3. Save the final output to `docs/PLUGIN-CHECK-AFTER.txt`. Remaining warnings must each have a one-line justification in that file.
4. Re-run the full click-through: escaping and sanitization changes are where regressions hide (double-encoded `&`, stripped HTML in report views, broken multi-select arrays).

Acceptance: Plugin Check reports zero errors; warnings justified; click-through still clean.

### - [x] Step 7: Version bump, readme, changelog

Actions:
1. Set `Version: 2.6` in the `main.php` header and `public $version = '2.6';`. Grep for any other `2.5`/`2.0.0` version literals.
2. `readme.txt`: `Stable tag: 2.6`; changelog entry `= 2.6 =` listing: tested up to WordPress 7.1, PHP 8.2 to 8.4 compatibility fixes, security hardening (escaping, nonces, capability checks), updated purchase link and Settings copy, minimum requirements raised to WordPress 5.9 and PHP 7.4. Add `== Upgrade Notice ==` for 2.6.
3. Bump `.wp-env.json` nothing; verify the plugin still activates cleanly on a fresh site and on a site that already had 2.5 options stored (set the three options with WP-CLI before activating).

Acceptance: header, property and readme agree on `2.6`; activation and upgrade paths clean.

Done 2026-11-12. Only `main.php` had version literals (header and `$version`). Changelog also lists the step 5b seal change and shortcode and the step 4/4b speed and error-message work, which the plan's list left out. Activation and upgrade tested on a throwaway wp-env site (WordPress 7.1, PHP 8.4, port 8896, destroyed afterwards) with the shipped build (repo minus `.distignore`): fresh activation clean; 2.5 from the WordPress.org zip with dummy options and two legacy widgets (106x42 and empty size) in Twenty Twenty-One, then the folder replaced by 2.6 as an update does: still active, options and widget instances unchanged, widgets render the new seal (md and lg), admin pages and deactivate/activate clean, no PHP notices. Plugin Check: 0 errors, 0 warnings.

### - [ ] Step 8: Release 2.6 to WordPress.org SVN

Prerequisite: `clonesupport` password (owner has it). Do this step only when the owner says go.

Actions:
1. Merge `release/2.6` into `main` via PR, tag `v2.6` in git.
2. Build a clean copy respecting `.distignore` (simplest: `git archive` plus deleting the ignored paths, or `rsync --exclude-from=.distignore`). Inspect the tree: no `docs/`, no `CONTEXT.md`, no `node_modules/`.
3. Check out SVN into an empty directory outside the repo (`brew install svn` or `docker run --rm -it -v "$PWD":/w -w /w` with an image that has svn). `svn co https://plugins.svn.wordpress.org/clone-guard-security-scanning svn-wc --depth immediates && svn up --set-depth infinity svn-wc/trunk svn-wc/assets`.
4. Replace `svn-wc/trunk` contents with the build; `svn add --force .`; `svn status | grep '^!' | awk '{print $2}' | xargs -r svn rm` for deleted files; `svn ci -m "Release 2.6: WordPress 7.1 and PHP 8.4 compatibility, security hardening" --username clonesupport`.
5. `svn cp trunk tags/2.6 && svn ci -m "Tag 2.6"`.
6. Wait a few minutes, reload `https://wordpress.org/plugins/clone-guard-security-scanning/` and confirm: version 2.6, "Tested up to 7.1", the warning is gone. Download the zip from WordPress.org and `diff -rq` it against the build.
7. Record the SVN revision numbers in `CONTEXT.md`.

Acceptance: listing shows 2.6 without the compatibility warning.

---

## Phase 2: modernize

### Step 9: (moved to Phase 1 as step 5b)

If the owner answers "no" to open question 7, move step 5b back here unchanged and ship 2.6 with the old widget, noting "seal update coming in 2.7" in the changelog.

### - [ ] Step 10: Gutenberg block for the seal

Actions:
1. Scaffold `blocks/seal/` with `block.json` (`apiVersion: 3`, name `cloneguard/seal`, category `widgets`, `render` pointing to a PHP render callback that calls `Clone_Guard_Seal::render()`). Use `@wordpress/scripts` for the build; commit the built `build/` output so the WordPress.org zip needs no build step, and keep `src/` out of the dist via `.distignore` if desired.
2. Attributes: `icon`, `size`, `position` (same whitelists as step 5b). Inspector controls are selects. The editor preview shows the static SVG `https://www.clone-systems.com/generate-seal/images/svg/modified/{icon}_modified.svg` at the chosen size, not the live script.
3. Register with `register_block_type( __DIR__ . '/blocks/seal/build' )` on `init`. Keep the classic widget registered for legacy sites.

Acceptance: block insertable in the Site Editor on Twenty Twenty-Five, renders the seal on the front end.

### - [ ] Step 11: Front-end library updates

Actions:
1. Replace jQuery datetimepicker with native `<input type="datetime-local">` in the schedule/scan forms; convert the value format server-side to what the API expects (document the format in `docs/API-V1-CONTRACT.md`).
2. Update Select2 to the latest 4.1.x release in `js/`/`css/` (copy the licence file). Test with the jQuery 3.7 that WordPress ships, no jQuery Migrate warnings in the console.
3. Delete the old vendored files.

Acceptance: all forms work on WordPress 7.1 with `SCRIPT_DEBUG` on and no console errors.

### - [ ] Step 12: CI and automated deploy

Actions:
1. `.github/workflows/ci.yml`: on push and PR, matrix PHP 7.4, 8.2, 8.4 running `php -l` on all files; a job that boots wp-env and runs `wp plugin check` and fails on errors.
2. `.github/workflows/deploy.yml`: on published GitHub release, `10up/action-wordpress-plugin-deploy@stable` with `SVN_USERNAME`/`SVN_PASSWORD` secrets and `SLUG: clone-guard-security-scanning`; a second workflow `10up/action-wordpress-plugin-asset-update` to push `readme.txt` and `.wordpress-org/` assets on `main`.
3. Move banner, icon and screenshots into `.wordpress-org/` (the SVN `assets/` convention) and out of `img/` if they are not used by the plugin UI itself.
4. Document the release procedure in `docs/RELEASING.md`: bump versions, changelog, tag, create GitHub release, done.

Acceptance: a dry-run release of `2.6.1` (or a `-beta` branch test with `dry-run: true`) completes.

---

## Phase 3: marketing refresh (needs Elyse)

### - [ ] Step 13: Screenshots, banner, icon
Retake every page against the refreshed portal; banner 772x250 and 1544x500, icon 128 and 256, in the website orange; place in `.wordpress-org/`; update the `== Screenshots ==` captions.

### - [ ] Step 14: Copy
Rewrite the WordPress.org description and the Settings page copy from Elyse's text; no dollar amounts in the opening lines; product names matching the pricing page.

### - [ ] Step 15: Discount code and backlinks
Marketing gave the new code `WORDPRESSNEW8` on 2026-11-12 (already in `readme.txt`); confirm it works in the cart. Add a page or section on clone-systems.com linking to the plugin; set that URL as `Plugin URI`, or keep GitHub, as Elyse prefers.

---

## Risk notes for the implementer

- The biggest Phase 1 time sink is step 6, not step 3. Escaping 100 echo sites by hand is slow; do it view by view and re-test each view before moving on.
- The seal is broken in production today (404 images), which is why the new embed is step 5b in Phase 1. The tricky part is load order: `get-seal.js` must be loaded before the inline call, synchronously, and the inline call must sit where the seal should appear. `wp_print_scripts()` at render time solves both; do not "optimize" it into a deferred or footer script.
- `Stable tag: trunk` is what 2.5 used. Switching to `Stable tag: 2.6` means the `tags/2.6` SVN tag must exist before trunk's readme claims it, otherwise WordPress.org serves trunk anyway with a warning. Commit trunk and the tag in the same session.
