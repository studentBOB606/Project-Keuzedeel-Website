<?php

use App\Models\Student as EloquentStudent;
use App\Models\User as EloquentUser;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Database connection class using Singleton pattern
 * Now wraps Eloquent for backward compatibility
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        // Use Eloquent's connection
        $this->connection = DB::connection()->getPdo();
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
        return DB::select(DB::raw($sql));
    }
    
    public function prepare($sql) {
        // For backward compatibility, return mysqli stmt-like object
        return DB::connection()->getPdo()->prepare($sql);
    }
    
    public function escape($string) {
        return DB::connection()->getPdo()->quote($string);
    }
    
    private function __clone() {}
    
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Student Model - Wrapper around Eloquent for backward compatibility
 */
class Student {
    private $eloquentModel;
    
    public function __construct($data = []) {
        if ($data instanceof EloquentStudent) {
            $this->eloquentModel = $data;
        } else {
            $this->eloquentModel = new EloquentStudent($data);
            if (isset($data['id'])) {
                $this->eloquentModel->id = $data['id'];
                $this->eloquentModel->exists = true;
            }
        }
    }
    
    // Getters
    public function getId() { return $this->eloquentModel->id; }
    public function getStudentnummer() { return $this->eloquentModel->studentnummer; }
    public function getOpleiding() { return $this->eloquentModel->opleiding; }
    public function getKlas() { return $this->eloquentModel->klas; }
    public function getScore() { return $this->eloquentModel->score; }
    public function getPasswordHash() { return $this->eloquentModel->password_hash; }
    
    // Setters
    public function setScore($score) { $this->eloquentModel->score = $score; }
    
    /**
     * Get all students from database
     */
    public static function getAll() {
        $eloquentStudents = EloquentStudent::all();
        $students = [];
        foreach ($eloquentStudents as $eloquentStudent) {
            $students[] = new Student($eloquentStudent);
        }
        return $students;
    }
    
    /**
     * Find student by studentnummer
     */
    public static function findByStudentnummer($studentnummer) {
        $eloquentStudent = EloquentStudent::findByStudentnummer($studentnummer);
        return $eloquentStudent ? new Student($eloquentStudent) : null;
    }
    
    /**
     * Update student score
     */
    public function updateScore($newScore) {
        return $this->eloquentModel->updateScore($newScore);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password) {
        return $this->eloquentModel->verifyPassword($password);
    }
    
    /**
     * Get score badge class based on score value
     */
    public function getScoreBadgeClass() {
        return $this->eloquentModel->getScoreBadgeClass();
    }
    
    /**
     * Get formatted score for display
     */
    public function getFormattedScore() {
        return $this->eloquentModel->getFormattedScore();
    }
}

/**
 * Admin/User Model - Wrapper around Eloquent
 */
class User {
    private $eloquentModel;
    
    public function __construct($data = []) {
        if ($data instanceof EloquentUser) {
            $this->eloquentModel = $data;
        } else {
            $this->eloquentModel = new EloquentUser($data);
            if (isset($data['id'])) {
                $this->eloquentModel->id = $data['id'];
                $this->eloquentModel->exists = true;
            }
        }
    }
    
    // Getters
    public function getId() { return $this->eloquentModel->id; }
    public function getUsername() { return $this->eloquentModel->username; }
    public function getRole() { return $this->eloquentModel->role; }
    
    /**
     * Find user by username and role
     */
    public static function findByUsernameAndRole($username, $role) {
        $eloquentUser = EloquentUser::findByUsernameAndRole($username, $role);
        return $eloquentUser ? new User($eloquentUser) : null;
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password) {
        return $this->eloquentModel->verifyPassword($password);
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin() {
        return $this->eloquentModel->isAdmin();
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
 * Score Manager - Now uses Eloquent
 */
class ScoreManager {
    /**
     * Update student score (admin only)
     */
    public static function updateScore($studentId, $score) {
        if (!Auth::isAdmin()) {
            return false;
        }
        
        $student = EloquentStudent::find($studentId);
        
        if ($student) {
            $student->score = $score;
            return $student->save();
        }
        
        return false;
    }
}
