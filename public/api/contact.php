<?php
/**
 * ELYAMANI Architecture & Execution - Contact Form Backend Handler
 * Target Inquiry Recipient: elyamani.cfd@gmail.com
 * Supported on Hostinger Shared Hosting & Apache/LiteSpeed/Nginx PHP environments.
 */

define('CONTACT_FORM_LOADED', true);

class ElyamaniContactHandler
{
    private array $config = [];
    private array $responseHeaders = [];
    private int $responseCode = 200;
    private array $responseBody = [];
    private bool $isTestMode = false;
    private ?object $customMailer = null;

    public function __construct(array $customConfig = [], bool $isTestMode = false, ?object $customMailer = null)
    {
        $this->isTestMode = $isTestMode;
        $this->customMailer = $customMailer;
        $this->loadConfiguration($customConfig);
    }

    /**
     * Load configuration from environment variables, external config files, or defaults.
     */
    private function loadConfiguration(array $customConfig = []): void
    {
        $fileConfig = [];

        // Check for secure configuration file outside public web root
        $candidatePaths = [
            __DIR__ . '/../../contact-config.php', // Hostinger: /home/uXXXX/contact-config.php
            __DIR__ . '/../contact-config.php',
            __DIR__ . '/contact-config.php'
        ];

        foreach ($candidatePaths as $path) {
            if (file_exists($path)) {
                $loaded = include $path;
                if (is_array($loaded)) {
                    $fileConfig = $loaded;
                    break;
                }
            }
        }

        // Also check .env file if available
        $envPaths = [
            __DIR__ . '/../../.env',
            __DIR__ . '/../.env',
            __DIR__ . '/.env'
        ];

        foreach ($envPaths as $envPath) {
            if (file_exists($envPath) && is_readable($envPath)) {
                $this->parseEnvFile($envPath);
                break;
            }
        }

        $defaultOrigins = [
            'https://el-yamani.com',
            'https://www.el-yamani.com',
            'https://ezzdhar.github.io'
        ];

        $rawAllowedOrigins = $customConfig['ALLOWED_ORIGINS']
            ?? $this->getEnvVar('ALLOWED_ORIGINS')
            ?? $fileConfig['ALLOWED_ORIGINS']
            ?? $defaultOrigins;

        if (is_string($rawAllowedOrigins)) {
            $allowedOrigins = array_filter(array_map('trim', explode(',', $rawAllowedOrigins)));
        } elseif (is_array($rawAllowedOrigins)) {
            $allowedOrigins = array_values(array_filter(array_map('trim', $rawAllowedOrigins)));
        } else {
            $allowedOrigins = $defaultOrigins;
        }

        $this->config = [
            'SMTP_HOST'         => $customConfig['SMTP_HOST'] ?? $this->getEnvVar('SMTP_HOST') ?? $fileConfig['SMTP_HOST'] ?? '',
            'SMTP_PORT'         => (int)($customConfig['SMTP_PORT'] ?? $this->getEnvVar('SMTP_PORT') ?? $fileConfig['SMTP_PORT'] ?? 465),
            'SMTP_SECURE'       => strtolower((string)($customConfig['SMTP_SECURE'] ?? $this->getEnvVar('SMTP_SECURE') ?? $fileConfig['SMTP_SECURE'] ?? 'ssl')),
            'SMTP_USER'         => $customConfig['SMTP_USER'] ?? $this->getEnvVar('SMTP_USER') ?? $fileConfig['SMTP_USER'] ?? '',
            'SMTP_PASS'         => $customConfig['SMTP_PASS'] ?? $this->getEnvVar('SMTP_PASS') ?? $fileConfig['SMTP_PASS'] ?? '',
            'SMTP_FROM_EMAIL'   => $customConfig['SMTP_FROM_EMAIL'] ?? $this->getEnvVar('SMTP_FROM_EMAIL') ?? $fileConfig['SMTP_FROM_EMAIL'] ?? '',
            'SMTP_FROM_NAME'    => $customConfig['SMTP_FROM_NAME'] ?? $this->getEnvVar('SMTP_FROM_NAME') ?? $fileConfig['SMTP_FROM_NAME'] ?? 'ELYMANI Website',
            'SMTP_TO_EMAIL'     => $customConfig['SMTP_TO_EMAIL'] ?? $this->getEnvVar('SMTP_TO_EMAIL') ?? $fileConfig['SMTP_TO_EMAIL'] ?? 'elyamani.cfd@gmail.com',
            'SMTP_TO_NAME'      => $customConfig['SMTP_TO_NAME'] ?? $this->getEnvVar('SMTP_TO_NAME') ?? $fileConfig['SMTP_TO_NAME'] ?? 'ELYAMANI',
            'ALLOWED_ORIGINS'   => $allowedOrigins,
            'RATE_LIMIT_ENABLED'=> (bool)($customConfig['RATE_LIMIT_ENABLED'] ?? $this->getEnvVar('RATE_LIMIT_ENABLED') ?? $fileConfig['RATE_LIMIT_ENABLED'] ?? true),
            'RATE_LIMIT_MAX'    => (int)($customConfig['RATE_LIMIT_MAX'] ?? $this->getEnvVar('RATE_LIMIT_MAX') ?? $fileConfig['RATE_LIMIT_MAX'] ?? 5),
            'RATE_LIMIT_WINDOW' => (int)($customConfig['RATE_LIMIT_WINDOW'] ?? $this->getEnvVar('RATE_LIMIT_WINDOW') ?? $fileConfig['RATE_LIMIT_WINDOW'] ?? 600),
        ];
    }

    private function getEnvVar(string $key): ?string
    {
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return $val;
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string)$_ENV[$key];
        }
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string)$_SERVER[$key];
        }
        return null;
    }

    private function parseEnvFile(string $filePath): void
    {
        $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) return;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;

            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $val = trim($parts[1]);
                $val = trim($val, "\"'");
                if (!isset($_SERVER[$key]) && !isset($_ENV[$key])) {
                    putenv("$key=$val");
                    $_ENV[$key] = $val;
                }
            }
        }
    }

    /**
     * Handle incoming HTTP or programmatic request.
     */
    public function handle(array $server = [], ?string $rawBody = null): array
    {
        $server = array_merge($_SERVER, $server);
        $method = strtoupper($server['REQUEST_METHOD'] ?? 'GET');
        $origin = $server['HTTP_ORIGIN'] ?? '';

        // All responses must declare Vary: Origin and JSON Content-Type
        $this->addHeader('Vary', 'Origin');
        $this->addHeader('Content-Type', 'application/json; charset=UTF-8');

        // CORS Validation
        $originAllowed = false;
        $matchedOrigin = '';

        if (!empty($origin)) {
            $normalizedOrigin = rtrim(strtolower($origin), '/');
            foreach ($this->config['ALLOWED_ORIGINS'] as $allowed) {
                if (rtrim(strtolower($allowed), '/') === $normalizedOrigin) {
                    $originAllowed = true;
                    $matchedOrigin = $origin;
                    break;
                }
            }

            if (!$originAllowed) {
                return $this->respond(403, [
                    'success' => false,
                    'message' => 'Origin not allowed by CORS policy'
                ]);
            }

            $this->addHeader('Access-Control-Allow-Origin', $matchedOrigin);
            $this->addHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
            $this->addHeader('Access-Control-Allow-Headers', 'Content-Type, Accept');
            $this->addHeader('Access-Control-Max-Age', '86400');
        }

        // Handle OPTIONS preflight
        if ($method === 'OPTIONS') {
            return $this->respond(204, []);
        }

        // Only POST is allowed for form submission
        if ($method !== 'POST') {
            $this->addHeader('Allow', 'POST, OPTIONS');
            return $this->respond(405, [
                'success' => false,
                'message' => 'Method Not Allowed'
            ]);
        }

        // Request Size Limit (Max 50 KB)
        $rawInput = $rawBody !== null ? $rawBody : file_get_contents('php://input');
        if (strlen($rawInput) > 51200) {
            return $this->respond(413, [
                'success' => false,
                'message' => 'حجم الطلب تجاوز الحد المسموح به.'
            ]);
        }

        // Abuse Protection & Rate Limiting
        $clientIp = $server['REMOTE_ADDR'] ?? '127.0.0.1';
        if ($this->config['RATE_LIMIT_ENABLED'] && !$this->checkRateLimit($clientIp)) {
            return $this->respond(429, [
                'success' => false,
                'message' => 'تم تجاوز الحد المسموح من المحاولات. يرجى الانتظار والمحاولة لاحقاً.'
            ]);
        }

        // Decode Payload
        $data = json_decode($rawInput, true);
        if (!is_array($data)) {
            $data = $_POST;
        }
        if (!is_array($data)) {
            $data = [];
        }

        // Honeypot Protection: Bots filling hidden website field are given simulated success without sending
        if (!empty($data['website'])) {
            $this->logError("Spam bot submission caught by honeypot field from IP: {$clientIp}");
            return $this->respond(200, [
                'success' => true,
                'message' => 'تم استلام استفسارك بنجاح! سيتواصل معك فريقنا قريباً.'
            ]);
        }

        // Extract & Sanitize Fields
        $firstName      = $this->sanitizeText($data['firstName'] ?? '', 100);
        $lastName       = $this->sanitizeText($data['lastName'] ?? '', 100);
        $email          = filter_var(trim((string)($data['email'] ?? '')), FILTER_SANITIZE_EMAIL);
        $phone          = $this->sanitizeText($data['phone'] ?? '', 30);
        $referral       = $this->sanitizeText($data['referral'] ?? '', 100);
        $projectDetails = trim(strip_tags((string)($data['projectDetails'] ?? ($data['message'] ?? ''))));

        $fullName = trim($firstName . ' ' . $lastName);

        // Server-Side Field Validation
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($projectDetails)) {
            return $this->respond(400, [
                'success' => false,
                'message' => 'يرجى ملء جميع الحقول المطلوبة.'
            ]);
        }

        // Email Format & Header Injection Check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strpbrk($email, "\r\n") !== false || strlen($email) > 254) {
            return $this->respond(400, [
                'success' => false,
                'message' => 'صيغة البريد الإلكتروني غير صحيحة.'
            ]);
        }

        // Phone Validation (Supports Egyptian & International numbers)
        $cleanPhone = preg_replace('/[\s\-\(\)\.]/', '', $phone);
        $egyptianRegex = '/^(?:\+20|0020|20)?0?1[0125]\d{8}$/';
        $intlRegex = '/^\+?[0-9]{7,20}$/';
        if (!preg_match($egyptianRegex, $cleanPhone) && !preg_match($intlRegex, $cleanPhone)) {
            return $this->respond(400, [
                'success' => false,
                'message' => 'صيغة رقم الهاتف غير صحيحة.'
            ]);
        }

        // Project Details length check
        $detailsLength = $this->safeStrlen($projectDetails);
        if ($detailsLength < 5 || $detailsLength > 10000) {
            return $this->respond(400, [
                'success' => false,
                'message' => 'يرجى تقديم تفاصيل واضحة عن المشروع (بين 5 و 10,000 حرف).'
            ]);
        }

        // Verify Server SMTP Configuration
        if (
            empty($this->config['SMTP_HOST']) ||
            empty($this->config['SMTP_USER']) ||
            empty($this->config['SMTP_PASS']) ||
            empty($this->config['SMTP_FROM_EMAIL'])
        ) {
            $this->logError('SMTP configuration is missing or incomplete. Please configure SMTP credentials on Hostinger.');
            return $this->respond(500, [
                'success' => false,
                'message' => 'خدمة البريد الإلكتروني غير مهيأة على الخادم حالياً. يرجى مراسلتنا مباشرة على elyamani.cfd@gmail.com'
            ]);
        }

        // Load PHPMailer Dependencies
        if (!$this->loadMailerClasses()) {
            $this->logError('PHPMailer classes not found. Ensure Composer dependencies or public/api/PHPMailer is present.');
            return $this->respond(500, [
                'success' => false,
                'message' => 'تعذر تحميل مكتبة إرسال البريد. يرجى التواصل معنا مباشرة على elyamani.cfd@gmail.com'
            ]);
        }

        // Send Email via Authenticated SMTP
        return $this->deliverEmail([
            'firstName'      => $firstName,
            'lastName'       => $lastName,
            'fullName'       => $fullName,
            'email'          => $email,
            'phone'          => $phone,
            'referral'       => $referral,
            'projectDetails' => $projectDetails
        ]);
    }

    /**
     * Load PHPMailer classes via Composer or bundled standalone files.
     */
    private function loadMailerClasses(): bool
    {
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            return true;
        }

        // Check Composer autoload
        $autoloadPaths = [
            __DIR__ . '/vendor/autoload.php',
            __DIR__ . '/../../vendor/autoload.php'
        ];

        foreach ($autoloadPaths as $path) {
            if (file_exists($path)) {
                require_once $path;
                if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                    return true;
                }
            }
        }

        // Check bundled standalone PHPMailer
        $bundledPath = __DIR__ . '/PHPMailer';
        if (file_exists($bundledPath . '/PHPMailer.php') && file_exists($bundledPath . '/SMTP.php') && file_exists($bundledPath . '/Exception.php')) {
            require_once $bundledPath . '/Exception.php';
            require_once $bundledPath . '/PHPMailer.php';
            require_once $bundledPath . '/SMTP.php';
            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                return true;
            }
        }

        return $this->customMailer !== null;
    }

    /**
     * Deliver the message using PHPMailer authenticated SMTP.
     */
    private function deliverEmail(array $fields): array
    {
        $mail = $this->customMailer ?: new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Server SMTP settings
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host       = $this->config['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['SMTP_USER'];
            $mail->Password   = $this->config['SMTP_PASS'];
            $mail->Port       = $this->config['SMTP_PORT'];
            $mail->Timeout    = 15;

            $encryptionTls = defined('PHPMailer\\PHPMailer\\PHPMailer::ENCRYPTION_STARTTLS')
                ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS
                : 'tls';
            $encryptionSsl = defined('PHPMailer\\PHPMailer\\PHPMailer::ENCRYPTION_SMTPS')
                ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                : 'ssl';

            if ($this->config['SMTP_SECURE'] === 'tls' || $this->config['SMTP_PORT'] === 587) {
                $mail->SMTPSecure = $encryptionTls;
            } else {
                $mail->SMTPSecure = $encryptionSsl;
            }

            // Sender & Recipients
            // Using verified domain email address as sender, client email as Reply-To
            $mail->setFrom($this->config['SMTP_FROM_EMAIL'], $this->config['SMTP_FROM_NAME']);
            $mail->addAddress($this->config['SMTP_TO_EMAIL'], $this->config['SMTP_TO_NAME']);
            $mail->addReplyTo($fields['email'], $fields['fullName']);

            // Header injection prevention: Clean newlines from names and subject
            $cleanFullName = preg_replace('/[\r\n]+/', ' ', $fields['fullName']);
            $mail->Subject = 'طلب استشارة ومشروع جديد من موقع اليماني: ' . $cleanFullName;

            // Generate HTML & Plain text bodies
            $mail->isHTML(true);
            $mail->Body    = $this->buildHtmlTemplate($fields);
            $mail->AltBody = $this->buildPlainTextTemplate($fields);

            // Execute sending
            $sent = $mail->send();

            if ($sent) {
                return $this->respond(200, [
                    'success' => true,
                    'message' => 'تم استلام استفسارك بنجاح! سيتواصل معك فريقنا قريباً.'
                ]);
            } else {
                $errorInfo = method_exists($mail, 'getError') ? $mail->getError() : 'SMTP sending failed';
                $this->logError("PHPMailer send returned false: {$errorInfo}");
                return $this->respond(500, [
                    'success' => false,
                    'message' => 'تعذر تسليم البريد عبر خادم SMTP. يرجى مراسلتنا مباشرة على elyamani.cfd@gmail.com'
                ]);
            }
        } catch (\Exception $e) {
            $this->logError("PHPMailer delivery failure: " . $e->getMessage());
            return $this->respond(500, [
                'success' => false,
                'message' => 'تعذر إرسال رسالتك عبر الخادم حالياً. يرجى مراسلتنا مباشرة على elyamani.cfd@gmail.com'
            ]);
        }
    }

    /**
     * Build the branded Arabic HTML email template.
     */
    private function buildHtmlTemplate(array $fields): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $fields['phone']);
        if (preg_match('/^01[0125][0-9]{8}$/', $cleanPhone)) {
            $waPhone = '2' . $cleanPhone;
        } else {
            $waPhone = $cleanPhone;
        }

        $dateStr = date('Y-m-d H:i:s');
        $toEmail = $this->config['SMTP_TO_EMAIL'];

        $escFullName = htmlspecialchars($fields['fullName'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escEmail    = htmlspecialchars($fields['email'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escPhone    = htmlspecialchars($fields['phone'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escReferral = htmlspecialchars($fields['referral'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escDetails  = nl2br(htmlspecialchars($fields['projectDetails'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        $escWaPhone  = htmlspecialchars($waPhone, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $referralRow = !empty($fields['referral']) ? '
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; width: 35%; color: #64748b;">كيف سمعت عنا:</td>
          <td style="padding: 12px 0; color: #0f172a;">' . $escReferral . '</td>
        </tr>' : '';

        $waButton = !empty($waPhone) ? '
        <a href="https://wa.me/' . $escWaPhone . '" target="_blank" style="display: inline-block; background-color: #25D366; color: #ffffff; text-decoration: none; padding: 11px 22px; border-radius: 6px; font-size: 14px; font-weight: 600; margin-left: 10px;">
          محادثة عبر واتساب
        </a>' : '';

        return '
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>طلب استشارة ومشروع جديد</title>
</head>
<body style="font-family: \'Segoe UI\', Tahoma, Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 24px; color: #1f2937; direction: rtl; text-align: right;">
  <div style="max-width: 620px; margin: auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(20, 49, 53, 0.08); border: 1px solid #e5e7eb;">
    
    <!-- Header with ELYMANI Brand Colors (#143135 Dark Green & #BA5D39 Terracotta) -->
    <div style="background-color: #143135; padding: 28px 24px; text-align: center; border-bottom: 4px solid #BA5D39;">
      <h2 style="color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px; font-weight: 700;">ELYMANI - اليماني</h2>
      <p style="color: #BA5D39; margin: 6px 0 0 0; font-size: 13px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">العمارة • التصميم الداخلي • التنفيذ المتكامل</p>
      <p style="color: #d1d5db; margin: 10px 0 0 0; font-size: 14px;">طلب استشارة جديد من نموذج التواصل بالموقع الإلكتروني</p>
    </div>

    <!-- Content Card -->
    <div style="padding: 32px 28px;">
      <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; width: 35%; color: #64748b;">الاسم بالكامل:</td>
          <td style="padding: 12px 0; color: #0f172a; font-weight: 600; font-size: 15px;">' . $escFullName . '</td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; color: #64748b;">البريد الإلكتروني:</td>
          <td style="padding: 12px 0; color: #0f172a;"><a href="mailto:' . $escEmail . '" style="color: #BA5D39; text-decoration: none; font-weight: 500;">' . $escEmail . '</a></td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; color: #64748b;">رقم الهاتف:</td>
          <td style="padding: 12px 0; color: #0f172a;"><a href="tel:' . $escPhone . '" dir="ltr" style="color: #BA5D39; text-decoration: none; font-weight: 500;">' . $escPhone . '</a></td>
        </tr>
        ' . $referralRow . '
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; color: #64748b;">تاريخ ووقت الإرسال:</td>
          <td style="padding: 12px 0; color: #64748b;" dir="ltr">' . $dateStr . '</td>
        </tr>
      </table>

      <!-- Project Details Box -->
      <div style="background-color: #FEFAF2; border-right: 4px solid #BA5D39; padding: 18px 20px; border-radius: 6px; margin-bottom: 24px;">
        <h4 style="margin: 0 0 10px 0; color: #143135; font-size: 15px; font-weight: 700;">تفاصيل المشروع / الاستفسار:</h4>
        <p style="margin: 0; line-height: 1.7; color: #334155; white-space: pre-line; font-size: 14px;">' . $escDetails . '</p>
      </div>

      <!-- Quick Actions -->
      <div style="margin-top: 28px; text-align: center;">
        ' . $waButton . '
        <a href="mailto:' . $escEmail . '" style="display: inline-block; background-color: #143135; color: #ffffff; text-decoration: none; padding: 11px 22px; border-radius: 6px; font-size: 14px; font-weight: 600;">
          الرد عبر الإيميل
        </a>
      </div>
    </div>

    <!-- Footer Note -->
    <div style="background-color: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
      تم إرسال هذا البريد تلقائياً من موقع اليماني (el-yamani.com) إلى ' . htmlspecialchars($toEmail, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '
    </div>

  </div>
</body>
</html>';
    }

    private function buildPlainTextTemplate(array $fields): string
    {
        return "طلب استشارة ومشروع جديد من موقع اليماني\n"
             . "=======================================\n"
             . "الاسم بالكامل: {$fields['fullName']}\n"
             . "البريد الإلكتروني: {$fields['email']}\n"
             . "رقم الهاتف: {$fields['phone']}\n"
             . (!empty($fields['referral']) ? "كيف سمعت عنا: {$fields['referral']}\n" : '')
             . "تاريخ الإرسال: " . date('Y-m-d H:i:s') . "\n\n"
             . "تفاصيل المشروع / الاستفسار:\n"
             . "--------------------------\n"
             . "{$fields['projectDetails']}\n";
    }

    /**
     * File-based rate limiting per client IP (Compatible with shared hosting).
     */
    private function checkRateLimit(string $ip): bool
    {
        $dir = sys_get_temp_dir() . '/elyamani_rate_limits';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (!is_dir($dir) || !is_writable($dir)) {
            return true; // Fail open if temporary directory cannot be created
        }

        $hash = hash('sha256', 'elyamani_ip_' . $ip);
        $file = $dir . '/' . $hash . '.json';
        $now = time();
        $window = $this->config['RATE_LIMIT_WINDOW'];
        $max = $this->config['RATE_LIMIT_MAX'];

        $fp = @fopen($file, 'c+');
        if (!$fp) return true;

        flock($fp, LOCK_EX);

        $size = filesize($file);
        $content = $size > 0 ? fread($fp, $size) : '';
        $timestamps = json_decode($content, true);
        if (!is_array($timestamps)) {
            $timestamps = [];
        }

        // Keep timestamps strictly within the current window
        $timestamps = array_filter($timestamps, function ($ts) use ($now, $window) {
            return ($now - (int)$ts) < $window;
        });

        if (count($timestamps) >= $max) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }

        $timestamps[] = $now;

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(array_values($timestamps)));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    }

    private function safeSubstr(string $str, int $start, int $length): string
    {
        if (function_exists('mb_substr')) {
            return mb_substr($str, $start, $length, 'UTF-8');
        }
        return substr($str, $start, $length);
    }

    private function safeStrlen(string $str): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, 'UTF-8');
        }
        return strlen($str);
    }

    private function sanitizeText(string $input, int $maxLength): string
    {
        $clean = trim(strip_tags($input));
        $clean = preg_replace('/[\r\n\t]+/', ' ', $clean);
        return $this->safeSubstr($clean, 0, $maxLength);
    }

    private function addHeader(string $name, string $value): void
    {
        $this->responseHeaders[$name] = $value;
        if (!$this->isTestMode) {
            header("{$name}: {$value}");
        }
    }

    private function respond(int $code, array $body): array
    {
        $this->responseCode = $code;
        $this->responseBody = $body;

        if (!$this->isTestMode) {
            http_response_code($code);
            if (!empty($body)) {
                echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            exit;
        }

        return [
            'status'  => $code,
            'headers' => $this->responseHeaders,
            'body'    => $body
        ];
    }

    private function logError(string $message): void
    {
        error_log("[ELYAMANI Contact API] " . $message);
    }

    public static function run(): void
    {
        $handler = new self();
        $handler->handle();
    }
}

// Execute request when called directly via HTTP
if (!defined('ELYAMANI_TESTING_MODE')) {
    date_default_timezone_set('Africa/Cairo');
    ElyamaniContactHandler::run();
}
