<?php
/**
 * LAKUM Artspace - Database Configuration
 * SINGLE SOURCE OF TRUTH - All database connections use this file
 * Database is on SAME Hostinger server, use localhost
 */

// Database Configuration - LOCALHOST ONLY
define('DB_HOST', 'localhost');
define('DB_USER', 'u812122863_neama');
define('DB_PASS', 'mySQL!lakum123!nema');
define('DB_NAME', 'u812122863_lakum_artspace');
define('DB_PORT', 3306);

// API Configuration
// Dynamically determine the base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . '://' . $host . $basePath . '/';

define('API_URL', getenv('API_URL') ?: $baseUrl);
define('SITE_URL', getenv('SITE_URL') ?: str_replace('/api/', '/', $baseUrl));
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');

// Security Configuration
define('JWT_SECRET', getenv('JWT_SECRET') ?: 'change-this-secret-key-in-production');
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf']);

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Create logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

// Create uploads directory if it doesn't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Database Singleton Class - MANDATORY FOR ALL API FILES
class Database {
    private static $instance = null;
    private $conn;
    private $connected = false;

    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                error_log('Database Connection Error: ' . $this->conn->connect_error);
                $this->connected = false;
            } else {
                $this->conn->set_charset('utf8mb4');
                $this->connected = true;
            }
        } catch (Exception $e) {
            error_log('Database Exception: ' . $e->getMessage());
            $this->connected = false;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function isConnected() {
        return $this->connected && $this->conn && !$this->conn->connect_error;
    }

    public function query($sql) {
        if (!$this->isConnected()) return false;
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        if (!$this->isConnected()) return false;
        return $this->conn->prepare($sql);
    }

    public function escape($string) {
        if (!$this->isConnected()) return $string;
        return $this->conn->real_escape_string($string);
    }

    public function lastInsertId() {
        if (!$this->isConnected()) return 0;
        return $this->conn->insert_id;
    }

    public function affectedRows() {
        if (!$this->isConnected()) return 0;
        return $this->conn->affected_rows;
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Response Helper Class
class Response {
    public static function success($data = null, $message = 'Success', $code = 200) {
        http_response_code($code);
        return json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($message = 'Error', $code = 400, $data = null) {
        http_response_code($code);
        return json_encode([
            'success' => false,
            'message' => $message,
            'data' => $data
        ]);
    }
}

// Validation Helper Class
class Validator {
    public static function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && (empty($data[$field]) || !isset($data[$field]))) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
            
            if (isset($data[$field]) && !empty($data[$field])) {
                if (strpos($rule, 'email') !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = ucfirst($field) . ' must be a valid email';
                }
                
                if (strpos($rule, 'min:') !== false) {
                    $min = (int)str_replace('min:', '', $rule);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field] = ucfirst($field) . ' must be at least ' . $min . ' characters';
                    }
                }
                
                if (strpos($rule, 'max:') !== false) {
                    $max = (int)str_replace('max:', '', $rule);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field] = ucfirst($field) . ' must not exceed ' . $max . ' characters';
                    }
                }
            }
        }
        
        return empty($errors) ? true : $errors;
    }
}

// CORS Headers - Allow production domain
$allowedOrigins = [
    'https://lakumartspace.com',
    'http://lakumartspace.com',
    'http://localhost',
    'http://localhost:3000',
    'http://127.0.0.1:5500'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Session Configuration
session_start();
session_set_cookie_params([
    'lifetime' => SESSION_TIMEOUT,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Set timezone
date_default_timezone_set('Asia/Riyadh');
?>
