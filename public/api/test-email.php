<?php
/**
 * ELYAMANI SMTP Diagnostics & Test Tool
 * Use this script to verify your SMTP connection and send a test email to elyamani.cfd@gmail.com.
 * 
 * Usage:
 * Open in your browser: https://el-yamani.com/api/test-email.php?key=elyamani_test
 */

// Safety check: protect with a secret key so public cannot trigger test emails
$secretKey = 'elyamani_test';
if (($_GET['key'] ?? '') !== $secretKey) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    exit("Access Denied. Pass ?key=elyamani_test to run the SMTP diagnostic test.");
}

header('Content-Type: text/html; charset=UTF-8');

// Load PHPMailer
$loaded = false;
$candidates = [
    __DIR__ . '/PHPMailer/PHPMailer.php',
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php'
];

foreach ($candidates as $cand) {
    if (file_exists($cand)) {
        if (str_ends_with($cand, 'PHPMailer.php')) {
            require_once __DIR__ . '/PHPMailer/Exception.php';
            require_once __DIR__ . '/PHPMailer/PHPMailer.php';
            require_once __DIR__ . '/PHPMailer/SMTP.php';
        } else {
            require_once $cand;
        }
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $loaded = true;
            break;
        }
    }
}

if (!$loaded) {
    exit("<h2 style='color:red;'>Error: PHPMailer classes not found in public/api/PHPMailer.</h2><p>Make sure the PHPMailer folder is uploaded to your server inside public/api/.</p>");
}

// Load configuration
$config = [];
$configPaths = [
    __DIR__ . '/../../contact-config.php',
    __DIR__ . '/../contact-config.php',
    __DIR__ . '/contact-config.php'
];
foreach ($configPaths as $cp) {
    if (file_exists($cp)) {
        $loadedConf = include $cp;
        if (is_array($loadedConf)) {
            $config = $loadedConf;
            break;
        }
    }
}

// Also check environment variables
$smtpHost = $config['SMTP_HOST'] ?? getenv('SMTP_HOST') ?: '';
$smtpPort = (int)($config['SMTP_PORT'] ?? getenv('SMTP_PORT') ?: 465);
$smtpSecure = strtolower((string)($config['SMTP_SECURE'] ?? getenv('SMTP_SECURE') ?: 'ssl'));
$smtpUser = $config['SMTP_USER'] ?? getenv('SMTP_USER') ?: '';
$smtpPass = $config['SMTP_PASS'] ?? getenv('SMTP_PASS') ?: '';
$smtpFrom = $config['SMTP_FROM_EMAIL'] ?? getenv('SMTP_FROM_EMAIL') ?: $smtpUser;
$smtpFromName = $config['SMTP_FROM_NAME'] ?? getenv('SMTP_FROM_NAME') ?: 'ELYAMANI Website';
$smtpTo = $config['SMTP_TO_EMAIL'] ?? getenv('SMTP_TO_EMAIL') ?: 'elyamani.cfd@gmail.com';

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>ELYAMANI SMTP Test</title>";
echo "<style>body{font-family:sans-serif;padding:30px;background:#f9fafb;color:#111;} pre{background:#1e1e1e;color:#4af626;padding:15px;border-radius:6px;overflow-x:auto;} .box{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.06);max-width:750px;margin:auto;}</style>";
echo "</head><body><div class='box'>";
echo "<h2>ELYAMANI SMTP Diagnostic Test</h2>";
echo "<p>Testing SMTP delivery to: <strong>" . htmlspecialchars($smtpTo) . "</strong></p>";
echo "<ul>";
echo "<li><strong>Host:</strong> " . htmlspecialchars($smtpHost ?: '(NOT SET)') . "</li>";
echo "<li><strong>Port:</strong> " . htmlspecialchars((string)$smtpPort) . " (" . htmlspecialchars($smtpSecure) . ")</li>";
echo "<li><strong>User:</strong> " . htmlspecialchars($smtpUser ?: '(NOT SET)') . "</li>";
echo "<li><strong>Pass:</strong> " . ($smtpPass ? '******** (' . strlen($smtpPass) . ' chars)' : '<span style="color:red;">NOT SET</span>') . "</li>";
echo "<li><strong>From:</strong> " . htmlspecialchars($smtpFrom ?: '(NOT SET)') . "</li>";
echo "</ul>";

if (empty($smtpHost) || empty($smtpUser) || empty($smtpPass)) {
    echo "<div style='background:#fee2e2;color:#991b1b;padding:15px;border-radius:6px;margin:20px 0;'>";
    echo "<strong>Configuration Missing!</strong><br>";
    echo "Please configure your credentials in <code>contact-config.php</code> (or server environment variables).";
    echo "</div></div></body></html>";
    exit;
}

echo "<h3>Live SMTP Debug Output:</h3>";
echo "<pre>";

$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->SMTPDebug = 2; // Output detailed debug log
    $mail->Debugoutput = function($str, $level) {
        echo htmlspecialchars($str) . "\n";
        flush();
    };

    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->Port       = $smtpPort;
    $mail->Timeout    = 15;

    if ($smtpSecure === 'tls' || $smtpPort === 587) {
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    }

    $mail->CharSet = 'UTF-8';
    $mail->setFrom($smtpFrom, $smtpFromName);
    $mail->addAddress($smtpTo, 'ELYAMANI Admin');
    $mail->Subject = 'اختبار إرسال البريد - موقع اليماني (' . date('Y-m-d H:i:s') . ')';
    $mail->isHTML(true);
    $mail->Body = '<h3>تم استلام البريد التجريبي بنجاح!</h3><p>يعمل خادم SMTP بشكل سليم والرسائل تصل إلى صندوق الوارد الخاص بك مباشرة.</p><p>تاريخ ووقت الاختبار: ' . date('Y-m-d H:i:s') . '</p>';
    $mail->AltBody = 'تم استلام البريد التجريبي بنجاح! يعمل خادم SMTP بشكل سليم.';

    $sent = $mail->send();
    echo "</pre>";

    if ($sent) {
        echo "<div style='background:#dcfce7;color:#166534;padding:15px;border-radius:6px;margin:20px 0;'>";
        echo "<strong>SUCCESS!</strong> Test email has been sent successfully to <strong>" . htmlspecialchars($smtpTo) . "</strong>.<br>Please check your inbox right now!";
        echo "</div>";
    }
} catch (\Exception $e) {
    echo "</pre>";
    echo "<div style='background:#fee2e2;color:#991b1b;padding:15px;border-radius:6px;margin:20px 0;'>";
    echo "<strong>SMTP ERROR:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "</div></body></html>";

