<?php
/**
 * Populate Legal Pages with Arabic Content
 * Adds Arabic translations for Terms and Privacy pages
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn->set_charset('utf8mb4');
    
    $log = [];
    
    // Arabic content for Privacy Policy
    $privacy_ar_title = 'سياسة الخصوصية';
    $privacy_ar_content = <<<'EOT'
<h2>سياسة الخصوصية وبيان حماية البيانات</h2>

<p>في LAKUM Artspace، نحن ملتزمون بحماية خصوصيتك وضمان شفافية كاملة حول كيفية استخدام بياناتك الشخصية.</p>

<h2>1. المعلومات التي نجمعها</h2>

<p>نجمع المعلومات التالية:</p>
<ul>
<li>معلومات التواصل (الاسم، البريد الإلكتروني، رقم الهاتف)</li>
<li>معلومات الحجز والفعاليات</li>
<li>معلومات الدفع والفواتير</li>
<li>بيانات الاستخدام والتصفح</li>
</ul>

<h2>2. كيفية استخدام المعلومات</h2>

<p>نستخدم معلوماتك لـ:</p>
<ul>
<li>معالجة الحجوزات والفعاليات</li>
<li>تحسين خدماتنا</li>
<li>التواصل معك بشأن الفعاليات والعروض</li>
<li>الامتثال للمتطلبات القانونية</li>
</ul>

<h2>3. حماية البيانات</h2>

<p>نستخدم تقنيات التشفير والحماية المتقدمة لحماية بياناتك الشخصية من الوصول غير المصرح به.</p>

<h2>4. حقوقك</h2>

<p>لديك الحق في:</p>
<ul>
<li>الوصول إلى بياناتك الشخصية</li>
<li>تصحيح المعلومات غير الدقيقة</li>
<li>حذف بياناتك</li>
<li>الاعتراض على معالجة بياناتك</li>
</ul>

<h2>5. التواصل معنا</h2>

<p>إذا كان لديك أي أسئلة حول سياسة الخصوصية، يرجى التواصل معنا عبر البريد الإلكتروني أو الهاتف.</p>
EOT;

    // Arabic content for Terms & Conditions
    $terms_ar_title = 'الشروط والأحكام';
    $terms_ar_content = <<<'EOT'
<h2>الشروط والأحكام</h2>

<p>مرحباً بك في LAKUM Artspace. يرجى قراءة الشروط والأحكام التالية بعناية قبل استخدام خدماتنا.</p>

<h2>1. قبول الشروط</h2>

<p>باستخدام موقعنا وخدماتنا، فإنك توافق على الالتزام بهذه الشروط والأحكام.</p>

<h2>2. استخدام الخدمات</h2>

<p>توافق على استخدام خدماتنا فقط للأغراض القانونية والمشروعة.</p>

<h2>3. حقوق الملكية الفكرية</h2>

<p>جميع المحتويات والتصاميم والصور على موقعنا محمية بحقوق الملكية الفكرية.</p>

<h2>4. المسؤولية</h2>

<p>لا نتحمل مسؤولية عن أي أضرار مباشرة أو غير مباشرة ناتجة عن استخدام خدماتنا.</p>

<h2>5. الحجوزات والإلغاء</h2>

<p>يمكنك إلغاء حجزك وفقاً لسياسة الإلغاء المحددة في وقت الحجز.</p>

<h2>6. التعديلات على الشروط</h2>

<p>نحتفظ بالحق في تعديل هذه الشروط في أي وقت. سيتم إخطارك بأي تغييرات جوهرية.</p>

<h2>7. القانون الحاكم</h2>

<p>تخضع هذه الشروط والأحكام لقوانين المملكة العربية السعودية.</p>

<h2>8. التواصل</h2>

<p>إذا كان لديك أي استفسارات، يرجى التواصل معنا.</p>
EOT;

    // Check if records exist
    $check_query = 'SELECT COUNT(*) as count FROM legal_page_translations WHERE language = "ar"';
    $result = $conn->query($check_query);
    $row = $result->fetch_assoc();
    $arabic_count = $row['count'];
    
    $log[] = "Current Arabic records: " . $arabic_count;
    
    // Insert or update Privacy Arabic
    $query = 'INSERT INTO legal_page_translations (page_key, language, title, content, last_updated) 
              VALUES (?, ?, ?, ?, NOW())
              ON DUPLICATE KEY UPDATE 
              title = VALUES(title), 
              content = VALUES(content), 
              last_updated = NOW()';
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    // Insert Privacy Arabic
    $page_key = 'privacy';
    $language = 'ar';
    $stmt->bind_param('ssss', $page_key, $language, $privacy_ar_title, $privacy_ar_content);
    if ($stmt->execute()) {
        $log[] = "✅ Privacy Arabic inserted/updated - Affected rows: " . $stmt->affected_rows;
    } else {
        $log[] = "❌ Privacy Arabic failed: " . $stmt->error;
    }
    
    // Insert Terms Arabic
    $page_key = 'terms';
    $language = 'ar';
    $stmt->bind_param('ssss', $page_key, $language, $terms_ar_title, $terms_ar_content);
    if ($stmt->execute()) {
        $log[] = "✅ Terms Arabic inserted/updated - Affected rows: " . $stmt->affected_rows;
    } else {
        $log[] = "❌ Terms Arabic failed: " . $stmt->error;
    }
    
    $stmt->close();
    
    // Verify all records
    $verify_query = 'SELECT page_key, language, title FROM legal_page_translations ORDER BY page_key, language';
    $result = $conn->query($verify_query);
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    
    $log[] = "✅ Total records after update: " . count($records);
    
    echo json_encode([
        'success' => true,
        'message' => 'Legal pages populated successfully',
        'log' => $log,
        'records' => $records
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
