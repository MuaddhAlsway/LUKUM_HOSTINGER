<?php
/**
 * Create Missing Tables
 * Creates press table and fixes schema issues
 */

header('Content-Type: application/json; charset=utf-8');

$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'RUNNING',
    'steps' => []
];

try {
    // Load config
    $envFile = __DIR__ . '/.env';
    $config = [
        'DB_HOST' => 'localhost',
        'DB_USER' => 'root',
        'DB_PASS' => '',
        'DB_NAME' => 'lakum_artspace',
        'DB_PORT' => 3306
    ];
    
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }
                if (in_array($key, ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME', 'DB_PORT'])) {
                    $config[$key] = $value;
                }
            }
        }
    }
    
    // Connect
    $conn = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Step 1: Create press table if it doesn't exist
    $report['steps'][] = [
        'step' => 1,
        'name' => 'Create Press Table',
        'status' => 'RUNNING'
    ];
    
    $createPressSQL = "CREATE TABLE IF NOT EXISTS press (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        content LONGTEXT,
        excerpt VARCHAR(500),
        source VARCHAR(255),
        cover_image VARCHAR(500),
        press_date DATE,
        url VARCHAR(500),
        category VARCHAR(100),
        slug VARCHAR(255) UNIQUE,
        is_published TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_published (is_published),
        INDEX idx_press_date (press_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($createPressSQL)) {
        $report['steps'][0]['status'] = 'SUCCESS';
        $report['steps'][0]['message'] = 'Press table created or already exists';
    } else {
        $report['steps'][0]['status'] = 'FAILED';
        $report['steps'][0]['error'] = $conn->error;
    }
    
    // Step 2: Insert press data if table is empty
    $report['steps'][] = [
        'step' => 2,
        'name' => 'Insert Press Data',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM press");
    $row = $result->fetch_assoc();
    $pressCount = (int)$row['count'];
    
    if ($pressCount === 0) {
        $pressInserts = [
            "INSERT INTO press (title, content, excerpt, source, category, cover_image, press_date, is_published) VALUES ('LAKUM Artspace Launches New Digital Gallery', 'LAKUM Artspace is proud to announce the launch of its new digital gallery, bringing contemporary art to a global audience. This innovative platform allows art enthusiasts from around the world to explore our collections, attend virtual exhibitions, and participate in online workshops. The digital gallery features high-resolution images, artist interviews, and interactive content.', 'Revolutionary online platform brings art to global audience', 'LAKUM Press', 'Announcement', 'assest/img-4.png', '2026-02-01', 1)",
            "INSERT INTO press (title, content, excerpt, source, category, cover_image, press_date, is_published) VALUES ('Award-Winning Artists Join LAKUM Collective', 'Several award-winning artists have joined the LAKUM Artspace collective, bringing international recognition to Riyadh cultural hub. These accomplished artists bring diverse perspectives and expertise, enriching our community and expanding our programming. Their presence strengthens LAKUM\\'s position as a leading contemporary art destination in the region.', 'International recognition for Riyadh cultural hub', 'Art Today Magazine', 'News', 'assest/img-4.png', '2026-01-28', 1)",
            "INSERT INTO press (title, content, excerpt, source, category, cover_image, press_date, is_published) VALUES ('LAKUM Hosts International Art Summit', 'LAKUM Artspace hosted the International Art Summit bringing together global leaders in contemporary art, curators, collectors, and cultural institutions. The three-day event featured keynote presentations, panel discussions, and networking opportunities. Participants explored emerging trends in contemporary art and discussed the future of cultural exchange.', 'Global leaders in contemporary art gather in Riyadh', 'Global Arts Network', 'Event', 'assest/img-4.png', '2026-01-15', 1)",
            "INSERT INTO press (title, content, excerpt, source, category, cover_image, press_date, is_published) VALUES ('New Sculpture Garden Opens at LAKUM', 'LAKUM Artspace unveiled its new outdoor sculpture garden, providing a dedicated space for contemporary sculpture exhibitions. The garden features works by both established and emerging sculptors, creating an immersive outdoor art experience. The space is designed to complement the indoor galleries and enhance the overall visitor experience.', 'Outdoor exhibition space celebrates contemporary sculpture', 'Architecture & Design', 'Announcement', 'assest/img-4.png', '2026-01-10', 1)",
            "INSERT INTO press (title, content, excerpt, source, category, cover_image, press_date, is_published) VALUES ('LAKUM Partners with International Museums', 'LAKUM Artspace announced strategic partnerships with leading international museums, expanding cultural reach and fostering global collaboration. These partnerships enable the exchange of exhibitions, artists, and knowledge, strengthening LAKUM\\'s position in the international art community. Joint programming and collaborative projects are planned for the coming year.', 'Strategic collaboration expands cultural reach', 'Cultural Affairs', 'Partnership', 'assest/img-4.png', '2025-12-28', 1)"
        ];
        
        $inserted = 0;
        foreach ($pressInserts as $sql) {
            if ($conn->query($sql)) {
                $inserted++;
            }
        }
        
        $report['steps'][1]['status'] = 'SUCCESS';
        $report['steps'][1]['inserted'] = $inserted;
    } else {
        $report['steps'][1]['status'] = 'SKIPPED';
        $report['steps'][1]['message'] = "Press table already has $pressCount records";
    }
    
    // Step 3: Verify all tables exist
    $report['steps'][] = [
        'step' => 3,
        'name' => 'Verify All Tables',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    $requiredTables = ['events', 'blogs', 'press', 'pricing', 'event_gallery', 'admins'];
    $missingTables = array_diff($requiredTables, $tables);
    
    $report['steps'][2]['status'] = empty($missingTables) ? 'SUCCESS' : 'WARNING';
    $report['steps'][2]['tables_found'] = count($tables);
    $report['steps'][2]['tables'] = $tables;
    $report['steps'][2]['missing'] = $missingTables;
    
    // Step 4: Final data count
    $report['steps'][] = [
        'step' => 4,
        'name' => 'Final Data Count',
        'status' => 'RUNNING'
    ];
    
    $dataCounts = [];
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $row = $result->fetch_assoc();
            $dataCounts[$table] = (int)$row['count'];
        }
    }
    
    $report['steps'][3]['status'] = 'SUCCESS';
    $report['steps'][3]['data_counts'] = $dataCounts;
    
    $report['status'] = 'SUCCESS';
    $report['message'] = 'All tables created and data verified!';
    
    $conn->close();
    
} catch (Exception $e) {
    $report['status'] = 'ERROR';
    $report['error'] = $e->getMessage();
}

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>

