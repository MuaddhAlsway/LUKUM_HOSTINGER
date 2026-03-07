<?php
/**
 * LAKUM Artspace - Populate Legal Pages (Terms & Privacy)
 * Adds bilingual content to legal_page_translations table
 * Shows ONLY the requested language based on ?lang parameter
 */

header('Content-Type: application/json');
require_once 'config.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    
    // Legal page content - English
    $termsEnglish = [
        'title' => 'Terms & Conditions',
        'content' => '<h2>Lakum Artspace Terms of Use</h2>
<p>By accessing and using the LAKUM Artspace website and services, you accept and agree to be bound by these Terms and Conditions.</p>

<h2>1. Standard Operating Hours</h2>
<p>I acknowledge that Lakum Artspace\'s operating hours are 10:00 AM – 10:00 PM, and that additional hours will incur extra charges.</p>

<h2>2. Liability for Damages</h2>
<p>I am responsible for any damage to the venue, equipment, furniture, or accessories during the entire rental period, including setup and dismantling.</p>

<h2>3. Official Damage Reporting</h2>
<p>Any damage will be documented and reported by a Lakum Artspace representative during the event.</p>

<h2>4. Surface Material Restrictions</h2>
<p>I will not use or apply stickers, vinyl, or adhesive materials on any internal or external surfaces without prior approval from Lakum Artspace.</p>

<h2>5. Event Promotion Policy</h2>
<p>Lakum Artspace is not obligated to promote or advertise external events on its social media or marketing channels.</p>

<h2>6. Branding and Logo Usage</h2>
<p>Use of Lakum Artspace\'s logo or branding elements is strictly prohibited unless formally approved.</p>

<h2>7. Private Area Access</h2>
<p>Access to private areas—including offices, the director\'s office, and storage rooms—is not permitted.</p>

<h2>8. Public Access Areas</h2>
<p>The mezzanine floor, including the shop and café, will remain open to the public during regular hours unless reserved as private for the event.</p>

<h2>9. On-Site Staff Presence</h2>
<p>Lakum Artspace staff—male and female—will be present throughout the event.</p>

<h2>10. Exhibition Approval Process</h2>
<p>Personal or group exhibitions by artists will not be considered unless formally submitted and approved by the Lakum Artspace jury.</p>

<p>Compliance with these terms ensures the preservation of Lakum Artspace\'s professional standards and physical integrity, establishing a clear framework for operational hours, property liability, and brand usage to which all parties are strictly bound throughout the duration of the engagement.</p>'
    ];
    
    // Legal page content - Arabic
    $termsArabic = [
        'title' => 'شروط وأحكام الاستخدام',
        'content' => '<h2>شروط استخدام لكم آرت سبيس</h2>
<p>بالوصول واستخدام موقع ويب لكم آرت سبيس والخدمات، فإنك توافق وتوافق على الالتزام بشروط وأحكام الاستخدام هذه.</p>

<h2>1. ساعات التشغيل القياسية</h2>
<p>أقر بأن ساعات تشغيل لكم آرت سبيس هي من الساعة 10:00 صباحًا إلى الساعة 10:00 مساءً، وأن الساعات الإضافية ستتحمل رسومًا إضافية.</p>

<h2>2. المسؤولية عن الأضرار</h2>
<p>أنا مسؤول عن أي ضرر يلحق بالمكان أو المعدات أو الأثاث أو الملحقات خلال فترة الإيجار بأكملها، بما في ذلك الإعداد والتفكيك.</p>

<h2>3. عملية الإبلاغ الرسمي عن الأضرار</h2>
<p>سيتم توثيق أي ضرر والإبلاغ عنه من قبل ممثل لكم آرت سبيس أثناء الحدث.</p>

<h2>4. قيود مواد السطح</h2>
<p>لن أستخدم أو أطبق ملصقات أو فينيل أو مواد لاصقة على أي أسطح داخلية أو خارجية بدون موافقة مسبقة من لكم آرت سبيس.</p>

<h2>5. سياسة ترويج الأحداث</h2>
<p>لكم آرت سبيس غير ملزمة بترويج أو الإعلان عن الأحداث الخارجية على قنوات وسائل التواصل الاجتماعي أو قنوات التسويق الخاصة بها.</p>

<h2>6. استخدام العلامة التجارية والشعار</h2>
<p>يُحظر استخدام شعار لكم آرت سبيس أو عناصر العلامة التجارية بشكل صارم ما لم يتم الموافقة عليه رسميًا.</p>

<h2>7. الوصول إلى المناطق الخاصة</h2>
<p>لا يُسمح بالوصول إلى المناطق الخاصة - بما في ذلك المكاتب ومكتب المدير وغرف التخزين.</p>

<h2>8. مناطق الوصول العام</h2>
<p>ستبقى أرضية الميزانين، بما في ذلك المتجر والمقهى، مفتوحة للجمهور خلال ساعات العمل العادية ما لم يتم حجزها بشكل خاص للحدث.</p>

<h2>9. وجود الموظفين في الموقع</h2>
<p>سيكون موظفو لكم آرت سبيس - ذكور وإناث - حاضرين طوال الحدث.</p>

<h2>10. عملية الموافقة على المعارض</h2>
<p>لن يتم النظر في المعارض الشخصية أو الجماعية للفنانين ما لم يتم تقديمها رسميًا والموافقة عليها من قبل لجنة لكم آرت سبيس.</p>

<p>يضمن الامتثال لهذه الشروط الحفاظ على معايير لكم آرت سبيس المهنية والسلامة المادية، مما يؤسس إطار عمل واضح لساعات التشغيل والمسؤولية عن الممتلكات واستخدام العلامة التجارية التي تلتزم بها جميع الأطراف بشكل صارم طوال مدة الاتفاق.</p>'
    ];
    
    // Privacy Policy - English
    $privacyEnglish = [
        'title' => 'Privacy Policy',
        'content' => '<h2>Privacy Policy</h2>
<p>LAKUM Artspace ("we," "us," "our," or "Company") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>

<h2>1. Information We Collect</h2>
<p>We may collect information about you in a variety of ways. The information we may collect on the site includes:</p>
<ul>
<li><strong>Personal Data:</strong> Name, email address, phone number, and other contact information you voluntarily provide.</li>
<li><strong>Device Information:</strong> Browser type, IP address, operating system, and pages visited.</li>
<li><strong>Cookies:</strong> We use cookies to enhance your experience and analyze site usage.</li>
</ul>

<h2>2. Use of Your Information</h2>
<p>Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the site to:</p>
<ul>
<li>Generate a personal profile about you so that future visits to the site will be personalized.</li>
<li>Increase the efficiency and operation of the site.</li>
<li>Monitor and analyze usage and trends to improve your experience with the site.</li>
<li>Notify you of updates to the site.</li>
<li>Offer new products, services, and/or recommendations to you.</li>
</ul>

<h2>3. Disclosure of Your Information</h2>
<p>We may share your information in the following situations:</p>
<ul>
<li><strong>By Law or to Protect Rights:</strong> If we believe the release of information is necessary to comply with the law.</li>
<li><strong>Third-Party Service Providers:</strong> We may share your information with third parties that perform services for us.</li>
<li><strong>Business Transfers:</strong> Your information may be transferred as part of a merger, acquisition, or sale of assets.</li>
</ul>

<h2>4. Security of Your Information</h2>
<p>We use administrative, technical, and physical security measures to protect your personal information. However, no method of transmission over the Internet is 100% secure.</p>

<h2>5. Contact Us</h2>
<p>If you have questions or comments about this Privacy Policy, please contact us at:</p>
<p>Email: info@lakumartspace.com<br>
Phone: +966 92 001 2083</p>'
    ];
    
    // Privacy Policy - Arabic
    $privacyArabic = [
        'title' => 'سياسة الخصوصية',
        'content' => '<h2>سياسة الخصوصية</h2>
<p>لكم آرت سبيس ("نحن" أو "الشركة") ملتزمة بحماية خصوصيتك. تشرح سياسة الخصوصية هذه كيفية جمعنا واستخدامنا والكشف عن معلوماتك وحمايتها عند زيارتك لموقعنا الإلكتروني.</p>

<h2>1. المعلومات التي نجمعها</h2>
<p>قد نجمع معلومات عنك بطرق مختلفة. قد تتضمن المعلومات التي قد نجمعها على الموقع ما يلي:</p>
<ul>
<li><strong>البيانات الشخصية:</strong> الاسم وعنوان البريد الإلكتروني ورقم الهاتف ومعلومات الاتصال الأخرى التي تقدمها طواعية.</li>
<li><strong>معلومات الجهاز:</strong> نوع المتصفح وعنوان IP ونظام التشغيل والصفحات التي تمت زيارتها.</li>
<li><strong>ملفات تعريف الارتباط:</strong> نستخدم ملفات تعريف الارتباط لتحسين تجربتك وتحليل استخدام الموقع.</li>
</ul>

<h2>2. استخدام معلوماتك</h2>
<p>يسمح لنا وجود معلومات دقيقة عنك بتزويدك بتجربة سلسة وفعالة ومخصصة. على وجه التحديد، قد نستخدم المعلومات المجمعة عنك عبر الموقع لـ:</p>
<ul>
<li>إنشاء ملف تعريف شخصي عنك بحيث تكون الزيارات المستقبلية للموقع مخصصة.</li>
<li>زيادة كفاءة وتشغيل الموقع.</li>
<li>مراقبة وتحليل الاستخدام والاتجاهات لتحسين تجربتك مع الموقع.</li>
<li>إخطارك بالتحديثات على الموقع.</li>
<li>تقديم منتجات وخدمات و/أو توصيات جديدة لك.</li>
</ul>

<h2>3. الكشف عن معلوماتك</h2>
<p>قد نشارك معلوماتك في الحالات التالية:</p>
<ul>
<li><strong>بموجب القانون أو لحماية الحقوق:</strong> إذا اعتقدنا أن الكشف عن المعلومات ضروري للامتثال للقانون.</li>
<li><strong>مزودو الخدمات من الجهات الخارجية:</strong> قد نشارك معلوماتك مع جهات خارجية تقدم خدمات لنا.</li>
<li><strong>تحويلات الأعمال:</strong> قد يتم نقل معلوماتك كجزء من دمج أو استحواذ أو بيع أصول.</li>
</ul>

<h2>4. أمان معلوماتك</h2>
<p>نستخدم تدابير أمان إدارية وتقنية وفيزيائية لحماية معلوماتك الشخصية. ومع ذلك، لا توجد طريقة نقل عبر الإنترنت آمنة بنسبة 100٪.</p>

<h2>5. اتصل بنا</h2>
<p>إذا كان لديك أسئلة أو تعليقات حول سياسة الخصوصية هذه، يرجى الاتصال بنا على:</p>
<p>البريد الإلكتروني: info@lakumartspace.com<br>
الهاتف: +966 92 001 2083</p>'
    ];
    
    $updated = 0;
    $errors = [];
    
    // Insert or update Terms & Conditions - English
    $query = "INSERT INTO legal_page_translations (page_key, language, title, content, last_updated, created_at) 
              VALUES ('terms', 'en', ?, ?, NOW(), NOW())
              ON DUPLICATE KEY UPDATE title = ?, content = ?, last_updated = NOW()";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $errors[] = "Prepare failed for terms EN";
    } else {
        $stmt->bind_param('ssss', $termsEnglish['title'], $termsEnglish['content'], $termsEnglish['title'], $termsEnglish['content']);
        if ($stmt->execute()) {
            $updated++;
        } else {
            $errors[] = "Execute failed for terms EN";
        }
        $stmt->close();
    }
    
    // Insert or update Terms & Conditions - Arabic
    $query = "INSERT INTO legal_page_translations (page_key, language, title, content, last_updated, created_at) 
              VALUES ('terms', 'ar', ?, ?, NOW(), NOW())
              ON DUPLICATE KEY UPDATE title = ?, content = ?, last_updated = NOW()";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $errors[] = "Prepare failed for terms AR";
    } else {
        $stmt->bind_param('ssss', $termsArabic['title'], $termsArabic['content'], $termsArabic['title'], $termsArabic['content']);
        if ($stmt->execute()) {
            $updated++;
        } else {
            $errors[] = "Execute failed for terms AR";
        }
        $stmt->close();
    }
    
    // Insert or update Privacy Policy - English
    $query = "INSERT INTO legal_page_translations (page_key, language, title, content, last_updated, created_at) 
              VALUES ('privacy', 'en', ?, ?, NOW(), NOW())
              ON DUPLICATE KEY UPDATE title = ?, content = ?, last_updated = NOW()";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $errors[] = "Prepare failed for privacy EN";
    } else {
        $stmt->bind_param('ssss', $privacyEnglish['title'], $privacyEnglish['content'], $privacyEnglish['title'], $privacyEnglish['content']);
        if ($stmt->execute()) {
            $updated++;
        } else {
            $errors[] = "Execute failed for privacy EN";
        }
        $stmt->close();
    }
    
    // Insert or update Privacy Policy - Arabic
    $query = "INSERT INTO legal_page_translations (page_key, language, title, content, last_updated, created_at) 
              VALUES ('privacy', 'ar', ?, ?, NOW(), NOW())
              ON DUPLICATE KEY UPDATE title = ?, content = ?, last_updated = NOW()";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $errors[] = "Prepare failed for privacy AR";
    } else {
        $stmt->bind_param('ssss', $privacyArabic['title'], $privacyArabic['content'], $privacyArabic['title'], $privacyArabic['content']);
        if ($stmt->execute()) {
            $updated++;
        } else {
            $errors[] = "Execute failed for privacy AR";
        }
        $stmt->close();
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Legal pages populated successfully',
        'updated' => $updated,
        'errors' => $errors,
        'note' => 'Terms & Conditions and Privacy Policy now available in English and Arabic'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log('Populate Legal Pages Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
