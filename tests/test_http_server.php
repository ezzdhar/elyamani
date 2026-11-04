<?php
/**
 * Real HTTP Server Test for ELYAMANI Contact Form
 * Boots the PHP built-in server and tests real HTTP transactions over the wire.
 */

$host = '127.0.0.1';
$port = 8899;
$url = "http://{$host}:{$port}/api/contact.php";

// Clear any stale rate limit files before test run
$rateDir = sys_get_temp_dir() . '/elyamani_rate_limits';
if (is_dir($rateDir)) {
    foreach (glob($rateDir . '/*.json') as $f) {
        @unlink($f);
    }
}

echo "\nStarting PHP built-in server on {$host}:{$port}...\n";
$serverCmd = sprintf(
    'exec php -S %s:%d -t %s',
    $host,
    $port,
    escapeshellarg(__DIR__ . '/../public')
);

// Under Windows:
$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w']
];

$phpBin = "C:\\Users\\CompuMarts\\AppData\\Local\\Microsoft\\WinGet\\Packages\\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\\php.exe";
$process = proc_open("\"{$phpBin}\" -S {$host}:{$port} -t \"" . realpath(__DIR__ . '/../public') . "\"", $descriptors, $pipes);

if (!is_resource($process)) {
    echo "Failed to start built-in server.\n";
    exit(1);
}

// Give server time to bind
usleep(600000);

function sendHttpRequest(string $method, string $url, array $headers = [], ?string $body = null): array
{
    $headerLines = [];
    foreach ($headers as $k => $v) {
        $headerLines[] = "{$k}: {$v}";
    }

    $ctx = stream_context_create([
        'http' => [
            'method'        => $method,
            'header'        => implode("\r\n", $headerLines),
            'content'       => $body,
            'ignore_errors' => true,
            'timeout'       => 5
        ]
    ]);

    $responseBody = @file_get_contents($url, false, $ctx);
    $responseHeaders = $http_response_header ?? [];

    $statusCode = 0;
    if (!empty($responseHeaders[0]) && preg_match('/HTTP\/\d\.\d\s+(\d+)/', $responseHeaders[0], $m)) {
        $statusCode = (int)$m[1];
    }

    return [
        'status'  => $statusCode,
        'headers' => $responseHeaders,
        'body'    => $responseBody
    ];
}

$passed = 0;
$total = 0;

function check(string $name, bool $ok, string $detail = '') {
    global $passed, $total;
    $total++;
    if ($ok) {
        $passed++;
        echo "  [PASS] {$name}\n";
    } else {
        echo "  [FAIL] {$name} ({$detail})\n";
    }
}

try {
    echo "\nRunning live HTTP tests against {$url}...\n";

    // 1. Live OPTIONS preflight with allowed origin
    $res = sendHttpRequest('OPTIONS', $url, [
        'Origin' => 'https://el-yamani.com'
    ]);
    $corsOriginHeader = '';
    foreach ($res['headers'] as $h) {
        if (stripos($h, 'Access-Control-Allow-Origin:') === 0) {
            $corsOriginHeader = trim(substr($h, strpos($h, ':') + 1));
        }
    }
    check("Live OPTIONS with authorized origin returns 204", $res['status'] === 204, "got {$res['status']}");
    check("Live OPTIONS returns Access-Control-Allow-Origin: https://el-yamani.com", $corsOriginHeader === 'https://el-yamani.com', "got {$corsOriginHeader}");

    // 2. Live OPTIONS preflight with unauthorized origin
    $res = sendHttpRequest('OPTIONS', $url, [
        'Origin' => 'https://unauthorized-domain.com'
    ]);
    check("Live OPTIONS with unauthorized origin returns 403", $res['status'] === 403, "got {$res['status']}");

    // 3. Live POST with unauthorized origin
    $res = sendHttpRequest('POST', $url, [
        'Origin'       => 'https://unauthorized-domain.com',
        'Content-Type' => 'application/json'
    ], json_encode(['test' => true]));
    check("Live POST with unauthorized origin returns 403", $res['status'] === 403, "got {$res['status']}");

    // 4. Live GET method not allowed
    $res = sendHttpRequest('GET', $url, [
        'Origin' => 'https://el-yamani.com'
    ]);
    check("Live GET returns 405 Method Not Allowed", $res['status'] === 405, "got {$res['status']}");

    // 5. Live POST with missing fields
    $res = sendHttpRequest('POST', $url, [
        'Origin'       => 'https://el-yamani.com',
        'Content-Type' => 'application/json'
    ], json_encode(['firstName' => 'علي']));
    check("Live POST with missing fields returns 400 Bad Request", $res['status'] === 400, "got {$res['status']}");
    $bodyJson = json_decode($res['body'], true);
    check("Live POST 400 returns JSON with success=false", ($bodyJson['success'] ?? null) === false, $res['body']);

    // 6. Direct entry via public/contact.php
    $contactUrl = "http://{$host}:{$port}/contact.php";
    $res = sendHttpRequest('OPTIONS', $contactUrl, [
        'Origin' => 'https://el-yamani.com'
    ]);
    check("Live OPTIONS via /contact.php wrapper returns 204", $res['status'] === 204, "got {$res['status']}");

} finally {
    // Terminate built-in server
    proc_terminate($process);
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($process);
    echo "Terminated PHP built-in server.\n";
}

echo "\n------------------------------------------------------\n";
echo "SUMMARY: {$passed} / {$total} live HTTP server tests passed.\n";
echo "------------------------------------------------------\n\n";

if ($passed !== $total) {
    exit(1);
}
