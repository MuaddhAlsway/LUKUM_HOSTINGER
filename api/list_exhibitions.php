<?php
require_once 'config.php';

try {
    $db = Database::getInstance();
    $result = $db->getConnection()->query("SELECT id, title_en, exhibition_date FROM exhibitions ORDER BY id ASC");
    
    if ($result) {
        $exhibitions = [];
        while ($row = $result->fetch_assoc()) {
            $exhibitions[] = $row;
        }
        echo json_encode([
            'success' => true,
            'count' => count($exhibitions),
            'exhibitions' => $exhibitions
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Query failed: ' . $db->getConnection()->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
