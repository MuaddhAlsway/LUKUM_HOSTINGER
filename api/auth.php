<?php
/**
 * LAKUM Artspace - Authentication System
 * Handles admin login and session management
 */

require_once 'config.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        // Validate inputs
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        // Map owner email to the actual admin DB email
        $authorizedEmails = ['info@lakumartspace.com', 'muaddhalsway@gmail.com'];
        if (!in_array(strtolower($email), $authorizedEmails)) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        // Always look up by the actual admin account email in the DB
        $lookupEmail = 'info@lakumartspace.com';

        // Check if admin exists
        $stmt = $this->db->prepare('SELECT id, email, password, name, role FROM admins WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $lookupEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        $admin = $result->fetch_assoc();

        // Verify password
        if (!password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        // Set session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['login_time'] = time();

        // Update last login
        $updateStmt = $this->db->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?');
        $updateStmt->bind_param('i', $admin['id']);
        $updateStmt->execute();

        return [
            'success' => true,
            'message' => 'Login successful',
            'admin' => [
                'id' => $admin['id'],
                'email' => $admin['email'],
                'name' => $admin['name'],
                'role' => $admin['role']
            ]
        ];
    }

    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logout successful'];
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }

    /**
     * Check session timeout
     */
    public function checkSessionTimeout() {
        if ($this->isLoggedIn()) {
            $currentTime = time();
            $loginTime = $_SESSION['login_time'] ?? 0;
            
            if (($currentTime - $loginTime) > SESSION_TIMEOUT) {
                session_destroy();
                return false;
            }
            
            // Update login time to extend session
            $_SESSION['login_time'] = $currentTime;
            return true;
        }
        return false;
    }

    /**
     * Get current admin
     */
    public function getCurrentAdmin() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'],
                'email' => $_SESSION['admin_email'],
                'name' => $_SESSION['admin_name'],
                'role' => $_SESSION['admin_role']
            ];
        }
        return null;
    }

    /**
     * Check permission
     */
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $role = $_SESSION['admin_role'];
        
        // Super admin has all permissions
        if ($role === 'super_admin') {
            return true;
        }

        // Check role-based permissions
        $permissions = [
            'admin' => ['view', 'create', 'edit', 'delete'],
            'editor' => ['view', 'create', 'edit'],
            'viewer' => ['view']
        ];

        return in_array($permission, $permissions[$role] ?? []);
    }

    /**
     * Create admin account (for setup only)
     */
    public function createAdmin($email, $password, $name, $role = 'admin') {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare('INSERT INTO admins (email, password, name, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $email, $hashedPassword, $name, $role);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Admin created successfully', 'id' => $this->db->insert_id];
        } else {
            return ['success' => false, 'message' => 'Failed to create admin'];
        }
    }

    /**
     * Change password
     */
    public function changePassword($adminId, $oldPassword, $newPassword) {
        // Get current password
        $stmt = $this->db->prepare('SELECT password FROM admins WHERE id = ?');
        $stmt->bind_param('i', $adminId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Admin not found'];
        }

        $admin = $result->fetch_assoc();

        // Verify old password
        if (!password_verify($oldPassword, $admin['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update password
        $updateStmt = $this->db->prepare('UPDATE admins SET password = ? WHERE id = ?');
        $updateStmt->bind_param('si', $hashedPassword, $adminId);

        if ($updateStmt->execute()) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to change password'];
        }
    }
}

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    $auth = new Auth();
    $result = $auth->login($data['email'] ?? '', $data['password'] ?? '');
    echo json_encode($result);
    exit();
}

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth = new Auth();
    $result = $auth->logout();
    echo json_encode($result);
    exit();
}

// Handle check session request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check') {
    $auth = new Auth();
    if ($auth->checkSessionTimeout()) {
        $admin = $auth->getCurrentAdmin();
        echo json_encode(['success' => true, 'admin' => $admin]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
    }
    exit();
}


