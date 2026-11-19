<?php
/**
 * Plugin Name: CloneGuard demo portal (local screenshots only)
 * Description: Answers every CloneGuard portal API call with invented demo data, so screenshots show no customer data. Nothing reaches the real portal while this file is in mu-plugins: reads get demo data, writes get a fake success.
 *
 * Dev tool, never shipped (scripts/ is in .distignore). Turn on and off with `scripts/demo-portal.sh on|off`.
 * Response shapes follow docs/API-V1-CONTRACT.md (checked against the live portal 2026-11-19, keys only).
 */

defined('ABSPATH') || exit;

// The Overview's compliance card fetches report details in parallel with the Requests library, which
// bypasses pre_http_request. Serve its cached summary instead (same transient name the plugin uses).
add_action('init', function () {
    $portal = get_option('cgss_portal_url');
    $token  = get_option('cgss_user_token');
    foreach (['pci', 'vrms', 'penetration'] as $type) {
        $key = 'cgss_' . md5($portal . '|' . $token . '|overview_summary_' . $type);
        add_filter('pre_transient_' . $key, function () {
            return ['compliance_status' => 'pass', 'vulnerabilities_sum' => 0, 'incomplete' => false];
        });
    }
});

add_filter('pre_http_request', function ($pre, $args, $url) {
    if (strpos($url, '/API/v1/') === false) {
        return $pre;
    }
    $method = isset($args['method']) ? strtoupper($args['method']) : 'GET';
    $path   = (string) wp_parse_url($url, PHP_URL_PATH);
    $path   = substr($path, strpos($path, '/API/v1/') + 7);
    parse_str((string) wp_parse_url($url, PHP_URL_QUERY), $query);
    $body   = cgss_demo_route($method, $path, $query);
    return [
        'headers'  => ['content-type' => 'application/json'],
        'body'     => wp_json_encode($body),
        'response' => ['code' => 200, 'message' => 'OK'],
        'cookies'  => [],
        'filename' => null,
    ];
}, 10, 3);

function cgss_demo_page($total) {
    return ['total_count' => $total, 'total_pages' => 1, 'per_page' => 10, 'current_page' => 1, 'current_page_results' => $total];
}

function cgss_demo_keyed($items) {
    $out = [];
    foreach ($items as $item) {
        $out[$item['id']] = $item;
    }
    return $out;
}

function cgss_demo_id($n) {
    return sprintf('d3m0%04x-0000-4000-8000-%012x', $n, $n);
}

function cgss_demo_targets() {
    $rows = [
        [1, 'Online store', 'shop.example.com', 1, 'Storefront and checkout'],
        [2, 'Payment API', 'api.example.com', 1, 'Card data never stored'],
        [3, 'Company website', 'www.example.com, example.com', 2, ''],
        [4, 'Mail server', '203.0.113.25', 1, ''],
        [5, 'Office VPN', '198.51.100.7', 1, 'Remote access gateway'],
        [6, 'Staging', 'staging.example.com', 1, 'Pre-release copy of the store'],
    ];
    $out = [];
    foreach ($rows as $r) {
        $out[] = ['id' => cgss_demo_id(100 + $r[0]), 'name' => $r[1], 'hosts' => $r[2], 'exclude_hosts' => '', 'max_hosts' => $r[3], 'comment' => $r[4], 'app_type' => 'pci', 'in_use' => 1];
    }
    return $out;
}

function cgss_demo_schedules() {
    return [
        ['id' => cgss_demo_id(201), 'name' => 'Weekly, Monday night', 'timezone' => 'America/New_York', 'first_time' => '2026-11-23T02:00:00.000-05:00', 'period' => 1, 'period_unit' => 'week', 'comment' => 'Low-traffic window'],
        ['id' => cgss_demo_id(202), 'name' => 'Quarterly ASV scan', 'timezone' => 'America/New_York', 'first_time' => '2027-01-05T03:00:00.000-05:00', 'period' => 3, 'period_unit' => 'month', 'comment' => 'PCI DSS quarterly requirement'],
        ['id' => cgss_demo_id(203), 'name' => 'Monthly', 'timezone' => 'UTC', 'first_time' => '2026-12-01T01:00:00.000+00:00', 'period' => 1, 'period_unit' => 'month', 'comment' => ''],
    ];
}

function cgss_demo_notifications() {
    return [
        ['id' => 301, 'name' => 'Scan finished', 'status_changed' => 'scan_completed', 'email_options' => ['to_address' => 'security@example.com']],
        ['id' => 302, 'name' => 'Compliance failed', 'status_changed' => 'compliance_failed', 'email_options' => ['to_address' => 'security@example.com']],
        ['id' => 303, 'name' => 'High severity finding', 'status_changed' => 'high_severity_findings', 'email_options' => ['to_address' => 'it-team@example.com']],
    ];
}

function cgss_demo_scans() {
    $t = cgss_demo_targets();
    $s = cgss_demo_schedules();
    $rows = [
        // n, name, target, schedule index or null, status, progress, last report date
        [1, 'Online store: quarterly PCI', 0, 1, 'Done', 100, '2026-11-17T02:00:00.000-05:00', '2027-01-05T03:00:00.000-05:00'],
        [2, 'Payment API', 1, 0, 'Running', 64, '2026-11-19T02:00:00.000-05:00', '2026-11-23T02:00:00.000-05:00'],
        [3, 'Company website', 2, 2, 'Done', 100, '2026-11-01T01:00:00.000+00:00', '2026-12-01T01:00:00.000+00:00'],
        [4, 'Mail server', 3, null, 'Done', 100, '2026-11-12T10:30:00.000-05:00', null],
        [5, 'Office VPN', 4, null, 'Done', 100, '2026-11-10T09:15:00.000-05:00', null],
        [6, 'Staging', 5, null, 'New', 0, null, null],
    ];
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'id' => cgss_demo_id($r[0]),
            'name' => $r[1],
            'status' => $r[4],
            'progress' => $r[5],
            'comment' => '',
            'app_type' => 'pci',
            'notifications' => [[['id' => 301, 'name' => 'Scan finished']]],
            'schedule' => $r[3] === null ? ['id' => '', 'name' => '', 'next_time' => null] : ['id' => $s[$r[3]]['id'], 'name' => $s[$r[3]]['name'], 'next_time' => $r[7]],
            'target' => ['id' => $t[$r[2]]['id'], 'name' => $t[$r[2]]['name'], 'hosts' => $t[$r[2]]['hosts']],
            'config' => ['id' => cgss_demo_id(501), 'name' => 'PCI ASV full scan'],
            'scanner' => ['id' => cgss_demo_id(601), 'name' => 'Clone Systems external scanner'],
            'last_report' => $r[6] === null ? ['id' => '', 'name' => '', 'timestamp' => null] : ['id' => cgss_demo_id(400 + $r[0]), 'name' => $r[6], 'timestamp' => $r[6]],
        ];
    }
    return $out;
}

function cgss_demo_reports() {
    $rows = [
        // n, scan n, scan name, date, compliance, high, medium, low
        [1, 2, 'Payment API', '2026-11-19T02:00:00.000-05:00', true, 0, 0, 3],
        [2, 1, 'Online store: quarterly PCI', '2026-11-17T02:00:00.000-05:00', true, 0, 0, 5],
        [3, 4, 'Mail server', '2026-11-12T10:30:00.000-05:00', true, 0, 0, 2],
        [4, 5, 'Office VPN', '2026-11-10T09:15:00.000-05:00', true, 0, 0, 1],
        [5, 3, 'Company website', '2026-11-01T01:00:00.000+00:00', true, 0, 0, 4],
        [6, 3, 'Company website', '2026-10-01T01:00:00.000+00:00', false, 1, 2, 6],
    ];
    $out = [];
    foreach ($rows as $r) {
        $done = ['status' => 'done'];
        $out[] = [
            'id' => cgss_demo_id(400 + $r[1] + ($r[0] === 6 ? 50 : 0)),
            'name' => $r[3],
            'compliance' => $r[4],
            'scan_run_status' => 'Done',
            'task' => ['id' => cgss_demo_id($r[1]), 'name' => $r[2], 'app_type' => 'pci'],
            'results' => ['high' => $r[5], 'medium' => $r[6], 'low' => $r[7], 'info' => 12, 'high_and_medium' => $r[5] + $r[6]],
            'results_count' => ['high_and_medium' => $r[5] + $r[6]],
            'files' => $r[0] <= 2 ? ['attestation' => $done, 'executive' => $done, 'detailed' => $done] : [],
        ];
    }
    return $out;
}

function cgss_demo_results() {
    $tags = 'cvss_base_vector=AV:N/AC:L/Au:N/C:N/I:N/A:N|summary=%s|insight=%s|affected=%s|impact=%s|solution=%s|vuldetect=%s|solution_type=VendorFix|qod_type=remote_banner';
    $rows = [
        [1, 'TLS 1.0 and TLS 1.1 protocols enabled', 'www.example.com', '443/tcp', 5.0, 'Medium',
            'The remote server accepts connections encrypted with the deprecated TLS 1.0 or TLS 1.1 protocols.', 'These protocol versions have known weaknesses and are no longer allowed for PCI DSS.', 'Web servers that still accept TLS 1.0 or TLS 1.1.', 'An attacker could decrypt or modify traffic between visitors and the site.', 'Disable TLS 1.0 and TLS 1.1 and allow only TLS 1.2 or newer.', 'Checks which TLS protocol versions the service accepts.'],
        [2, 'Missing HTTP Strict-Transport-Security header', 'shop.example.com', '443/tcp', 2.6, 'Low',
            'The web application does not send the Strict-Transport-Security header.', 'Without HSTS, a browser can be tricked into connecting over plain HTTP.', 'All pages served over HTTPS.', 'Visitors could be downgraded to an unencrypted connection.', 'Send Strict-Transport-Security with a max-age of at least one year.', 'Reads the HTTP response headers.'],
        [3, 'SSH server allows weak MAC algorithms', '198.51.100.7', '22/tcp', 2.6, 'Low',
            'The SSH server offers MAC algorithms that are considered weak.', 'Weak MAC algorithms make it easier to tamper with session data.', 'OpenSSH with default legacy settings.', 'Integrity of SSH sessions could be weakened.', 'Remove hmac-md5 and hmac-sha1 variants from the server configuration.', 'Lists the MAC algorithms the server offers.'],
        [4, 'Cookie without Secure attribute', 'shop.example.com', '443/tcp', 2.1, 'Low',
            'A cookie set by the application is missing the Secure attribute.', 'Cookies without Secure can be sent over unencrypted connections.', 'Session or preference cookies.', 'The cookie value could be read on an insecure network.', 'Set the Secure attribute on every cookie.', 'Reads the Set-Cookie response headers.'],
        [5, 'SMTP server supports STARTTLS with weak ciphers', '203.0.113.25', '25/tcp', 4.3, 'Medium',
            'The mail server negotiates weak cipher suites after STARTTLS.', 'Weak ciphers reduce the protection of mail in transit.', 'Mail servers with legacy cipher lists.', 'Mail in transit could be decrypted by a capable attacker.', 'Restrict the cipher list to modern AEAD ciphers.', 'Negotiates each cipher suite after STARTTLS.'],
    ];
    $out = [];
    foreach ($rows as $r) {
        $out[] = [
            'id' => cgss_demo_id(700 + $r[0]),
            'name' => $r[1],
            'host' => $r[2],
            'hostname' => $r[2],
            'port' => $r[3],
            'severity' => $r[4],
            'threat' => $r[5],
            'qod' => 80,
            'nvt' => ['oid' => '1.3.6.1.4.1.25623.1.0.' . (117270 + $r[0]), 'name' => $r[1], 'cve' => 'NOCVE', 'tags' => sprintf($tags, $r[6], $r[7], $r[8], $r[9], $r[10], $r[11])],
            'task' => ['id' => cgss_demo_id(1), 'name' => 'Online store: quarterly PCI'],
            'overrides' => [],
        ];
    }
    return $out;
}

function cgss_demo_find($items, $id) {
    foreach ($items as $item) {
        if ((string) $item['id'] === (string) $id) {
            return $item;
        }
    }
    return null;
}

function cgss_demo_route($method, $path, $query = []) {
    if ($method !== 'GET') {
        // Writes never leave the site in demo mode.
        return ['status' => 200, 'status_text' => 'Demo mode: nothing was sent to the portal.'];
    }

    if ($path === '/my_details') {
        return ['status' => 200, 'status_text' => 'User details', 'details' => [
            'app_type' => 'pci', 'current_app_type' => 'pci', 'company_name' => 'Example Store',
            'organization' => ['name' => 'Example Store', 'company_name' => 'Example Store', 'available_products' => ['pci' => true, 'vrms' => true, 'penetration' => true, 'darkweb' => false, 'agent_scan' => false, 'webapp' => false]],
        ]];
    }

    $lists = [
        'scans' => cgss_demo_scans(),
        'reports' => cgss_demo_reports(),
        'schedules' => cgss_demo_schedules(),
        'targets' => cgss_demo_targets(),
        'notifications' => cgss_demo_notifications(),
    ];

    if (preg_match('#^/(scans|reports|schedules|targets|notifications)/?$#', $path, $m)) {
        $items = $lists[$m[1]];
        if (!empty($query['per_page'])) {
            $items = array_slice($items, 0, (int) $query['per_page']);
        }
        $out = ['status' => 200, 'status_text' => 'OK', 'pagination' => cgss_demo_page(count($items)), $m[1] => [cgss_demo_keyed($items)]];
        if ($m[1] === 'targets') {
            $out['ips_count'] = 7;
        }
        return $out;
    }
    if (preg_match('#^/(scans|reports|schedules|targets|notifications)/([^/.]+)(/details)?$#', $path, $m)) {
        $item = cgss_demo_find($lists[$m[1]], $m[2]);
        return $item ? ['status' => 200, 'status_text' => 'OK', $m[1] => [[$item['id'] => $item]]] : ['status' => 404, 'status_text' => 'Not found'];
    }
    if ($path === '/results') {
        $items = cgss_demo_results();
        return ['status' => 200, 'status_text' => 'OK', 'pagination' => cgss_demo_page(count($items)), 'results' => cgss_demo_keyed($items)];
    }
    if (preg_match('#^/results/([^/]+)$#', $path, $m)) {
        $item = cgss_demo_find(cgss_demo_results(), $m[1]);
        return $item ? ['status' => 200, 'results' => [$item['id'] => $item]] : ['status' => 404, 'status_text' => 'Not found'];
    }
    if ($path === '/scanners/activated') {
        return ['status' => 200, 'count' => ['total' => 1], 'scanners' => [[cgss_demo_id(601) => ['id' => cgss_demo_id(601), 'name' => 'Clone Systems external scanner']]]];
    }
    if ($path === '/configs') {
        $c = ['id' => cgss_demo_id(501), 'name' => 'PCI ASV full scan', 'app_type' => 'pci'];
        return ['status' => 200, 'pagination' => cgss_demo_page(1), 'configs' => [[$c['id'] => $c]]];
    }
    return ['status' => 404, 'status_text' => 'Not found'];
}
