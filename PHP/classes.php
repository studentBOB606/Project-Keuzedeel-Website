<?php

/**
 * Database connection class using Singleton pattern
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connection = new mysqli("localhost", "root", "", "studenten");
        
        if ($this->connection->connect_error) {
            throw new Exception("Database connection failed: " . $this->connection->connect_error);
        }
        
        $this->connection->set_charset("utf8mb4");
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql) {
        return $this->connection->query($sql);
    }
    
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
    
    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }
    
    private function __clone() {}
    
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Student Model
 */
class Student {
    private $id;
    private $studentnummer;
    private $opleiding;
    private $klas;
    private $score;
    private $passwordHash;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->studentnummer = $data['studentnummer'] ?? '';
        $this->opleiding = $data['opleiding'] ?? '';
        $this->klas = $data['klas'] ?? '';
        $this->score = $data['score'] ?? null;
        $this->passwordHash = $data['password_hash'] ?? '';
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getStudentnummer() { return $this->studentnummer; }
    public function getOpleiding() { return $this->opleiding; }
    public function getKlas() { return $this->klas; }
    public function getScore() { return $this->score; }
    public function getPasswordHash() { return $this->passwordHash; }
    
    // Setters
    public function setScore($score) { $this->score = $score; }
    
    /**
     * Get all students from database
     */
    public static function getAll() {
        $db = Database::getInstance();
        $result = $db->query("SELECT id, studentnummer, opleiding, klas, score FROM student");
        
        $students = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $students[] = new Student($row);
            }
        }
        
        return $students;
    }
    
    /**
     * Find student by studentnummer
     */
    public static function findByStudentnummer($studentnummer) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, studentnummer, opleiding, klas, score, password_hash FROM student WHERE studentnummer = ?");
        $stmt->bind_param("s", $studentnummer);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            return new Student($result->fetch_assoc());
        }
        
        return null;
    }
    
    /**
     * Update student score
     */
    public function updateScore($newScore) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE student SET score = ? WHERE id = ?");
        $stmt->bind_param("di", $newScore, $this->id);
        
        if ($stmt->execute()) {
            $this->score = $newScore;
            return true;
        }
        
        return false;
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->passwordHash);
    }
    
    /**
     * Get score badge class based on score value
     */
    public function getScoreBadgeClass() {
        if ($this->score === null) {
            return 'score-none';
        }
        
        if ($this->score >= 8) {
            return 'score-high';
        } elseif ($this->score >= 6) {
            return 'score-medium';
        } else {
            return 'score-low';
        }
    }
    
    /**
     * Get formatted score for display
     */
    public function getFormattedScore() {
        return $this->score !== null ? number_format($this->score, 1) : 'Geen';
    }
}

/**
 * Admin/User Model
 */
class User {
    private $id;
    private $username;
    private $role;
    private $passwordHash;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->role = $data['role'] ?? '';
        $this->passwordHash = $data['password_hash'] ?? '';
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getRole() { return $this->role; }
    
    /**
     * Find user by username and role
     */
    public static function findByUsernameAndRole($username, $role) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, role, password_hash FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            return new User($result->fetch_assoc());
        }
        
        return null;
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->passwordHash);
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin() {
        return $this->role === 'admin';
    }
}

/**
 * Authentication Manager
 */
class Auth {
    /**
     * Login user (admin or student)
     */
    public static function login($username, $password, $role) {
        if ($role === 'admin') {
            return self::loginAdmin($username, $password);
        } else {
            return self::loginStudent($username, $password);
        }
    }
    
    /**
     * Login admin user
     */
    private static function loginAdmin($username, $password) {
        $user = User::findByUsernameAndRole($username, 'admin');
        
        if ($user && $user->verifyPassword($password)) {
            $_SESSION['admin_id'] = $user->getId();
            $_SESSION['role'] = 'admin';
            return true;
        }
        
        return false;
    }
    
    /**
     * Login student
     */
    private static function loginStudent($username, $password) {
        $student = Student::findByStudentnummer($username);
        
        if ($student && $student->verifyPassword($password)) {
            $_SESSION['student_id'] = $student->getId();
            $_SESSION['role'] = 'student';
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout current user
     */
    public static function logout() {
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000, 
                $params['path'], 
                $params['domain'], 
                $params['secure'], 
                $params['httponly']
            );
        }
        
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['student_id']) || isset($_SESSION['role']);
    }
    
    /**
     * Check if current user is admin
     */
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Check if current user is student
     */
    public static function isStudent() {
        return (isset($_SESSION['role']) && $_SESSION['role'] === 'student') || 
               (isset($_SESSION['student_id']) && !isset($_SESSION['role']));
    }
    
    /**
     * Require admin access (die if not admin)
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            http_response_code(403);
            die("Access denied - Admin only");
        }
    }
    
    /**
     * Require student access (die if not student)
     */
    public static function requireStudent() {
        if (!self::isStudent()) {
            http_response_code(403);
            die("Access denied - Student only");
        }
    }
    
    /**
     * Require any logged in user
     */
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }
}

/**
 * Score Manager
 */
class ScoreManager {
    /**
     * Update student score (admin only)
     */
    public static function updateScore($studentId, $score) {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE student SET score = ? WHERE id = ?");
        $stmt->bind_param("di", $score, $studentId);
        
        return $stmt->execute();
    }
}
