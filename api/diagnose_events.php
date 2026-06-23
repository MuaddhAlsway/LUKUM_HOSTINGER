<?php
/**
 * Diagnose events table - check what data exists
 */

require_once 'config.php';

$db = Database::getInstance();

echo json_encode([
    'events_table' => [
        'exists' => (bool)$db->getConnection()->query("SHOW TABLES LIKE 'events'")->num_rows,
        'total_count' => $db->getConnection()->query("SELECT COUNT(*) as cnt FROM events")->fetch_assoc()['cnt'],
        'columns' => array_column(
            iterator_to_array($db->getConnection()->query("SHOW COLUMNS FROM events")->fetch_all(MYSQLI_ASSOC)), 
            'Field'
        ),
        'sample_event' => $db->getConnection()->query("SELECT * FROM events LIMIT 1")->fetch_assoc()
    ],
    'exhibitions_table' => [
        'exists' => (bool)$db->getConnection()->query("SHOW TABLES LIKE 'exhibitions'")->num_rows,
        'total_count' => $db->getConnection()->query("SELECT COUNT(*) as cnt FROM exhibitions")->fetch_assoc()['cnt'],
        'sample_exhibition' => $db->getConnection()->query("SELECT id, title_en, cover_image FROM exhibitions LIMIT 1")->fetch_assoc()
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
