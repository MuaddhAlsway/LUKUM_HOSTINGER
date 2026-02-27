<?php
/**
 * Direct Insert Pricing - Bypasses Database class
 * Uses mysqli directly for maximum compatibility
 */

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

try {
    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    // Check if pricing table exists
    $result = $conn->query("SHOW TABLES LIKE 'pricing'");
    if ($result->num_rows === 0) {
        // Create table
        $createSQL = "
            CREATE TABLE pricing (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                price INT,
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
        
        if (!$conn->query($createSQL)) {
            throw new Exception('Failed to create table: ' . $conn->error);
        }
    }
    
    // Delete existing data
    $conn->query('DELETE FROM pricing');
    
    // Pricing data
    $pricingData = [
        [1, 'Hall 1', 12000, 'SAR/day', '', '*(excluding VAT)', '<div class="pricing-accordion__service"><span></span><div><strong>Support Services</strong><p>Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Operational Services</strong><p>Management of essential technical operations, covering lighting, sound systems, air conditioning, and reliable electrical supply.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Custom Events Set Up</strong><p>Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event\'s specific requirements.</p></div></div>', 1, 1],
        [2, 'Hall 2', 7200, 'SAR/day', '', '*(excluding VAT)', '<div class="pricing-accordion__service"><span></span><div><strong>Support Services</strong><p>Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Operational Services</strong><p>Management of essential technical operations, covering lighting, sound systems, air conditioning, reliable electrical supply, and the provision of a projector and screen.</p></div></div><div class="pricing-accordion__service"><span></span><div><strong>Custom Events Set Up</strong><p>Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event\'s specific requirements.</p></div></div>', 2, 1],
        [3, 'Hourly Rate', 0, '', 'Hall 1: 1,000 SAR/hour | Hall 2: 600 SAR/hour', '*(excluding VAT)', '<p class="pricing-accordion__intro">Our hourly bookings exclusively for short-format experiences, including:</p><ul class="pricing-accordion__list"><li>Creative workshops and hands-on sessions</li><li>Talks, panels, and intimate discussions</li><li>Music lessons, rehearsals, or small performances</li><li>Yoga, wellness, and movement sessions</li><li>Training sessions or educational programs</li><li>Photoshoots and video filming</li><li>Other short gatherings or community-based activities</li></ul><div class="pricing-accordion__note"><strong>Please note:</strong><p>Hourly rates apply only to short-duration events, typically lasting a few hours. This option is not available for full-day events, exhibitions, or large-scale productions.</p></div>', 3, 1],
        [4, 'Set up/Dismantle Day', 3400, 'SAR/day', '', '*(excluding VAT)', '<h4>Setup/Dismantle Day Services</h4><p>This service is exclusively available for multi-day events that require a dedicated day for either pre-event setup or post-event dismantling. It provides essential access and support to ensure a smooth and efficient transition for your main event days.</p><p>We offer flexibility with the times and openings of the space to align precisely with the event organizer\'s needs.</p>', 4, 1],
        [5, 'Café', 3400, 'SAR/day', '', '*(excluding VAT)', '<h4>Café Rental</h4><p>This exclusive service is offered when a client chooses to rent the entire space, ensuring a fully private and uninterrupted experience.</p><p>The café can be booked in full, and the rental fee is fully redeemable, allowing the client to benefit from ordering beverages up to the same amount.</p>', 5, 1],
        [6, 'Meeting Room', 60, 'SAR/hour', '', '*(excluding VAT)', '<h4>Services Provided</h4><ul class="pricing-accordion__features"><li><strong>Capacity:</strong> Up to six people.</li><li><strong>Inclusive Refreshments:</strong> Complimentary coffee of the day and water provided per person.</li><li><strong>Technology:</strong> Projector included and free high-speed Wi-Fi access.</li><li><strong>Supplies:</strong> Essential notepads and pens.</li></ul>', 6, 1]
    ];
    
    // Insert data
    $inserted = 0;
    foreach ($pricingData as $item) {
        $query = "INSERT INTO pricing (id, title, price, price_unit, price_sec, vat_note, content, display_order, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('isississi', 
            $item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7], $item[8]
        );
        
        if ($stmt->execute()) {
            $inserted++;
        }
        $stmt->close();
    }
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => "Successfully inserted $inserted pricing items",
        'count' => $inserted
    ]);
    
} catch (Exception $e) {
    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

