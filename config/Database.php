<?php
// Database Configuration
// This class handles the connection to the MySQL database
// Usage: $db = new Database(); $conn = $db->connect();

class Database {
    // Database credentials - Change these if using different database
    private $host = 'localhost';
    private $db_name = 'auction_platform';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Connect to database and return connection object
    public function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        // Check for connection errors
        if ($this->conn->connect_error) {
            die('Connection Error: ' . $this->conn->connect_error);
        }

        // Set character encoding to UTF-8
        $this->conn->set_charset("utf8mb4");
        return $this->conn;
    }
}
?>
