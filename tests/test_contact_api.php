<?php
/**
 * Test Suite for ELYAMANI Contact Form PHP Backend
 * Verifies all security, CORS, validation, error handling, and SMTP scenarios.
 */

define('ELYAMANI_TESTING_MODE', true);
require_once __DIR__ . '/../public/api/contact.php';

class MockMailerSuccess
{
    public string $CharSet = '';
    public string $Host = '';
    public bool $SMTPAuth = false;
    public string $Username = '';
    public string $Password = '';
    public int $Port = 465;
    public int $Timeout = 15;
    public string $SMTPSecure = '';
    public string $Subject = '';
    public string $Body = '';
    public string $AltBody = '';
    public array $from = [];
    public array $to = [];
    public array $replyTo = [];
    public bool $isHTML = false;

    public function isSMTP(): void {}
    public function isHTML(bool $isHtml = true): void { $this->isHTML = $isHtml; }
    public function setFrom(string $address, string $name = ''): void { $this->from = [$address, $name]; }
    public function addAddress(string $address, string $name = ''): void { $this->to[] = [$address, $name]; }
    public function addReplyTo(string $address, string $name = ''): void { $this->replyTo[] = [$address, $name]; }
    public function send(): bool { return true; }
}

class MockMailerAuthFail extends MockMailerSuccess
{
    public function send(): bool
    {
        throw new \Exception("SMTP Error: Could not authenticate (535 Incorrect authentication data)");
    }
}

class MockMailerSendFail extends MockMailerSuccess
{
    public function send(): bool
    {
        return false;
    }
    public function getError(): string
    {
        return "SMTP Error: Data not accepted by remote server (550 Mailbox full)";
    }
}

$totalTests = 0;
$passedTests = 0;

function runTest(string $title, callable $testFn): void
{
    global $totalTests, $passedTests;
    $totalTests++;
    try {
        $testFn();
        $passedTests++;
        echo "  [PASS] {$title}\n";
    } catch (\Throwable $e) {
        echo "  [FAIL] {$title}\n";
        echo "         Error: " . $e->getMessage() . "\n";
    }
}

function assertEquals($expected, $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        $expStr = is_scalar($expected) ? var_export($expected, true) : json_encode($expected);
        $actStr = is_scalar($actual) ? var_export($actual, true) : json_encode($actual);
        throw new \Exception("{$message} - Expected: {$expStr}, got: {$actStr}");
    }
}

function assertTrue($condition, string $message = ''): void
{
    if (!$condition) {
        throw new \Exception("{$message} - Expected true, got false");
    }
}

echo "\n======================================================\n";
echo "   RUNNING ELYAMANI CONTACT FORM BACKEND TEST SUITE   \n";
echo "======================================================\n\n";

// Clear rate limits before running backend tests
$rateDir = sys_get_temp_dir() . '/elyamani_rate_limits';
if (is_dir($rateDir)) {
    foreach (glob($rateDir . '/*.json') as $f) {
        @unlink($f);
    }
}

$validPayload = json_encode([
    'firstName'      => 'أحمد',
    'lastName'       => 'اليماني',
    'phone'          => '01012345678',
    'email'          => 'client@example.com',
    'referral'       => 'Google Search',
    'projectDetails' => 'استفسار عن تصميم وتنفيذ فيلا سكنية في دمياط الجديدة بمسطح 400 متر.'
]);

$validConfig = [
    'SMTP_HOST'         => 'smtp.hostinger.com',
    'SMTP_PORT'         => 465,
    'SMTP_SECURE'       => 'ssl',
    'SMTP_USER'         => 'noreply@el-yamani.com',
    'SMTP_PASS'         => 'secret_test_password',
    'SMTP_FROM_EMAIL'   => 'noreply@el-yamani.com',
    'SMTP_FROM_NAME'    => 'ELYMANI Website',
    'SMTP_TO_EMAIL'     => 'elyamani.cfd@gmail.com',
    'SMTP_TO_NAME'      => 'ELYAMANI',
    'ALLOWED_ORIGINS'   => ['https://el-yamani.com', 'https://www.el-yamani.com', 'https://ezzdhar.github.io'],
    'RATE_LIMIT_ENABLED'=> false // Disabled in tests for isolated repeatability
];

// 1. OPTIONS Preflight with Allowed Origin
runTest("OPTIONS preflight from authorized origin returns 204 with CORS headers", function () use ($validConfig) {
    $handler = new ElyamaniContactHandler($validConfig, true);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'OPTIONS',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ]);

    assertEquals(204, $res['status'], "Status code must be 204");
    assertEquals('https://el-yamani.com', $res['headers']['Access-Control-Allow-Origin'] ?? null, "CORS origin header");
    assertEquals('Origin', $res['headers']['Vary'] ?? null, "Vary header");
    assertTrue(strpos($res['headers']['Access-Control-Allow-Methods'] ?? '', 'POST') !== false, "POST in allow methods");
});

// 2. Unauthorized Origin Rejection (OPTIONS)
runTest("OPTIONS preflight from unauthorized origin returns 403 Forbidden", function () use ($validConfig) {
    $handler = new ElyamaniContactHandler($validConfig, true);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'OPTIONS',
        'HTTP_ORIGIN'    => 'https://malicious-spammer.com'
    ]);

    assertEquals(403, $res['status'], "Status code must be 403");
    assertEquals(false, $res['body']['success'], "Success flag false");
    assertEquals('Origin', $res['headers']['Vary'] ?? null, "Vary header");
});

// 3. Unauthorized Origin Rejection (POST)
runTest("POST request from unauthorized origin returns 403 Forbidden", function () use ($validConfig, $validPayload) {
    $handler = new ElyamaniContactHandler($validConfig, true);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://evil-site.com'
    ], $validPayload);

    assertEquals(403, $res['status'], "Status code must be 403");
    assertEquals(false, $res['body']['success'], "Success flag false");
});

// 4. Invalid Request Method Rejection
runTest("GET request returns 405 Method Not Allowed with Allow header", function () use ($validConfig) {
    $handler = new ElyamaniContactHandler($validConfig, true);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'GET',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ]);

    assertEquals(405, $res['status'], "Status code must be 405");
    assertEquals(false, $res['body']['success'], "Success flag false");
    assertTrue(strpos($res['headers']['Allow'] ?? '', 'POST') !== false, "Allow header contains POST");
});

// 5. Missing Required Fields Rejection
runTest("POST with missing required fields returns 400 Bad Request", function () use ($validConfig) {
    $handler = new ElyamaniContactHandler($validConfig, true);
    $incompletePayload = json_encode([
        'firstName' => 'محمد',
        'phone'     => '01012345678'
        // missing lastName, email, projectDetails
    ]);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ], $incompletePayload);

    assertEquals(400, $res['status'], "Status code must be 400");
    assertEquals(false, $res['body']['success'], "Success must be false");
});

// 6. Invalid Email Rejection
runTest("POST with invalid email format returns 400 Bad Request", function () use ($validConfig) {
    $handler = new ElyamaniContactHandler($validConfig, true);
    $invalidEmailPayload = json_encode([
        'firstName'      => 'سارة',
        'lastName'       => 'علي',
        'phone'          => '01012345678',
        'email'          => 'invalid-email-format-without-at',
        'projectDetails' => 'تفاصيل مشروع ديكور داخلي لعيادة طبية.'
    ]);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ], $invalidEmailPayload);

    assertEquals(400, $res['status'], "Status code must be 400");
    assertEquals(false, $res['body']['success'], "Success must be false");
});

// 7. Missing SMTP Configuration Rejection
runTest("POST with missing SMTP configuration returns 500 without leaking secrets", function () use ($validPayload) {
    $unconfiguredConfig = [
        'SMTP_HOST'          => '',
        'SMTP_USER'          => '',
        'SMTP_PASS'          => '',
        'SMTP_FROM_EMAIL'    => '',
        'ALLOWED_ORIGINS'    => ['https://el-yamani.com'],
        'RATE_LIMIT_ENABLED' => false
    ];
    $handler = new ElyamaniContactHandler($unconfiguredConfig, true);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ], $validPayload);

    assertEquals(500, $res['status'], "Status code must be 500");
    assertEquals(false, $res['body']['success'], "Success must be false");
    assertTrue(empty($res['body']['password']), "Password must not be in response");
});

// 8. SMTP Authentication Failure Handling
runTest("SMTP authentication failure returns 500 and catches exception safely", function () use ($validConfig, $validPayload) {
    $mockMailer = new MockMailerAuthFail();
    $handler = new ElyamaniContactHandler($validConfig, true, $mockMailer);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ], $validPayload);

    assertEquals(500, $res['status'], "Status code must be 500");
    assertEquals(false, $res['body']['success'], "Success must be false");
});

// 9. SMTP Sending Failure Handling
runTest("SMTP sending failure (mailer returning false) returns 500", function () use ($validConfig, $validPayload) {
    $mockMailer = new MockMailerSendFail();
    $handler = new ElyamaniContactHandler($validConfig, true, $mockMailer);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ], $validPayload);

    assertEquals(500, $res['status'], "Status code must be 500");
    assertEquals(false, $res['body']['success'], "Success must be false");
});

// 10. Successful SMTP Acceptance & Delivery
runTest("Successful SMTP acceptance returns 200 with success message and proper recipient", function () use ($validConfig, $validPayload) {
    $mockMailer = new MockMailerSuccess();
    $handler = new ElyamaniContactHandler($validConfig, true, $mockMailer);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ], $validPayload);

    assertEquals(200, $res['status'], "Status code must be 200");
    assertEquals(true, $res['body']['success'], "Success must be true");
    // Verify recipient remains elyamani.cfd@gmail.com
    assertEquals('elyamani.cfd@gmail.com', $mockMailer->to[0][0], "Recipient must be elyamani.cfd@gmail.com");
    // Verify Reply-To contains customer's email
    assertEquals('client@example.com', $mockMailer->replyTo[0][0], "Reply-To must contain customer email");
    // Verify sender is verified domain email
    assertEquals('noreply@el-yamani.com', $mockMailer->from[0], "Sender must be verified domain email");
    // Verify HTML body contains brand styling & Arabic content
    assertTrue(strpos($mockMailer->Body, '#143135') !== false, "HTML body has brand green");
    assertTrue(strpos($mockMailer->Body, '#BA5D39') !== false, "HTML body has brand terracotta");
    assertTrue(strpos($mockMailer->Body, 'اليماني') !== false, "HTML body has Arabic text");
});

// 11. Honeypot Anti-Spam Trap
runTest("Honeypot field filled by spam bot returns 200 without sending email", function () use ($validConfig) {
    $botPayload = json_encode([
        'firstName'      => 'Spam',
        'lastName'       => 'Bot',
        'phone'          => '01012345678',
        'email'          => 'bot@spam.com',
        'projectDetails' => 'Buy cheap links now',
        'website'        => 'http://spamsite.xyz' // honeypot filled!
    ]);
    $mockMailer = new MockMailerSuccess();
    $handler = new ElyamaniContactHandler($validConfig, true, $mockMailer);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ], $botPayload);

    assertEquals(200, $res['status'], "Status must be 200");
    assertEquals(true, $res['body']['success'], "Success must be true");
    assertTrue(empty($mockMailer->to), "Mail must NOT be sent to SMTP");
});

// 12. Request Size Limit Enforcement (> 50 KB)
runTest("Payload exceeding 50 KB limit returns 413 Payload Too Large", function () use ($validConfig) {
    $oversizedPayload = json_encode([
        'firstName'      => 'أحمد',
        'lastName'       => 'علي',
        'phone'          => '01012345678',
        'email'          => 'test@example.com',
        'projectDetails' => str_repeat('A', 60000)
    ]);
    $handler = new ElyamaniContactHandler($validConfig, true);
    $res = $handler->handle([
        'REQUEST_METHOD' => 'POST',
        'HTTP_ORIGIN'    => 'https://el-yamani.com'
    ], $oversizedPayload);

    assertEquals(413, $res['status'], "Status code must be 413");
    assertEquals(false, $res['body']['success'], "Success must be false");
});

// 13. Rate Limiting Enforcement
runTest("Rate limiting rejects requests exceeding threshold with 429", function () use ($validConfig, $validPayload) {
    $rateLimitConfig = array_merge($validConfig, [
        'RATE_LIMIT_ENABLED' => true,
        'RATE_LIMIT_MAX'     => 3,
        'RATE_LIMIT_WINDOW'  => 60
    ]);
    $testIp = '192.0.2.' . mt_rand(1, 254);
    $mockMailer = new MockMailerSuccess();
    $handler = new ElyamaniContactHandler($rateLimitConfig, true, $mockMailer);

    $results = [];
    for ($i = 0; $i < 4; $i++) {
        $results[] = $handler->handle([
            'REQUEST_METHOD' => 'POST',
            'HTTP_ORIGIN'    => 'https://el-yamani.com',
            'REMOTE_ADDR'    => $testIp
        ], $validPayload);
    }

    assertEquals(200, $results[0]['status'], "1st submission status");
    assertEquals(200, $results[1]['status'], "2nd submission status");
    assertEquals(200, $results[2]['status'], "3rd submission status");
    assertEquals(429, $results[3]['status'], "4th submission status must be 429 Too Many Requests");
});

echo "\n------------------------------------------------------\n";
echo "SUMMARY: {$passedTests} / {$totalTests} tests passed.\n";
echo "------------------------------------------------------\n\n";

if ($passedTests !== $totalTests) {
    exit(1);
}
