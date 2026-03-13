<?php
session_start();
require_once __DIR__ . '/../bootstrap.php';

// Handle score updates (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);
    $score = ($_POST['score'] === '' || $_POST['score'] === null) ? null : floatval($_POST['score']);
    
    if (ScoreManager::updateScore($student_id, $score)) {
        header("Location: keuzedeel.php" . (isset($_GET['klas']) ? "?klas=" . urlencode($_GET['klas']) : ""));
        exit;
    }
}

// Get selected class filter (admin only)
$selectedKlas = isset($_GET['klas']) ? $_GET['klas'] : 'all';

// Get students based on user role
$db = Database::getInstance();
$students = [];

if (Auth::isStudent()) {
    // Students can only see their own data
    if (isset($_SESSION['student_id'])) {
        // First get the studentnummer from the session ID
        $stmt = $db->prepare("SELECT studentnummer FROM student WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['student_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $studentnummer = $row['studentnummer'];
            // Now get all keuzedelen for this student
            $allStmt = $db->prepare("SELECT id, studentnummer, opleiding, klas, score FROM student WHERE studentnummer = ? ORDER BY opleiding");
            $allStmt->bind_param("s", $studentnummer);
            $allStmt->execute();
            $allResult = $allStmt->get_result();
            while ($keuzedeel = $allResult->fetch_assoc()) {
                $students[] = new Student($keuzedeel);
            }
        }
    }
} elseif (Auth::isAdmin()) {
    // Admins can see all students or filter by class
    if ($selectedKlas !== 'all') {
        $stmt = $db->prepare("SELECT id, studentnummer, opleiding, klas, score FROM student WHERE klas = ?");
        $stmt->bind_param("s", $selectedKlas);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $students[] = new Student($row);
        }
    } else {
        $students = Student::getAll();
    }
}

// Get all unique classes for the filter dropdown
$klassenResult = $db->query("SELECT DISTINCT klas FROM student ORDER BY klas");
$klassen = [];
while ($row = $klassenResult->fetch_assoc()) {
    $klassen[] = $row['klas'];
}
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
        .filter-container {
            background: rgba(212, 175, 55, .1);
            border: 2px solid rgba(212, 175, 55, .3);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .filter-label {
            font-weight: 700;
            color: var(--green-950);
            font-size: 15px;
        }
        .filter-select {
            padding: 10px 16px;
            border: 2px solid var(--slate-300);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            background: var(--white);
            color: var(--green-950);
            cursor: pointer;
            transition: all .2s;
            min-width: 150px;
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--gold-500);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, .1);
        }
        .filter-select:hover {
            border-color: var(--gold-500);
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
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-badge">
                📊 <?php echo Auth::isStudent() ? 'Mijn Voortgang' : 'Score Management'; ?>
            </div>
            <h1><?php echo Auth::isStudent() ? 'Mijn Keuzedeel Score' : 'Studenten Cijfers'; ?></h1>
            <p class="hero-subtitle"><?php echo Auth::isStudent() ? 'Bekijk je persoonlijke score en voortgang' : 'Beheer individuele student scores'; ?></p>
        </div>

        <!-- Students Table -->
        <div class="table-container">
            <h2 style="color: var(--green-950); font-size: 28px; font-weight: 700; margin-bottom: 8px;"><?php echo Auth::isStudent() ? 'Mijn Gegevens' : 'Student Overzicht'; ?></h2>
            <p style="color: var(--slate-700); margin-bottom: 20px;"><?php echo Auth::isStudent() ? 'Je persoonlijke score informatie' : 'Bekijk en update student scores'; ?></p>

            <!-- Class Filter (Admin Only) -->
            <?php if (Auth::isAdmin()): ?>
                <div class="filter-container">
                    <span class="filter-label">🎯 Filter op klas:</span>
                    <select class="filter-select" onchange="window.location.href='keuzedeel.php?klas=' + this.value">
                        <option value="all" <?php echo $selectedKlas === 'all' ? 'selected' : ''; ?>>Alle klassen</option>
                        <?php foreach ($klassen as $klas): ?>
                            <option value="<?php echo htmlspecialchars($klas); ?>" <?php echo $selectedKlas === $klas ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($klas); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($selectedKlas !== 'all'): ?>
                        <span style="color: var(--green-700); font-weight: 600;">
                            <?php echo count($students); ?> student(en) in deze klas
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <table class="student-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Studentnummer</th>
                        <th>Keuzedeel</th>
                        <th>Opleiding</th>
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
                            echo "<td><strong>{$student->getOpleidingName()}</strong></td>";
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
                                                   value='{$scoreValue}' placeholder='Score'>
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
