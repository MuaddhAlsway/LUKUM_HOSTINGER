<?php
/**
 * Restore events immediately
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Insert 5 sample events
    $sql = "INSERT INTO events (title, description, location, event_date, event_time, event_end_time, cover_image, is_featured, category, slug) VALUES
    ('Event 1', 'Description 1', 'Location 1', '2026-03-15', '10:00', '18:00', 'assest/img-4.png', 1, 'exhibition', 'event-1'),
    ('Event 2', 'Description 2', 'Location 2', '2026-03-20', '14:00', '17:00', 'assest/img-4.png', 1, 'workshop', 'event-2'),
    ('Event 3', 'Description 3', 'Location 3', '2026-03-25', '11:00', '16:00', 'assest/img-4.png', 0, 'masterclass', 'event-3'),
    ('Event 4', 'Description 4', 'Location 4', '2026-02-20', '09:00', '17:00', 'assest/img-4.png', 1, 'exhibition', 'event-4'),
    ('Event 5', 'Description 5', 'Location 5', '2026-02-28', '13:00', '16:00', 'assest/img-4.png', 0, 'seminar', 'event-5')";
    
    if (!$conn->query($sql)) {
        throw new Exception('Insert failed: ' . $conn->error);
    }
    
    $inserted = $conn->affected_rows;
    
    // Verify
    $result = $conn->query("SELECT COUNT(*) as count FROM events");
    $row = $result->fetch_assoc();
    $total = $row['count'];
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Events restored',
        'inserted' => $inserted,
        'total_events' => $total
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
?>

