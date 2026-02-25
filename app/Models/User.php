<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    public $timestamps = false;
    
    protected $fillable = [
        'username',
        'role',
        'password_hash'
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * Verify password
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Find user by username and role
     */
    public static function findByUsernameAndRole($username, $role)
    {
        return self::where('username', $username)
                   ->where('role', $role)
                   ->first();
    }
}
