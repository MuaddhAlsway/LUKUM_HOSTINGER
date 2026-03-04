<?php
/**
 * Create Admin Account
 * Run this once to create the admin account with proper password hash
 */

require_once 'config.php';

$email = 'admin@lakumartspace.com';
$password = 'admin123';
$name = 'Admin';
$role = 'admin';

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Get database connection
$db = Database::getInstance()->getConnection();

// Check if admin already exists
$checkStmt = $db->prepare('SELECT id FROM admins WHERE email = ?');
$checkStmt->bind_param('s', $email);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    // Update existing admin
    $updateStmt = $db->prepare('UPDATE admins SET password = ?, name = ?, role = ? WHERE email = ?');
    $updateStmt->bind_param('ssss', $hashedPassword, $name, $role, $email);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Admin account updated successfully',
            'email' => $email,
            'password' => $password
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update admin account: ' . $db->error
        ]);
    }
} else {
    // Insert new admin
    $insertStmt = $db->prepare('INSERT INTO admins (email, password, name, role) VALUES (?, ?, ?, ?)');
    $insertStmt->bind_param('ssss', $email, $hashedPassword, $name, $role);
    
    if ($insertStmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Admin account created successfully',
            'email' => $email,
            'password' => $password
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create admin account: ' . $db->error
        ]);
    }
}

$db->close();
?>


