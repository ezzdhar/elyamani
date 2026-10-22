<?php
/**
 * ELYAMANI Architecture & Execution - Contact Form Email Handler for Hostinger
 * Target Email: elyamani.cfd@gmail.com
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
} 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// Read raw JSON or form-urlencoded POST
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    $data = $_POST;
}

// Extract and sanitize fields
$firstName      = isset($data['firstName']) ? trim(strip_tags($data['firstName'])) : '';
$lastName       = isset($data['lastName']) ? trim(strip_tags($data['lastName'])) : '';
$email          = isset($data['email']) ? filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL) : '';
$phone          = isset($data['phone']) ? trim(strip_tags($data['phone'])) : '';
$referral       = isset($data['referral']) ? trim(strip_tags($data['referral'])) : '';
$projectDetails = isset($data['projectDetails']) ? trim(strip_tags($data['projectDetails'])) : (isset($data['message']) ? trim(strip_tags($data['message'])) : '');

$fullName = trim($firstName . ' ' . $lastName);

// Validation
if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($projectDetails)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'يرجى ملء جميع الحقول المطلوبة.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'صيغة البريد الإلكتروني غير صحيحة.']);
    exit;
}

// Email settings
$toEmail = 'elyamani.cfd@gmail.com';
$subject = 'طلب استشارة ومشروع جديد من موقع اليماني: ' . $fullName;

// Format clean phone for WhatsApp
$cleanPhone = preg_replace('/[^0-9]/', '', $phone);
if (preg_match('/^01[0125][0-9]{8}$/', $cleanPhone)) {
    $waPhone = '2' . $cleanPhone;
} else {
    $waPhone = $cleanPhone;
}

// HTML Message Body with ELYMANI Brand Styling (#143135 Dark Green & #BA5D39 Terracotta)
$emailBody = '
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>طلب استشارة ومشروع جديد</title>
</head>
<body style="font-family: \'Segoe UI\', Tahoma, Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 24px; color: #1f2937; direction: rtl; text-align: right;">
  <div style="max-width: 620px; margin: auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(20, 49, 53, 0.08); border: 1px solid #e5e7eb;">
    
    <!-- Header with ELYMANI Brand Colors -->
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
          <td style="padding: 12px 0; color: #0f172a; font-weight: 600; font-size: 15px;">' . htmlspecialchars($fullName) . '</td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; color: #64748b;">البريد الإلكتروني:</td>
          <td style="padding: 12px 0; color: #0f172a;"><a href="mailto:' . htmlspecialchars($email) . '" style="color: #BA5D39; text-decoration: none; font-weight: 500;">' . htmlspecialchars($email) . '</a></td>
        </tr>
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; color: #64748b;">رقم الهاتف:</td>
          <td style="padding: 12px 0; color: #0f172a;"><a href="tel:' . htmlspecialchars($phone) . '" dir="ltr" style="color: #BA5D39; text-decoration: none; font-weight: 500;">' . htmlspecialchars($phone) . '</a></td>
        </tr>
        ' . (!empty($referral) ? '
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; color: #64748b;">كيف سمعت عنا:</td>
          <td style="padding: 12px 0; color: #0f172a;">' . htmlspecialchars($referral) . '</td>
        </tr>
        ' : '') . '
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 12px 0; font-weight: 600; color: #64748b;">تاريخ ووقت الإرسال:</td>
          <td style="padding: 12px 0; color: #64748b;" dir="ltr">' . date('Y-m-d H:i:s') . '</td>
        </tr>
      </table>

      <!-- Project Details Box -->
      <div style="background-color: #FEFAF2; border-right: 4px solid #BA5D39; padding: 18px 20px; border-radius: 6px; margin-bottom: 24px;">
        <h4 style="margin: 0 0 10px 0; color: #143135; font-size: 15px; font-weight: 700;">تفاصيل المشروع / الاستفسار:</h4>
        <p style="margin: 0; line-height: 1.7; color: #334155; white-space: pre-line; font-size: 14px;">' . nl2br(htmlspecialchars($projectDetails)) . '</p>
      </div>

      <!-- Quick Actions -->
      <div style="margin-top: 28px; text-align: center;">
        ' . (!empty($waPhone) ? '
        <a href="https://wa.me/' . htmlspecialchars($waPhone) . '" target="_blank" style="display: inline-block; background-color: #25D366; color: #ffffff; text-decoration: none; padding: 11px 22px; border-radius: 6px; font-size: 14px; font-weight: 600; margin-left: 10px;">
          محادثة عبر واتساب
        </a>
        ' : '') . '
        <a href="mailto:' . htmlspecialchars($email) . '" style="display: inline-block; background-color: #143135; color: #ffffff; text-decoration: none; padding: 11px 22px; border-radius: 6px; font-size: 14px; font-weight: 600;">
          الرد عبر الإيميل
        </a>
      </div>
    </div>

    <!-- Footer Note -->
    <div style="background-color: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;">
      تم إرسال هذا البريد تلقائياً من موقع اليماني (el-yamani.com) إلى ' . htmlspecialchars($toEmail) . '
    </div>

  </div>
</body>
</html>
';

// Standard PHP mail headers with UTF-8 support
$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=UTF-8';
$headers[] = 'From: ELYAMANI Website <' . $toEmail . '>';
$headers[] = 'Reply-To: ' . $fullName . ' <' . $email . '>';
$headers[] = 'X-Mailer: PHP/' . phpversion();

// Send email
$mailSent = @mail($toEmail, '=?UTF-8?B?' . base64_encode($subject) . '?=', $emailBody, implode("\r\n", $headers));

if ($mailSent) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'تم استلام استفسارك بنجاح! سيتواصل معك فريقنا قريباً.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'تعذر إرسال البريد عبر الخادم. يرجى مراجعة إعدادات البريد في الاستضافة.'
    ]);
}
