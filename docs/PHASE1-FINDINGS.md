# Phase 1 findings: click-through of 2.5 on WordPress 7.1 (Plan step 2)

Run on 2026-11-11 against the live portal `pciscan.clone-systems.com` (`/API/v1`), with the owner's account. Plugin version 2.5 exactly as published (branch `release/2.6`, commit `db7c35e`).

- Sites: wp-env `http://localhost:8888` (WordPress 7.1, PHP 8.4.27) and `http://localhost:8890` (WordPress 7.1, PHP 8.2.35). Classic theme Twenty Twenty-One for the widget test.
- Method: logged-in HTTP requests to every admin page and AJAX handler (same requests the browser makes), with `debug.log` collected and cleared after each request. API response shapes checked by printing only field names, never values.
- Account: a large organization account in **VRMS** mode (`app_type: vrms`; pci, penetration, vrms, darkweb, agent_scan and webapp all available). Roughly 152 pages of scans, 2,306 of reports, 2,758 of results (vulnerabilities), 536 of schedules, 106 of targets, 1 of notifications.
- No customer data (names, hosts, emails, IDs) is recorded in this file.

## Summary

| # | Severity | Finding | Where | Fix in |
|---|---|---|---|---|
| F1 | **Fatal** | Any API call that fails at the transport level (timeout over 10 s, DNS failure, wrong portal URL) crashes the page with "There has been a critical error". `api()` never checks `is_wp_error()`, so callers `json_decode('')` and `count(null)` throws a `TypeError` on PHP 8. | `classes/class-clone-guard-api.php:243, 472, 545` (every `count($data[...])`), reached from `main.php:1821`, `1745`, `1918` | Step 4 (WP_Error handling, 30 s timeout) |
| F2 | **Fatal** (seen in practice) | The portal's `/results` is slow on uncached pages: 10.6 s to 21.5 s measured directly with curl, 0.5 s once cached. The Vulnerabilities list therefore hits F1 the first time a page is opened (page 1 on first visit, page 2, last page), then works on reload. | Vulnerabilities page | Step 4 + portal question (see CONTEXT) |
| F3 | Fatal (needs a hand-typed URL) | `admin.php?page=cgss_scans&key=schedule-update&subkey=…` reaches `getScan('schedule-update')`; the 404 body comes back from `api()` already decoded and is passed to `json_decode()` again → `TypeError`. The URL comes from the clock icon in the Scans list, which is a `<span href>` with no click handler, so users cannot reach it by clicking. Root cause is general: `api()` returns arrays for 404/422 and strings otherwise, and most getters `json_decode()` whatever they get. | `classes/class-clone-guard-api.php:511`; link built at `views/admin_scans.php:182` with page `scans` instead of `options` | Step 4 (return type), step 3 (link) |
| F4 | Functional bug | **Creating a target reports an error even though the target is created.** `ajaxTargetCreate()` reads `createTarget($item)['status']`, but `createTarget()` returns `true`, so the result is `null` and the user sees an empty error. Users will retry and create duplicates. Also `$target_id` is undefined there. | `main.php:1624`, `main.php:1632` | Step 3 |
| F5 | Functional bug (API changed) | **Report detail always shows Compliance: Fail.** `/reports/{id}` no longer returns `compliance` (the list endpoint `/reports` still does). Same three reports: list shows Fail, Pass, Pass; detail shows Fail, Fail, Fail. Misleading on a compliance product. | `main.php:463-467`, `views/admin_report_view.php:52` | Step 4 (portal question first) |
| F6 | Functional gap (API changed) | **Report file types.** The plugin knows `attestation`, `executive`, `detailed`, `remediation`, `feedback`. VRMS reports now offer `executive_vrms`, `vulnerability_assessment`, `vulnerability_summary`, `scan_comparison`, `remediation`. On VRMS reports every download except Remediation shows as not generated. PCI-mode file types not re-checked (tab switch was not approved, see Not tested). | `main.php:468-488`, `views/admin_report_view.php:57-97` | Step 4 (portal question first) |
| F7 | Functional bug (API changed) | **Notification status values.** The portal now stores `status_changed` as event names (`scan_completed`, `scan_started`, `scan_failed`, `scan_stopped`, `compliance_failed`, `high_severity_findings`, `kev_detected`, `report_generated`, `report_generation_failed`). The plugin sends and compares `Done` / `Running`. Create and update still return success, but the edit form never pre-selects the saved status (falls back to "Scan Completed", so saving again can silently change it). Unknown how the portal stored `Done`. | `main.php:799`, `main.php:849`, `views/admin_notification_edit.php:41-48`, `views/admin_notification_create.php:38` | Step 4 (portal question first) |
| F8 | Minor | Remediation download is an `.xlsx` file (ZIP signature `PK`) served as `remediation.xls` with `application/vnd.ms-excel`; Excel warns about the mismatch. | `classes/class-clone-guard-api.php:162-164` | Step 4 |
| F9 | Minor | Settings save never checks the credentials against the portal; a wrong token is saved with a success message and only fails later (redirect loop back to Settings). This is how a truncated token went unnoticed during setup. | `main.php:1559-1596` | Step 6 or Phase 2 (nice to have) |
| F10 | Performance | Overview takes 16 to 19 s (one `getReport()` per scan on page 1, plus four `getUserDetails()` calls). Scan create/edit forms take 8 to 11 s, close to the 10 s timeout. Every list page calls `/my_details` four times. | `main.php:198-224`, `289`, `352`, `408-411` and the same pattern on each page | Step 4 (cache `getUserDetails()` per request); deeper fix Phase 2 |
| F11 | Known, broken in production | Seal widget: all 8 `certifiedN_lg.png` images return **404** (checked each). Report link `seals.clone-systems.com/report?dn=…` returns 200. Widget renders with no PHP notices on both classic and block themes. | `classes/class-clone-guard-widget.php` | Step 5b |

## PHP notices (identical on PHP 8.4 and 8.2)

Beyond F1 to F4 above:

| Notice | File:line | Trigger | Visible effect |
|---|---|---|---|
| `Deprecated: Creation of dynamic property Clone_Guard_Security_Scanning::$user_token` / `$api_key` | `main.php:1732-1733, 1754-1755, 1808-1809, 1856-1857, 1910-1911` | Every plugin admin page | none |
| `Deprecated: strtotime(): Passing null to parameter #1` | `views/admin_scans.php:209` | Scans list, any scan whose schedule has `next_time: null` | "Next Scan" shows `Thu Jan 1 1970 0:00:00` for that scan |
| `Warning: Undefined array key 2` | `views/admin_options.php:254` | Options, Targets table, a target whose hosts contain exactly one comma | Hosts column prints the parts glued together with no separator (`echo $a[0] . $a[1] . $a[2]`); also unescaped |
| `Warning: Undefined array key "text"` | `views/admin_vulnerability_view.php:21` | Every vulnerability detail without an exception (`overrides` is an empty array) | none |
| `Warning: Trying to access array offset on true` | `main.php:1624` | Target create (F4) | empty error message |

No notices on: Settings, Settings save, report downloads, scan create/target/schedule/notification sub-forms, Options create/edit forms, schedule and notification create/update/delete, front end with the widget, Appearance > Widgets.

## Page by page

Times are wall-clock for the full admin page on 8888 (8890 within about 10%). "Notices" excludes the two dynamic-property deprecations that every plugin page logs.

| Page / action | HTTP | Time | Notices / errors | API data in view |
|---|---|---|---|---|
| Overview | 200 | 16-19 s | none | filled (5 latest reports, compliance card) |
| Scans list, page 1 / 2 / last | 200 | 4.5 s | `admin_scans.php:209` | all columns filled |
| Scan edit form (3 scans) | 200 | 9.6-11 s | none | filled |
| Scan create form, `&return=yes` | 200 | 8.3-8.8 s | none | selects filled |
| Scan create sub-forms (schedule, target, notification) | 200 | 1.5 s | none | n/a |
| `cgss_scans&key=schedule-update` (hand-typed) | 200 + critical error | 2 s | F3 | n/a |
| Reports list, page 1 / 2 / last | 200 | 3.7 s | none | all columns filled |
| Report view (3 reports) | 200 | 2 s | none | F5 (compliance), F6 (file types) |
| Report download: remediation | 200 | <1 s | none | F8 |
| Report download: feedback | 302 to the feedback PDF | <1 s | none | ok |
| Report download: other types | not available on these reports (F6) | | | |
| Vulnerabilities list, page 1 / 2 / last | **500** on first open, 200 on reload | 10 s (fail), 3 s | F1 + F2 | all columns filled |
| Vulnerability view (3) | 200 | 1.1 s | `admin_vulnerability_view.php:21` | sections filled (summary, insight, solution; impact absent in that tag set, expected) |
| Add exception form (2) | 200 | 1.1 s | none | filled |
| Edit exception form | **not tested** (no result with an exception on the first 3 pages) | | | |
| Options list, paged, last pages | 200 | 6 s | `admin_options.php:254` | all columns filled (comments optional) |
| Target / schedule / notification edit forms (3 each) | 200 | 2.6 s | none | filled, except F7 (status not pre-selected) |
| Target / schedule / notification create forms | 200 | 2.2 s | none | n/a |
| Target create (submit) | 200 | 0.5 s | F4 | created, but UI shows error |
| Target update, single delete, bulk delete | 200 / 302 | 0.5 / 2.7 s | none | ok, verified on portal |
| Schedule create, update, single delete, bulk delete | 200 / 302 | 0.5 / 2.6 s | none | ok, verified on portal (one-time schedule echoes `period` 0) |
| Notification create, update, single delete, bulk delete | 200 / 302 | 0.5 / 2.6 s | none | ok, verified on portal; F7 |
| Settings, Settings `&msg=access` | 200 | 0.05 s | none | n/a |
| Settings save (same values) | 200 | 0.02 s | none | n/a |
| Settings save with unreachable portal URL, then Overview / Options | **500** | 0.6 s | F1 | n/a; restored afterwards |
| Front end with 2 seal widgets (classic theme) | 200 | 0.05 s | none | F11 (images 404) |
| Seal widget on block theme (`the_widget`) | renders | | none | F11 |
| Appearance > Widgets (classic) | 200 | 0.1 s | none | form shows the 8 broken images |

Throwaway entries created for the write tests were named "ZZ plugin test …" (targets on 192.0.2.10/11, reserved documentation IPs; notifications to an example.com address; one-time schedules in 2030, never attached to a scan) and were all deleted; the portal was checked afterwards and none remain.

## Not tested, and why

| Item | Reason |
|---|---|
| Product tabs (switch VRMS / PCI / Penetration) | Changes the account's mode on the portal; owner did not approve for this pass. PCI-mode report file types (F6) unverified because of this. |
| Report generate / generate-check | Owner did not approve (adds files to a real report). |
| Scan create, update, delete (submit); scan start, stop, scheduled start | Owner did not approve; start would launch a real scan. |
| Vulnerability exceptions: add, edit, bulk add | Changes findings that count toward compliance; skipped. Edit form also had no data to open. |
| Report delete | Destructive. |
| Browser console / JavaScript behaviour (tabs, Select2, datetimepicker, AJAX form messages) | No browser driven in this pass. Needs a manual check by the owner, or a browser session, in step 3 or step 6. |

These can be run on a disposable account, or with the owner's go-ahead, during the step 3 re-run.
