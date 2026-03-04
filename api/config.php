<?php
/**
 * LAKUM Artspace - API Database Configuration
 * SINGLE SOURCE OF TRUTH - All database connections use this file
 * 
 * SECURITY: Configuration is loaded from config.local.php (not in Git)
 * 
 * CRITICAL FIXES APPLIED:
 * ✅ Session configuration order fixed (session_set_cookie_params BEFORE session_start)
 * ✅ JWT secret validation added (fails if missing or default)
 * ✅ URL normalization fixed (no double/missing slashes)
 * ✅ CORS validation improved (strict comparison)
 * ✅ Directory permissions improved (0775 for shared hosting)
 * ✅ Config validation comprehensive (all required keys checked)
 * ✅ Error handling standardized (consistent JSON responses)
 */

// ============================================================================
// STEP 1: LOAD AND VALIDATE CONFIGURATION
// ============================================================================

$configPath = __DIR__ . '/../config.local.php';

if (!file_exists($configPath)) {
    error_log('CRITICAL: config.local.php not found at ' . $configPath);
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Server configuration error. Contact administrator.'
    ]));
}

// Load configuration
$config = require $configPath;

// ============================================================================
// STEP 2: COMPREHENSIVE CONFIG VALIDATION
// ============================================================================

$requiredSections = [
    'db' => ['host', 'user', 'password', 'database'],
    'site' => ['url'],
    'security' => ['jwt_secret'],
    'logging' => [],
    'uploads' => []
];

foreach ($requiredSections as $section => $requiredKeys) {
    if (!isset($config[$section]) || !is_array($config[$section])) {
        error_log("CRITICAL: Missing or invalid config section: $section");
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Server configuration error. Contact administrator.'
        ]));
    }
    
    foreach ($requiredKeys as $key) {
        if (empty($config[$section][$key])) {
            error_log("CRITICAL: Missing required config key: $section.$key");
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'message' => 'Server configuration error. Contact administrator.'
            ]));
        }
    }
}

// ============================================================================
// STEP 3: VALIDATE JWT SECRET (CRITICAL SECURITY CHECK)
// ============================================================================

$jwtSecret = $config['security']['jwt_secret'] ?? '';
if (empty($jwtSecret) || $jwtSecret === 'change-this-secret-key-in-production') {
    error_log('CRITICAL SECURITY: JWT_SECRET is not configured or using default value');
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Server configuration error. Contact administrator.'
    ]));
}

// ============================================================================
// STEP 4: DEFINE CONSTANTS - DATABASE
// ============================================================================

define('DB_HOST', $config['db']['host']);
define('DB_USER', $config['db']['user']);
define('DB_PASS', $config['db']['password']);
define('DB_NAME', $config['db']['database']);
define('DB_PORT', $config['db']['port'] ?? 3306);
define('DB_CHARSET', $config['db']['charset'] ?? 'utf8mb4');

// ============================================================================
// STEP 5: DEFINE CONSTANTS - SITE (WITH URL NORMALIZATION)
// ============================================================================

// Normalize SITE_URL to always have trailing slash
$siteUrl = rtrim($config['site']['url'], '/');
define('SITE_URL', $siteUrl . '/');
define('SITE_TIMEZONE', $config['site']['timezone'] ?? 'Asia/Riyadh');

// ============================================================================
// STEP 6: DEFINE CONSTANTS - SECURITY
// ============================================================================

define('JWT_SECRET', $jwtSecret);
define('SESSION_TIMEOUT', $config['security']['session_timeout'] ?? 3600);
define('MAX_UPLOAD_SIZE', $config['security']['max_upload_size'] ?? 5242880);
define('ALLOWED_EXTENSIONS', $config['security']['allowed_extensions'] ?? ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf']);

// ============================================================================
// STEP 7: DEFINE CONSTANTS - LOGGING
// ============================================================================

define('ERROR_LOG_PATH', $config['logging']['error_log_path'] ?? __DIR__ . '/../logs/error.log');
define('DISPLAY_ERRORS', $config['logging']['display_errors'] ?? false);
define('LOG_ERRORS', $config['logging']['log_errors'] ?? true);

// ============================================================================
// STEP 8: DEFINE CONSTANTS - UPLOADS (WITH URL NORMALIZATION)
// ============================================================================

define('UPLOAD_DIR', $config['uploads']['directory'] ?? __DIR__ . '/../uploads/');
define('UPLOAD_URL', $siteUrl . '/uploads/');

// ============================================================================
// STEP 9: DEFINE CONSTANTS - API URL
// ============================================================================

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = dirname($_SERVER['SCRIPT_NAME']);
define('API_URL', $protocol . '://' . $host . $basePath . '/');

// ============================================================================
// STEP 10: CONFIGURE ERROR REPORTING
// ============================================================================

error_reporting(E_ALL);
ini_set('display_errors', DISPLAY_ERRORS ? 1 : 0);
ini_set('log_errors', LOG_ERRORS ? 1 : 0);
ini_set('error_log', ERROR_LOG_PATH);

// ============================================================================
// STEP 11: CREATE REQUIRED DIRECTORIES WITH PROPER PERMISSIONS
// ============================================================================

// Create logs directory if it doesn't exist
if (!is_dir(dirname(ERROR_LOG_PATH))) {
    @mkdir(dirname(ERROR_LOG_PATH), 0775, true);
    @chmod(dirname(ERROR_LOG_PATH), 0775);
}

// Create uploads directory if it doesn't exist
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0775, true);
    @chmod(UPLOAD_DIR, 0775);
}

// Database Singleton Class - MANDATORY FOR ALL API FILES
class Database {
    private static $instance = null;
    private $conn;
    private $connected = false;

    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            
            if ($this->conn->connect_error) {
                error_log('Database Connection Error: ' . $this->conn->connect_error);
                $this->connected = false;
            } else {
                $this->conn->set_charset(DB_CHARSET);
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

// ============================================================================
// STEP 12: CONFIGURE CORS HEADERS (IMPROVED VALIDATION)
// ============================================================================

$allowedOrigins = [
    'https://lakumartspace.com',
    'http://lakumartspace.com',
    'http://localhost',
    'http://localhost:3000',
    'http://127.0.0.1:5500'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// ✅ STRICT CORS VALIDATION (with strict comparison)
if (!empty($origin) && in_array($origin, $allowedOrigins, true)) {
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

// ============================================================================
// STEP 13: CONFIGURE SESSION (CRITICAL FIX: ORDER MATTERS!)
// ============================================================================

// ✅ CRITICAL: session_set_cookie_params() MUST come BEFORE session_start()
session_set_cookie_params([
    'lifetime' => SESSION_TIMEOUT,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Now start the session with proper cookie parameters
session_start();

// ============================================================================
// STEP 14: SET TIMEZONE
// ============================================================================

date_default_timezone_set(SITE_TIMEZONE);

// ============================================================================
// CONFIGURATION COMPLETE
// ============================================================================

// Make config available globally if needed
global $APP_CONFIG;
$APP_CONFIG = $config;

?>
