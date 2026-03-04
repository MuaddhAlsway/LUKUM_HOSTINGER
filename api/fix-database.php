<?php
/**
 * LAKUM Artspace - Fix Database
 * Fixes table names and inserts missing data
 */

header('Content-Type: application/json; charset=utf-8');

$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'RUNNING',
    'steps' => [],
    'errors' => []
];

try {
    // Load configuration
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
    
    // Connect to database
    $conn = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Step 1: Check if event_galleries table exists
    $report['steps'][] = [
        'step' => 1,
        'name' => 'Check for event_galleries table',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query("SHOW TABLES LIKE 'event_galleries'");
    $hasEventGalleries = $result && $result->num_rows > 0;
    
    if ($hasEventGalleries) {
        $report['steps'][0]['status'] = 'FOUND';
        $report['steps'][0]['message'] = 'event_galleries table exists';
        
        // Rename it to event_gallery
        $report['steps'][] = [
            'step' => 2,
            'name' => 'Rename event_galleries to event_gallery',
            'status' => 'RUNNING'
        ];
        
        if ($conn->query("RENAME TABLE event_galleries TO event_gallery")) {
            $report['steps'][1]['status'] = 'SUCCESS';
            $report['steps'][1]['message'] = 'Table renamed successfully';
        } else {
            $report['steps'][1]['status'] = 'FAILED';
            $report['steps'][1]['error'] = $conn->error;
        }
    } else {
        $report['steps'][0]['status'] = 'NOT_FOUND';
        $report['steps'][0]['message'] = 'event_galleries table does not exist';
    }
    
    // Step 2: Check current event count
    $report['steps'][] = [
        'step' => 3,
        'name' => 'Check current event count',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM events");
    $row = $result->fetch_assoc();
    $currentEventCount = (int)$row['count'];
    
    $report['steps'][2]['status'] = 'SUCCESS';
    $report['steps'][2]['current_events'] = $currentEventCount;
    
    // Step 3: Insert missing events if needed
    if ($currentEventCount < 12) {
        $report['steps'][] = [
            'step' => 4,
            'name' => 'Insert missing events',
            'status' => 'RUNNING'
        ];
        
        $events = [
            ['Contemporary Art Exhibition 2026', 'Explore the latest contemporary art pieces from emerging artists around the world.', 'Main Gallery', '2026-03-15', '10:00', '18:00', 'assest/img-4.png', 1, 'exhibition'],
            ['Photography Workshop - Advanced Techniques', 'Learn professional photography techniques from industry experts.', 'Studio A', '2026-03-20', '14:00', '17:00', 'assest/img-4.png', 1, 'workshop'],
            ['Digital Art Masterclass - 3D Design', 'Master digital art tools and techniques with focus on 3D design and animation.', 'Tech Studio', '2026-03-25', '11:00', '16:00', 'assest/img-4.png', 0, 'masterclass'],
            ['Cultural Heritage Exhibition', 'Celebrated the rich cultural heritage through art.', 'Heritage Hall', '2026-02-20', '09:00', '17:00', 'assest/img-4.png', 1, 'exhibition'],
            ['Sculpture Seminar - Materials and Techniques', 'Discovered the art of sculpture with hands-on experience.', 'Sculpture Studio', '2026-02-28', '13:00', '16:00', 'assest/img-4.png', 0, 'seminar'],
            ['Painting Fundamentals - Watercolor Basics', 'Mastered the fundamentals of painting with focus on watercolor techniques.', 'Studio B', '2026-02-14', '10:00', '13:00', 'assest/img-4.png', 0, 'workshop'],
            ['Modern Art Retrospective 2025', 'A comprehensive look at modern art movements and their impact on contemporary culture.', 'Main Gallery', '2026-01-30', '10:00', '18:00', 'assest/img-4.png', 0, 'exhibition'],
            ['Ceramic Arts Festival', 'Celebrate the beauty and craftsmanship of ceramic art with local and international artists.', 'Exhibition Hall', '2026-01-15', '11:00', '19:00', 'assest/img-4.png', 0, 'festival'],
            ['Digital Photography Showcase', 'Discover stunning digital photography works from talented photographers worldwide.', 'Studio A', '2026-01-10', '14:00', '18:00', 'assest/img-4.png', 0, 'showcase'],
            ['Abstract Expressionism Workshop', 'Explore abstract expressionism techniques and create your own masterpiece.', 'Studio B', '2025-12-20', '13:00', '16:00', 'assest/img-4.png', 0, 'workshop'],
            ['Sculpture Exhibition - Form and Space', 'Experience the interplay of form and space through contemporary sculpture.', 'Sculpture Garden', '2025-12-05', '10:00', '17:00', 'assest/img-4.png', 0, 'exhibition'],
            ['Traditional Art Techniques Seminar', 'Learn traditional art techniques passed down through generations.', 'Heritage Hall', '2025-11-25', '15:00', '18:00', 'assest/img-4.png', 0, 'seminar']
        ];
        
        $inserted = 0;
        foreach ($events as $event) {
            $stmt = $conn->prepare('INSERT IGNORE INTO events (title, description, location, event_date, event_time, event_end_time, cover_image, is_featured, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssssii', $event[0], $event[1], $event[2], $event[3], $event[4], $event[5], $event[6], $event[7], $event[8]);
            if ($stmt->execute()) {
                $inserted++;
            }
        }
        
        $report['steps'][3]['status'] = 'SUCCESS';
        $report['steps'][3]['inserted'] = $inserted;
    }
    
    // Step 4: Check press count
    $report['steps'][] = [
        'step' => 5,
        'name' => 'Check press count',
        'status' => 'RUNNING'
    ];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM press");
    $row = $result->fetch_assoc();
    $currentPressCount = (int)$row['count'];
    
    $report['steps'][4]['status'] = 'SUCCESS';
    $report['steps'][4]['current_press'] = $currentPressCount;
    
    // Step 5: Insert missing press if needed
    if ($currentPressCount < 5) {
        $report['steps'][] = [
            'step' => 6,
            'name' => 'Insert missing press releases',
            'status' => 'RUNNING'
        ];
        
        $pressReleases = [
            ['LAKUM Artspace Launches New Digital Gallery', 'LAKUM Artspace is proud to announce the launch of its new digital gallery, bringing contemporary art to a global audience. This innovative platform allows art enthusiasts from around the world to explore our collections, attend virtual exhibitions, and participate in online workshops. The digital gallery features high-resolution images, artist interviews, and interactive content.', 'Revolutionary online platform brings art to global audience', 'LAKUM Press', 'Announcement', 'assest/img-4.png', '2026-02-01'],
            ['Award-Winning Artists Join LAKUM Collective', 'Several award-winning artists have joined the LAKUM Artspace collective, bringing international recognition to Riyadh cultural hub. These accomplished artists bring diverse perspectives and expertise, enriching our community and expanding our programming. Their presence strengthens LAKUM\'s position as a leading contemporary art destination in the region.', 'International recognition for Riyadh cultural hub', 'Art Today Magazine', 'News', 'assest/img-4.png', '2026-01-28'],
            ['LAKUM Hosts International Art Summit', 'LAKUM Artspace hosted the International Art Summit bringing together global leaders in contemporary art, curators, collectors, and cultural institutions. The three-day event featured keynote presentations, panel discussions, and networking opportunities. Participants explored emerging trends in contemporary art and discussed the future of cultural exchange.', 'Global leaders in contemporary art gather in Riyadh', 'Global Arts Network', 'Event', 'assest/img-4.png', '2026-01-15'],
            ['New Sculpture Garden Opens at LAKUM', 'LAKUM Artspace unveiled its new outdoor sculpture garden, providing a dedicated space for contemporary sculpture exhibitions. The garden features works by both established and emerging sculptors, creating an immersive outdoor art experience. The space is designed to complement the indoor galleries and enhance the overall visitor experience.', 'Outdoor exhibition space celebrates contemporary sculpture', 'Architecture & Design', 'Announcement', 'assest/img-4.png', '2026-01-10'],
            ['LAKUM Partners with International Museums', 'LAKUM Artspace announced strategic partnerships with leading international museums, expanding cultural reach and fostering global collaboration. These partnerships enable the exchange of exhibitions, artists, and knowledge, strengthening LAKUM\'s position in the international art community. Joint programming and collaborative projects are planned for the coming year.', 'Strategic collaboration expands cultural reach', 'Cultural Affairs', 'Partnership', 'assest/img-4.png', '2025-12-28']
        ];
        
        $inserted = 0;
        foreach ($pressReleases as $press) {
            $stmt = $conn->prepare('INSERT IGNORE INTO press (title, content, excerpt, source, category, cover_image, press_date, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, 1)');
            $stmt->bind_param('sssssss', $press[0], $press[1], $press[2], $press[3], $press[4], $press[5], $press[6]);
            if ($stmt->execute()) {
                $inserted++;
            }
        }
        
        $report['steps'][5]['status'] = 'SUCCESS';
        $report['steps'][5]['inserted'] = $inserted;
    }
    
    // Final check
    $report['steps'][] = [
        'step' => 7,
        'name' => 'Final data count',
        'status' => 'RUNNING'
    ];
    
    $counts = [];
    $tables = ['events', 'blogs', 'press', 'pricing', 'event_gallery', 'admins'];
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $row = $result->fetch_assoc();
            $counts[$table] = (int)$row['count'];
        }
    }
    
    $report['steps'][6]['status'] = 'SUCCESS';
    $report['steps'][6]['data_counts'] = $counts;
    
    $report['status'] = 'SUCCESS';
    $report['message'] = 'Database fixed successfully!';
    
    $conn->close();
    
} catch (Exception $e) {
    $report['status'] = 'ERROR';
    $report['error'] = $e->getMessage();
}

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>


