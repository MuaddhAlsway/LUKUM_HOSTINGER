<?php
// Update event dates based on eventcontainer data

require_once __DIR__ . '/../config.php';

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Database connection
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$conn->set_charset('utf8mb4');

// Event data from eventcontainer
$events = [
    // Upcoming Events
    ['name' => 'AMPM', 'date' => '2026-10-21', 'time' => '17:53', 'status' => 'Upcoming'],
    ['name' => 'YSL 2', 'date' => '2026-05-30', 'time' => '13:15', 'status' => 'Upcoming'],
    ['name' => 'Nana', 'date' => '2026-05-30', 'time' => '17:00', 'status' => 'Upcoming'],
    ['name' => 'Snapchat', 'date' => '2026-05-03', 'time' => '18:00', 'status' => 'Upcoming'],
    ['name' => 'Cheval Blanc', 'date' => '2026-04-16', 'time' => '13:15', 'status' => 'Upcoming'],
    
    // Previous Events
    ['name' => 'YSL', 'date' => '2025-09-18', 'time' => '17:00', 'status' => 'Previous'],
    ['name' => 'Maysar', 'date' => '2025-09-13', 'time' => '16:38', 'status' => 'Previous'],
    ['name' => 'Eyewa', 'date' => '2025-06-19', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Anastasia Beverly Hills', 'date' => '2025-05-28', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Dior', 'date' => '2025-05-12', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Chalhoub Group', 'date' => '2025-05-11', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Huda Beauty', 'date' => '2025-04-29', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Oscar De La Renta', 'date' => '2025-02-18', 'time' => '17:15', 'status' => 'Previous'],
    ['name' => 'AMPM', 'date' => '2024-12-19', 'time' => '17:39', 'status' => 'Previous'],
    ['name' => 'Namshi', 'date' => '2024-09-19', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Piaget', 'date' => '2024-05-15', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Mont Blanc', 'date' => '2024-04-30', 'time' => '16:00', 'status' => 'Previous'],
    ['name' => 'Chador', 'date' => '2024-03-09', 'time' => '17:42', 'status' => 'Previous'],
    ['name' => 'Tag Tagia', 'date' => '2024-03-07', 'time' => '17:43', 'status' => 'Previous'],
    ['name' => 'Tiktok', 'date' => '2024-02-28', 'time' => '14:00', 'status' => 'Previous'],
    ['name' => 'American Eagle', 'date' => '2023-09-14', 'time' => '16:45', 'status' => 'Previous'],
    ['name' => 'Social Development Bank', 'date' => '2023-05-28', 'time' => '17:45', 'status' => 'Previous'],
    ['name' => 'La Praire', 'date' => '2023-03-14', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Messika', 'date' => '2022-11-14', 'time' => '13:15', 'status' => 'Previous'],
    ['name' => 'Shiseido', 'date' => '2022-03-25', 'time' => '18:15', 'status' => 'Previous'],
    ['name' => 'Boucheron', 'date' => '2021-10-04', 'time' => '13:15', 'status' => 'Previous'],
];

$results = [
    'success' => true,
    'updated' => 0,
    'notFound' => 0,
    'errors' => [],
    'events' => []
];

foreach ($events as $event) {
    $name = $event['name'];
    $date = $event['date'];
    $time = $event['time'];
    
    // Update event
    $stmt = $conn->prepare("UPDATE events SET event_date = ?, event_time = ? WHERE title = ?");
    $stmt->bind_param("sss", $date, $time, $name);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $results['updated']++;
            $results['events'][] = [
                'name' => $name,
                'date' => $date,
                'time' => $time,
                'status' => 'Updated'
            ];
        } else {
            $results['notFound']++;
            $results['events'][] = [
                'name' => $name,
                'date' => $date,
                'time' => $time,
                'status' => 'Not Found'
            ];
        }
    } else {
        $results['errors'][] = "Error updating $name: " . $stmt->error;
    }
}

$conn->close();
echo json_encode($results);
?>



