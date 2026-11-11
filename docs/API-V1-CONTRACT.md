# Portal API v1: what the plugin calls and what it reads (Plan step 4)

Base URL: `https://{cgss_portal_url}/API/v1` (default portal `pciscan.clone-systems.com`). Every request sends `Authorization: Basic {cgss_user_token}`, `x-api-key: {cgss_api_key}` and, since step 4, `User-Agent: CloneGuardSecurityScanning/{version}; {home_url}` with a 30 s timeout. Write bodies are form-encoded with bracketed keys (`targets[name]=…`).

Checked 2026-11-11 against the live portal. **Verified** = exercised through the plugin in step 2/3/4 and/or with `scripts/api-probe.sh` (GET only; prints status and top-level keys, never bodies). Field names below come from `main.php` and `views/`; response shapes were checked by field name only.

## How `Clone_Guard_API::api()` maps responses (after step 4)

| Portal response | `api()` returns | What callers do |
|---|---|---|
| Network failure (timeout, DNS, TLS) | `['status' => 0, 'status_text' => message, 'cgss_error' => true]`, sets `last_error`; later calls in the same request fail fast with the same error | getters return empty lists; create/update return the message (shown in the form); delete/start/stop/generate return `false`; pages print the message as an error notice |
| HTTP 5xx | same error array ("returned an error (HTTP 5xx)") | same as above |
| HTTP 401 | `false` | page probes in `adminTemplateRedirect()` redirect to Settings with `msg=access` |
| HTTP 404, 422 | decoded JSON array (portal error body: `status`, `status_text`, …) | create/update show `status_text`; getters return empty (detail pages show "not found") |
| Anything else (200) | raw body string | callers `json_decode()` it |

Top-level keys seen on success: lists `status, status_text, pagination, {collection}` (reports: `status, reports, pagination, meta`; targets add `ips_count, settings`); details `status, status_text, uuid, {collection}` (targets: `settings` instead of `uuid`). `pagination` has `current_page, current_page_results, per_page, total_count, total_pages`. List collections are `{collection}[0]` = list of items, except `results`, which is an object keyed by id.

## Reads (GET)

| Method | Endpoint | Fields the plugin reads | Status |
|---|---|---|---|
| `getUserDetails()` | `GET /my_details` | `details.app_type`, `details.organization.available_products.{pci,vrms,penetration}` | Verified. Since step 4: called once per request and cached; returns defaults (`app_type ''`, products `false`) if the call fails. Current values: `app_type` is `pci` or `vrms` (switched between step 2 and step 4); products also list `darkweb`, `agent_scan`, `webapp` (unused). |
| `getScans($page)` | `GET /scans?page=N` | per scan: `id, name, status, progress, target.{id,name}, schedule.{id,next_time}, last_report.{id,name}, notifications, scanner.id, config.id, comment`; `pagination.*` | Verified. `schedule.next_time` can be `null` (handled in step 3). |
| `getScan($id)` | `GET /scans/{id}` | same as above; `notifications[0][*].id` | Verified. Unknown id: 404 (detail page shows "not found"). |
| `getReports($page, $perPage)` | `GET /reports?page=N&per_page=M` | per report: `id, name` (a date string), `task.name, compliance, results.{high,medium,low,high_and_medium}, files`; `pagination.*` | Verified. List still has `compliance`. |
| `getReport($id)` | `GET /reports/{id}` | `id, name, task.name, compliance, results_count.high_and_medium, files.{attestation,executive,detailed,remediation}` | Verified, **changed**: no `compliance` any more (step 4 shows "Not available" instead of "Fail"; question 4a). `files` is `[]` on many reports. Unknown id: 404. |
| `generateReportCheck($id, $type)` | `GET /reports/{id}/details` | `reports[0][*].files.{type}.status == 'done'` | Verified (GET, status and keys). `strpos` bug fixed in step 4. Polling through the plugin not exercised (report generate not approved). |
| `downloadReport($id, $type)` | `GET /reports/{id}.pdf?type={type}` | raw file | Verified: `attestation`, `executive`, `detailed` (PDF), `remediation` (.xlsx, served as `.xlsx` since step 4 when the file starts with `PK`), `feedback` (plugin redirects to the static PDF, no API call). File types **changed**: PCI reports also offer `certificate_of_compliance`; VRMS reports offer `executive_vrms`, `vulnerability_assessment`, `vulnerability_summary`, `scan_comparison`, `remediation` (question 4b). |
| `getResults($page, $perPage)` | `GET /results?page=N&per_page=M` | per result: `id, name, host, port, severity, threat, nvt.oid, overrides.{id,status,text}`; `pagination.*` | Verified. **Slow**: 10 to 35 s for an uncached page, 0.5 s cached (question 4d). |
| `getResult($id)` | `GET /results/{id}` | `id, name, host, port, severity, threat, nvt.{cve,tags,oid}, task.{id,name}, overrides.{id,status,text}` | Verified. `overrides` is `[]` when there is no exception. Tags seen: `cvss_base_vector, severity_vector, severity_origin, severity_date, qod_type, solution_type, summary, vuldetect, insight, affected, solution`. Unknown id: no 404, the request hangs past 30 s (question 4d). |
| `getException($id)` | `GET /exceptions/{id}` | `exceptions[0][first].{id,text,hosts,port,nvt_oid,task.{id,name}}` | **Untested** (no result with an exception on the pages checked). |
| `getAllSchedules($page)` | `GET /schedules?page=N` | per schedule: `id, name, period, period_unit, comment`; `pagination.*` | Verified. |
| `getSchedule($id)` | `GET /schedules/{id}` | `id, name, first_time, period, period_unit (once/day/week/month), timezone, comment` | Verified. One-time schedules come back with `period` 0. |
| `getAllTargets($page)` | `GET /targets?page=N` | per target: `id, name, hosts (comma-separated string), max_hosts, comment`; `pagination.*` | Verified. Extra top-level keys `ips_count`, `settings` (unused). |
| `getTarget($id)` | `GET /targets/{id}` | `id, name, hosts, comment` | Verified. |
| `getAllNotifications($page)` | `GET /notifications?page=N` | per notification: `id (integer), name, status_changed, email_options.to_address`; `pagination.*` | Verified, **changed**: `status_changed` values are event names (`scan_completed`, `scan_started`, `scan_failed`, `scan_stopped`, `compliance_failed`, `high_severity_findings`, `kev_detected`, `report_generated`, `report_generation_failed`), not `Done`/`Running` (question 4c). |
| `getNotification($id)` | `GET /notifications/{id}` | same | Verified. Edit form cannot pre-select the event-name values (F7, open). |
| `getScanners()` | `GET /scanners/activated` | `scanners[0][*].{id,name}`, `count.total` | Verified. |
| `getScanConfigs()` | `GET /configs?active=false&per_page=100` | `configs[0][*].{id,name,app_type}`; `pagination.*` | Verified (100 configs, about 4 s). |

## Writes

| Method | Endpoint | Body | Status |
|---|---|---|---|
| `createTarget($item)` | `POST /targets` | `targets[name], targets[hosts], targets[comment]` | Verified (step 2). Returns `true`; the handler now reads it correctly (step 3, F4). `exclude_hosts` from the form is not sent. |
| `updateTarget($id, $item)` | `PUT /targets/{id}` | same | Verified. |
| `deleteTarget($id)` | `DELETE /targets/{id}` | none | Verified (single and bulk). |
| `createSchedule($item)` | `POST /schedules` | `schedules[name], schedules[first_time]` (`Y-m-d H:i:s`), `schedules[timezone]`, `schedules[period]`, `schedules[period_unit]` (`once/day/week/month`), `schedules[comment]` | Verified. |
| `updateSchedule($id, $item)` | `PUT /schedules/{id}` | same | Verified. |
| `deleteSchedule($id)` | `DELETE /schedules/{id}` | none | Verified (single and bulk). |
| `createNotification($item)` | `POST /notifications` | `notifications[name], notifications[to_address], notifications[status_changed]` (`Done`/`Running`) | Verified (returns success; stored value unknown, question 4c). |
| `updateNotification($id, $item)` | `PUT /notifications/{id}` | same | Verified (same caveat). |
| `deleteNotification($id)` | `DELETE /notifications/{id}` | none | Verified (single and bulk). |
| `createScan($item)` | `POST /scans` | `scans[name], scans[target_id], scans[schedule_id], scans[notification_ids], scans[scanner_id], scans[config_id], scans[comment]` | Untested (not approved). |
| `updateScan($id, $item)` | `PUT /scans/{id}` | same | Untested. |
| `deleteScan($id)` | `DELETE /scans/{id}` | none | Untested. |
| `startScan($id)` / `startScanScheduled($id)` / `stopScan($id)` | `POST /scans/{id}/start`, `/start_scheduled`, `/stop` | none | Untested (would launch a real scan). |
| `generateReport($id, $type)` | `GET /reports/{id}/generate?type={type}` | none | Untested (not approved). A GET that changes state: never called by `api-probe.sh`. |
| `deleteReport($id)` | `DELETE /reports/{id}` | none | Untested (destructive). |
| `updateUserAppType($type)` | `PUT /modules/?app_type={type}` | none | Untested through the plugin (not approved). The account's `app_type` did change between step 2 and step 4, outside these tests. Handler now treats only `true` as success (step 4). |
| `createException($item)` | `POST /exceptions` | `exceptions[text], exceptions[nvt], exceptions[task]` (+ `exceptions[hosts]` from the bulk path) | Untested (changes compliance findings). |
| `updateException($id, $text, $nvt)` | `PUT /exceptions/{id}?exceptions[text]=…&exceptions[nvt]=…` | values in the query string, not URL-encoded | Untested. Note for step 6: `$text` goes into the URL unencoded. |

delete/start/stop/generate return `false` on a network or 5xx error since step 4 (they used to report success).
