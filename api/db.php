<?php
/**
 * LAKUM Artspace - Database Connection
 * Handles database connection with error handling
 */

// Load configuration
require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $conn;
    private $host;
    private $user;
    private $pass;
    private $db;
    private $isConnected = false;

    private function __construct() {
        // Use config constants if available, otherwise use defaults
        $this->host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $this->user = defined('DB_USER') ? DB_USER : 'root';
        $this->pass = defined('DB_PASS') ? DB_PASS : '';
        $this->db = defined('DB_NAME') ? DB_NAME : 'lakum_artspace';
        
        // Try to connect to database
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        
        if ($this->conn->connect_error) {
            error_log('Database Connection Error: ' . $this->conn->connect_error);
            $this->isConnected = false;
        } else {
            $this->conn->set_charset('utf8mb4');
            $this->isConnected = true;
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
        return $this->isConnected && $this->conn && !$this->conn->connect_error;
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
?>

