<?php
/**
 * Migration runner - executes all pending migrations
 * This file should be called during deployment to ensure database is up to date
 */

header('Content-Type: application/json');

// List of migration files to run
$migrations = [
    'auto-migrate-pricing-ar.php'
];

$results = [];

foreach ($migrations as $migration) {
    $migrationPath = __DIR__ . '/' . $migration;
    
    if (file_exists($migrationPath)) {
        // Capture output from migration script
        ob_start();
        include $migrationPath;
        $output = ob_get_clean();
        
        $results[$migration] = json_decode($output, true);
    } else {
        $results[$migration] = [
            'success' => false,
            'message' => 'Migration file not found: ' . $migration
        ];
    }
}

echo json_encode([
    'success' => true,
    'migrations_run' => count($migrations),
    'results' => $results,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>

