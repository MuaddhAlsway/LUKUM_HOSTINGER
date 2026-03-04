<?php
/**
 * LAKUM Artspace - Insert Spaces Pricing Data
 * Inserts pricing data from public spaces.html into database
 */

header('Content-Type: application/json');
require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    $conn = $db->getConnection();
    
    // Pricing data from spaces.html
    $pricingData = [
        [
            'name' => 'Hall 1',
            'description' => '<h3>Hall 1</h3><p>Full day rental for Hall 1</p><h4>Services Included:</h4><ul><li><strong>Support Services:</strong> Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</li><li><strong>Operational Services:</strong> Management of essential technical operations, covering lighting, sound systems, air conditioning, and reliable electrical supply.</li><li><strong>Custom Events Set Up:</strong> Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event\'s specific requirements.</li></ul>',
            'price' => 12000.00,
            'currency' => 'SAR',
            'duration' => 'per day',
            'features' => 'Support Services, Operational Services, Custom Events Set Up'
        ],
        [
            'name' => 'Hall 2',
            'description' => '<h3>Hall 2</h3><p>Full day rental for Hall 2</p><h4>Services Included:</h4><ul><li><strong>Support Services:</strong> Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</li><li><strong>Operational Services:</strong> Management of essential technical operations, covering lighting, sound systems, air conditioning, reliable electrical supply, and the provision of a projector and screen.</li><li><strong>Custom Events Set Up:</strong> Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event\'s specific requirements.</li></ul>',
            'price' => 7200.00,
            'currency' => 'SAR',
            'duration' => 'per day',
            'features' => 'Support Services, Operational Services, Projector & Screen, Custom Events Set Up'
        ],
        [
            'name' => 'Hourly Rate',
            'description' => '<h3>Hourly Rate</h3><p>Short-format experiences and hourly bookings</p><h4>Available for:</h4><ul><li>Creative workshops and hands-on sessions</li><li>Talks, panels, and intimate discussions</li><li>Music lessons, rehearsals, or small performances</li><li>Yoga, wellness, and movement sessions</li><li>Training sessions or educational programs</li><li>Photoshoots and video filming</li><li>Other short gatherings or community-based activities</li></ul><p><strong>Note:</strong> Hourly rates apply only to short-duration events, typically lasting a few hours. This option is not available for full-day events, exhibitions, or large-scale productions.</p>',
            'price' => null,
            'currency' => 'SAR',
            'duration' => 'Hall 1: 1,000/hour, Hall 2: 600/hour',
            'features' => 'Creative workshops, Talks & panels, Music lessons, Yoga & wellness, Training sessions, Photoshoots, Community activities'
        ],
        [
            'name' => 'Set up/Dismantle Day',
            'description' => '<h3>Set up/Dismantle Day</h3><p>Dedicated day for pre-event setup or post-event dismantling for multi-day events</p><p>This service is exclusively available for multi-day events that require a dedicated day for either pre-event setup or post-event dismantling. It provides essential access and support to ensure a smooth and efficient transition for your main event days.</p><p>We offer flexibility with the times and openings of the space to align precisely with the event organizer\'s needs.</p>',
            'price' => 3400.00,
            'currency' => 'SAR',
            'duration' => 'per day',
            'features' => 'Setup/Dismantle access, Flexible timing, Technical support'
        ],
        [
            'name' => 'Café',
            'description' => '<h3>Café Rental</h3><p>Exclusive café rental when booking the entire space</p><p>This exclusive service is offered when a client chooses to rent the entire space, ensuring a fully private and uninterrupted experience.</p><p>The café can be booked in full, and the rental fee is fully redeemable, allowing the client to benefit from ordering beverages up to the same amount.</p>',
            'price' => 3400.00,
            'currency' => 'SAR',
            'duration' => 'per day',
            'features' => 'Full café rental, Fully redeemable credit for beverages, Private experience'
        ],
        [
            'name' => 'Meeting Room',
            'description' => '<h3>Meeting Room</h3><p>Professional meeting space for up to 6 people</p><h4>Services Provided:</h4><ul><li><strong>Capacity:</strong> Up to six people.</li><li><strong>Inclusive Refreshments:</strong> Complimentary coffee of the day and water provided per person.</li><li><strong>Technology:</strong> Projector included and free high-speed Wi-Fi access.</li><li><strong>Supplies:</strong> Essential notepads and pens.</li></ul>',
            'price' => 60.00,
            'currency' => 'SAR',
            'duration' => 'per hour',
            'features' => 'Capacity up to 6 people, Complimentary coffee & water, Projector, Wi-Fi, Notepads & pens'
        ]
    ];
    
    $inserted_count = 0;
    
    foreach ($pricingData as $pricing) {
        $query = 'INSERT INTO pricing (name, description, price, currency, duration, features, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, 1)';
        
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param('ssdsss', 
            $pricing['name'],
            $pricing['description'],
            $pricing['price'],
            $pricing['currency'],
            $pricing['duration'],
            $pricing['features']
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed for ' . $pricing['name'] . ': ' . $stmt->error);
        }
        
        $inserted_count++;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Spaces pricing data inserted successfully',
        'inserted_count' => $inserted_count
    ]);
    
} catch (Exception $e) {
    error_log('Insert Spaces Pricing Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>


