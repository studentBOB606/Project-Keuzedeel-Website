<?php
session_start();
require_once 'classes.php';

// Handle score updates (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id']) && isset($_POST['score'])) {
    $student_id = intval($_POST['student_id']);
    $score = floatval($_POST['score']);
    
    if (ScoreManager::updateScore($student_id, $score)) {
        header("Location: keuzedeel.php");
        exit;
    }
}

// Get all students
$students = Student::getAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuzedeel Beheer - Studenten Scores</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .table-container {
            background: rgba(255, 255, 255, .96);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
            overflow-x: auto;
            margin-top: 32px;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .student-table thead {
            background: linear-gradient(135deg, var(--green-800), var(--green-700));
        }
        .student-table th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--white);
            border-bottom: 2px solid var(--gold-500);
        }
        .student-table tbody tr {
            border-bottom: 1px solid var(--slate-200);
            transition: all .2s;
        }
        .student-table tbody tr:hover {
            background: rgba(212, 175, 55, .08);
            transform: scale(1.01);
        }
        .student-table td {
            padding: 20px;
            color: var(--slate-700);
            font-size: 15px;
        }
        .score-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 14px;
            min-width: 60px;
            text-align: center;
        }
        .score-high { background: #d1fae5; color: #065f46; }
        .score-medium { background: #fef3c7; color: #92400e; }
        .score-low { background: #fee2e2; color: #991b1b; }
        .score-none { background: var(--slate-200); color: var(--slate-700); }
        .score-input {
            padding: 8px 12px;
            border: 2px solid var(--slate-300);
            border-radius: 8px;
            font-size: 14px;
            width: 80px;
            transition: border-color .2s;
        }
        .score-input:focus {
            outline: none;
            border-color: var(--gold-500);
        }
        .update-btn {
            padding: 8px 16px;
            background: var(--green-700);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all .2s;
        }
        .update-btn:hover {
            background: var(--green-800);
            transform: translateY(-2px);
        }
        .action-cell {
            display: flex;
            gap: 8px;
            align-items: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-inner">
            <a href="index.php" class="brand">
                <div class="brand-logo">K</div>
                <div class="brand-text">
                    <strong>Keuzedeel Portal</strong>
                    <small>Student Management</small>
                </div>
            </a>
            <nav class="nav">
                <a href="index.php">Dashboard</a>
                <a href="keuzedeel.php">Scores</a>
                <?php if (Auth::isAdmin()): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-badge">
                📊 Score Management
            </div>
            <h1>Studenten Cijfers</h1>
            <p class="hero-subtitle">Beheer individuele student scores</p>
        </div>

        <!-- Students Table -->
        <div class="table-container">
            <h2 style="color: var(--green-950); font-size: 28px; font-weight: 700; margin-bottom: 8px;">Student Overzicht</h2>
            <p style="color: var(--slate-700); margin-bottom: 20px;">Bekijk en update student scores</p>

            <table class="student-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Studentnummer</th>
                        <th>Keuzedeel</th>
                        <th>Klas</th>
                        <th>Score</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($students)) {
                        foreach ($students as $student) {
                            echo "<tr>";
                            echo "<td><strong>#{$student->getId()}</strong></td>";
                            echo "<td>{$student->getStudentnummer()}</td>";
                            echo "<td>{$student->getOpleiding()}</td>";
                            echo "<td><span class='score-badge score-medium'>{$student->getKlas()}</span></td>";
                            echo "<td><span class='score-badge {$student->getScoreBadgeClass()}'>{$student->getFormattedScore()}</span></td>";
                            
                            // Only show update form for admin users
                            if (Auth::isAdmin()) {
                                $scoreValue = $student->getScore() !== null ? $student->getScore() : '';
                                echo "<td>
                                        <form method='POST' class='action-cell'>
                                            <input type='hidden' name='student_id' value='{$student->getId()}'>
                                            <input type='number' name='score' class='score-input' 
                                                   min='0' max='10' step='0.1' 
                                                   value='{$scoreValue}' placeholder='Score' required>
                                            <button type='submit' class='update-btn'>Update</button>
                                        </form>
                                      </td>";
                            } else {
                                echo "<td style='color: var(--slate-500); font-style: italic;'>Alleen admin</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding: 40px; color: var(--slate-500);'>
                                <strong>Geen studenten gevonden</strong>
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
