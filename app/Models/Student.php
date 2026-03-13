<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';
    public $timestamps = false;
    
    protected $fillable = [
        'studentnummer',
        'naam',
        'opleiding',
        'klas',
        'score',
        'password_hash'
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    /**
     * Verify password
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Get score badge class based on score value
     */
    public function getScoreBadgeClass()
    {
        if ($this->score === null) {
            return 'score-none';
        }
        
        if ($this->score >= 5.5) {
            return 'score-high';
        } else {
            return 'score-low';
        }
    }

    /**
     * Get formatted score for display
     */
    public function getFormattedScore()
    {
        return $this->score !== null ? number_format($this->score, 1) : 'Geen';
    }

    /**
     * Find student by studentnummer
     */
    public static function findByStudentnummer($studentnummer)
    {
        return self::where('studentnummer', $studentnummer)->first();
    }

    /**
     * Update student score
     */
    public function updateScore($newScore)
    {
        $this->score = $newScore;
        return $this->save();
    }
}
