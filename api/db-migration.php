<?php
/**
 * Database Migration Script
 * Adds bilingual columns to tables for PHASE 3
 * 
 * This script:
 * 1. Adds *_en and *_ar columns to existing tables
 * 2. Migrates existing data to *_en columns
 * 3. Provides fallback for *_ar columns
 * 
 * Run once to set up bilingual database structure
 */

require_once 'db.php';

header('Content-Type: application/json');

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    if (!$conn || !$db->isConnected()) {
        throw new Exception('Database connection failed');
    }

    $migrations = [];
    $errors = [];

    // ============ BLOGS TABLE ============
    
    // Check if title_en column exists
    $result = $conn->query("SHOW COLUMNS FROM blogs LIKE 'title_en'");
    if ($result->num_rows === 0) {
        // Add title_en column
        if (!$conn->query("ALTER TABLE blogs ADD COLUMN title_en VARCHAR(255) AFTER title")) {
            $errors[] = "Failed to add title_en to blogs: " . $conn->error;
        } else {
            // Copy existing title to title_en
            $conn->query("UPDATE blogs SET title_en = title WHERE title_en IS NULL");
            $migrations[] = "✅ Added title_en to blogs";
        }
    } else {
        $migrations[] = "⏭️ title_en already exists in blogs";
    }

    // Check if title_ar column exists
    $result = $conn->query("SHOW COLUMNS FROM blogs LIKE 'title_ar'");
    if ($result->num_rows === 0) {
        // Add title_ar column
        if (!$conn->query("ALTER TABLE blogs ADD COLUMN title_ar VARCHAR(255) AFTER title_en")) {
            $errors[] = "Failed to add title_ar to blogs: " . $conn->error;
        } else {
            $migrations[] = "✅ Added title_ar to blogs";
        }
    } else {
        $migrations[] = "⏭️ title_ar already exists in blogs";
    }

    // Check if content_en column exists
    $result = $conn->query("SHOW COLUMNS FROM blogs LIKE 'content_en'");
    if ($result->num_rows === 0) {
        // Add content_en column
        if (!$conn->query("ALTER TABLE blogs ADD COLUMN content_en LONGTEXT AFTER content")) {
            $errors[] = "Failed to add content_en to blogs: " . $conn->error;
        } else {
            // Copy existing content to content_en
            $conn->query("UPDATE blogs SET content_en = content WHERE content_en IS NULL");
            $migrations[] = "✅ Added content_en to blogs";
        }
    } else {
        $migrations[] = "⏭️ content_en already exists in blogs";
    }

    // Check if content_ar column exists
    $result = $conn->query("SHOW COLUMNS FROM blogs LIKE 'content_ar'");
    if ($result->num_rows === 0) {
        // Add content_ar column
        if (!$conn->query("ALTER TABLE blogs ADD COLUMN content_ar LONGTEXT AFTER content_en")) {
            $errors[] = "Failed to add content_ar to blogs: " . $conn->error;
        } else {
            $migrations[] = "✅ Added content_ar to blogs";
        }
    } else {
        $migrations[] = "⏭️ content_ar already exists in blogs";
    }

    // ============ EVENTS TABLE ============

    // Check if title_en column exists
    $result = $conn->query("SHOW COLUMNS FROM events LIKE 'title_en'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE events ADD COLUMN title_en VARCHAR(255) AFTER title")) {
            $errors[] = "Failed to add title_en to events: " . $conn->error;
        } else {
            $conn->query("UPDATE events SET title_en = title WHERE title_en IS NULL");
            $migrations[] = "✅ Added title_en to events";
        }
    } else {
        $migrations[] = "⏭️ title_en already exists in events";
    }

    // Check if title_ar column exists
    $result = $conn->query("SHOW COLUMNS FROM events LIKE 'title_ar'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE events ADD COLUMN title_ar VARCHAR(255) AFTER title_en")) {
            $errors[] = "Failed to add title_ar to events: " . $conn->error;
        } else {
            $migrations[] = "✅ Added title_ar to events";
        }
    } else {
        $migrations[] = "⏭️ title_ar already exists in events";
    }

    // Check if description_en column exists
    $result = $conn->query("SHOW COLUMNS FROM events LIKE 'description_en'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE events ADD COLUMN description_en LONGTEXT AFTER description")) {
            $errors[] = "Failed to add description_en to events: " . $conn->error;
        } else {
            $conn->query("UPDATE events SET description_en = description WHERE description_en IS NULL");
            $migrations[] = "✅ Added description_en to events";
        }
    } else {
        $migrations[] = "⏭️ description_en already exists in events";
    }

    // Check if description_ar column exists
    $result = $conn->query("SHOW COLUMNS FROM events LIKE 'description_ar'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE events ADD COLUMN description_ar LONGTEXT AFTER description_en")) {
            $errors[] = "Failed to add description_ar to events: " . $conn->error;
        } else {
            $migrations[] = "✅ Added description_ar to events";
        }
    } else {
        $migrations[] = "⏭️ description_ar already exists in events";
    }

    // ============ PRESS TABLE ============

    // Check if title_en column exists
    $result = $conn->query("SHOW COLUMNS FROM press LIKE 'title_en'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE press ADD COLUMN title_en VARCHAR(255) AFTER title")) {
            $errors[] = "Failed to add title_en to press: " . $conn->error;
        } else {
            $conn->query("UPDATE press SET title_en = title WHERE title_en IS NULL");
            $migrations[] = "✅ Added title_en to press";
        }
    } else {
        $migrations[] = "⏭️ title_en already exists in press";
    }

    // Check if title_ar column exists
    $result = $conn->query("SHOW COLUMNS FROM press LIKE 'title_ar'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE press ADD COLUMN title_ar VARCHAR(255) AFTER title_en")) {
            $errors[] = "Failed to add title_ar to press: " . $conn->error;
        } else {
            $migrations[] = "✅ Added title_ar to press";
        }
    } else {
        $migrations[] = "⏭️ title_ar already exists in press";
    }

    // Check if content_en column exists
    $result = $conn->query("SHOW COLUMNS FROM press LIKE 'content_en'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE press ADD COLUMN content_en LONGTEXT AFTER content")) {
            $errors[] = "Failed to add content_en to press: " . $conn->error;
        } else {
            $conn->query("UPDATE press SET content_en = content WHERE content_en IS NULL");
            $migrations[] = "✅ Added content_en to press";
        }
    } else {
        $migrations[] = "⏭️ content_en already exists in press";
    }

    // Check if content_ar column exists
    $result = $conn->query("SHOW COLUMNS FROM press LIKE 'content_ar'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE press ADD COLUMN content_ar LONGTEXT AFTER content_en")) {
            $errors[] = "Failed to add content_ar to press: " . $conn->error;
        } else {
            $migrations[] = "✅ Added content_ar to press";
        }
    } else {
        $migrations[] = "⏭️ content_ar already exists in press";
    }

    // ============ PRICING TABLE ============

    // Check if title_en column exists
    $result = $conn->query("SHOW COLUMNS FROM pricing LIKE 'title_en'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE pricing ADD COLUMN title_en VARCHAR(255) AFTER title")) {
            $errors[] = "Failed to add title_en to pricing: " . $conn->error;
        } else {
            $conn->query("UPDATE pricing SET title_en = title WHERE title_en IS NULL");
            $migrations[] = "✅ Added title_en to pricing";
        }
    } else {
        $migrations[] = "⏭️ title_en already exists in pricing";
    }

    // Check if title_ar column exists
    $result = $conn->query("SHOW COLUMNS FROM pricing LIKE 'title_ar'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE pricing ADD COLUMN title_ar VARCHAR(255) AFTER title_en")) {
            $errors[] = "Failed to add title_ar to pricing: " . $conn->error;
        } else {
            $migrations[] = "✅ Added title_ar to pricing";
        }
    } else {
        $migrations[] = "⏭️ title_ar already exists in pricing";
    }

    // Check if content_en column exists
    $result = $conn->query("SHOW COLUMNS FROM pricing LIKE 'content_en'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE pricing ADD COLUMN content_en LONGTEXT AFTER content")) {
            $errors[] = "Failed to add content_en to pricing: " . $conn->error;
        } else {
            $conn->query("UPDATE pricing SET content_en = content WHERE content_en IS NULL");
            $migrations[] = "✅ Added content_en to pricing";
        }
    } else {
        $migrations[] = "⏭️ content_en already exists in pricing";
    }

    // Check if content_ar column exists
    $result = $conn->query("SHOW COLUMNS FROM pricing LIKE 'content_ar'");
    if ($result->num_rows === 0) {
        if (!$conn->query("ALTER TABLE pricing ADD COLUMN content_ar LONGTEXT AFTER content_en")) {
            $errors[] = "Failed to add content_ar to pricing: " . $conn->error;
        } else {
            $migrations[] = "✅ Added content_ar to pricing";
        }
    } else {
        $migrations[] = "⏭️ content_ar already exists in pricing";
    }

    // Return results
    echo json_encode([
        'success' => count($errors) === 0,
        'migrations' => $migrations,
        'errors' => $errors,
        'total_migrations' => count($migrations),
        'total_errors' => count($errors)
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

