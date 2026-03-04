<?php
/**
 * Fix pricing table - create with correct schema
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Check if pricing table exists
    $result = $db->query("SHOW TABLES LIKE 'pricing'");
    $tableExists = $result && $result->num_rows > 0;
    
    if ($tableExists) {
        // Check if it has the correct columns
        $describeResult = $db->query("DESCRIBE pricing");
        $columns = [];
        while ($row = $describeResult->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        
        // Check if title column exists
        if (!in_array('title', $columns)) {
            // Table exists but doesn't have title column - need to fix it
            // Drop and recreate
            $db->query("DROP TABLE pricing");
            $tableExists = false;
        }
    }
    
    if (!$tableExists) {
        // Create pricing table with correct schema
        $createTableSQL = "
            CREATE TABLE IF NOT EXISTS pricing (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                price INT DEFAULT 0,
                price_unit VARCHAR(50),
                price_sec VARCHAR(100),
                vat_note VARCHAR(255),
                content LONGTEXT,
                display_order INT DEFAULT 0,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_order (display_order),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        if (!$db->query($createTableSQL)) {
            throw new Exception('Failed to create pricing table: ' . $db->getConnection()->error);
        }
        
        // Insert 6 real pricing items
        $pricingData = [
            [
                'title' => 'Hall 1',
                'price' => 12000,
                'price_unit' => 'SAR/day',
                'price_sec' => '',
                'vat_note' => '*(excluding VAT)',
                'content' => '<div class="pricing-accordion__service"><span></span><div><strong>Support Services</strong><p>Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Operational Services</strong><p>Management of essential technical operations, covering lighting, sound systems, air conditioning, and reliable electrical supply.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Custom Events Set Up</strong><p>Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event\'s specific requirements.</p></div></div>',
                'display_order' => 1,
                'is_active' => 1
            ],
            [
                'title' => 'Hall 2',
                'price' => 7200,
                'price_unit' => 'SAR/day',
                'price_sec' => '',
                'vat_note' => '*(excluding VAT)',
                'content' => '<div class="pricing-accordion__service"><span></span><div><strong>Support Services</strong><p>Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Operational Services</strong><p>Management of essential technical operations, covering lighting, sound systems, air conditioning, reliable electrical supply, and the provision of a projector and screen.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Custom Events Set Up</strong><p>Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event\'s specific requirements.</p></div></div>',
                'display_order' => 2,
                'is_active' => 1
            ],
            [
                'title' => 'Hourly Rate',
                'price' => 0,
                'price_unit' => '',
                'price_sec' => 'Hall 1: 1,000 SAR/hour | Hall 2: 600 SAR/hour',
                'vat_note' => '*(excluding VAT)',
                'content' => '<p class="pricing-accordion__intro">Our hourly bookings exclusively for short-format experiences, including:</p><ul class="pricing-accordion__list"><li>Creative workshops and hands-on sessions</li><li>Talks, panels, and intimate discussions</li><li>Music lessons, rehearsals, or small performances</li><li>Yoga, wellness, and movement sessions</li><li>Training sessions or educational programs</li><li>Photoshoots and video filming</li><li>Other short gatherings or community-based activities</li></ul><div class="pricing-accordion__note"><strong>Please note:</strong><p>Hourly rates apply only to short-duration events, typically lasting a few hours. This option is not available for full-day events, exhibitions, or large-scale productions.</p></div>',
                'display_order' => 3,
                'is_active' => 1
            ],
            [
                'title' => 'Set up/Dismantle Day',
                'price' => 3400,
                'price_unit' => 'SAR/day',
                'price_sec' => '',
                'vat_note' => '*(excluding VAT)',
                'content' => '<h4>Setup/Dismantle Day Services</h4><p>This service is exclusively available for multi-day events that require a dedicated day for either pre-event setup or post-event dismantling. It provides essential access and support to ensure a smooth and efficient transition for your main event days.</p><p>We offer flexibility with the times and openings of the space to align precisely with the event organizer\'s needs.</p>',
                'display_order' => 4,
                'is_active' => 1
            ],
            [
                'title' => 'Café',
                'price' => 3400,
                'price_unit' => 'SAR/day',
                'price_sec' => '',
                'vat_note' => '*(excluding VAT)',
                'content' => '<h4>Café Rental</h4><p>This exclusive service is offered when a client chooses to rent the entire space, ensuring a fully private and uninterrupted experience.</p><p>The café can be booked in full, and the rental fee is fully redeemable, allowing the client to benefit from ordering beverages up to the same amount.</p>',
                'display_order' => 5,
                'is_active' => 1
            ],
            [
                'title' => 'Meeting Room',
                'price' => 60,
                'price_unit' => 'SAR/hour',
                'price_sec' => '',
                'vat_note' => '*(excluding VAT)',
                'content' => '<h4>Services Provided</h4><ul class="pricing-accordion__features"><li><strong>Capacity:</strong> Up to six people.</li><li><strong>Inclusive Refreshments:</strong> Complimentary coffee of the day and water provided per person.</li><li><strong>Technology:</strong> Projector included and free high-speed Wi-Fi access.</li><li><strong>Supplies:</strong> Essential notepads and pens.</li></ul>',
                'display_order' => 6,
                'is_active' => 1
            ]
        ];
        
        $insertQuery = 'INSERT INTO pricing (title, price, price_unit, price_sec, vat_note, content, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $db->prepare($insertQuery);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->getConnection()->error);
        }
        
        $inserted = 0;
        foreach ($pricingData as $item) {
            $stmt->bind_param('sisssii', 
                $item['title'],
                $item['price'],
                $item['price_unit'],
                $item['price_sec'],
                $item['vat_note'],
                $item['content'],
                $item['display_order'],
                $item['is_active']
            );
            
            if ($stmt->execute()) {
                $inserted++;
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Pricing table created and populated with 6 real pricing items',
            'inserted' => $inserted,
            'action' => 'created'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Pricing table already exists with correct schema',
            'action' => 'exists'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


