<?php
session_start();
require_once 'classes.php';

// Require authentication
Auth::requireAuth();

$isAdmin = Auth::isAdmin();
$isStudent = Auth::isStudent();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $error = 'Vul alle velden in.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Nieuwe wachtwoorden komen niet overeen.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Kies een wachtwoord van minimaal 6 tekens.';
    } else {
        try {
            $db = Database::getInstance();
            
            if ($isAdmin) {
                // Admin password change
                $adminId = $_SESSION['admin_id'];
                $stmt = $db->prepare('SELECT password_hash FROM users WHERE id = ? AND role = "admin"');
                $stmt->bind_param('i', $adminId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows !== 1) {
                    $error = 'Account niet gevonden.';
                } else {
                    $row = $result->fetch_assoc();
                    if (!password_verify($currentPassword, $row['password_hash'])) {
                        $error = 'Huidig wachtwoord is onjuist.';
                    } else {
                        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                        $update = $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
                        $update->bind_param('si', $newHash, $adminId);
                        if ($update->execute()) {
                            $message = 'Wachtwoord succesvol gewijzigd.';
                        } else {
                            $error = 'Kon wachtwoord niet wijzigen.';
                        }
                    }
                }
            } elseif ($isStudent) {
                // Student password change
                $studentId = $_SESSION['student_id'];
                $stmt = $db->prepare('SELECT password_hash FROM student WHERE id = ?');
                $stmt->bind_param('i', $studentId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows !== 1) {
                    $error = 'Account niet gevonden.';
                } else {
                    $row = $result->fetch_assoc();
                    if (!password_verify($currentPassword, $row['password_hash'])) {
                        $error = 'Huidig wachtwoord is onjuist.';
                    } else {
                        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                        $update = $db->prepare('UPDATE student SET password_hash = ? WHERE id = ?');
                        $update->bind_param('si', $newHash, $studentId);
                        if ($update->execute()) {
                            $message = 'Wachtwoord succesvol gewijzigd.';
                        } else {
                            $error = 'Kon wachtwoord niet wijzigen.';
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $error = 'Er is een fout opgetreden: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profiel</title>
    <link rel="stylesheet" href="../CSS/style.css" />
    <style>
        .wrap { max-width: 720px; margin: 0 auto; padding: 28px 18px 56px; }
        .card { background: rgba(255, 255, 255, .95); border: 1px solid rgba(255, 255, 255, .6); border-radius: 18px; padding: 22px; box-shadow: 0 18px 55px rgba(0,0,0,.35); }
        .row { margin-top: 12px; }
        label { display:block; font-weight: 700; margin-bottom: 6px; color: #0b2d22; }
        input { width: 100%; padding: 10px 12px; border-radius: 12px; border: 1px solid var(--slate-200); }
        .toplinks { display:flex; justify-content: space-between; gap: 10px; margin-bottom: 14px; }
        .toplinks a { color: rgba(255,255,255,.92); text-decoration: none; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.06); }
        .toplinks a:hover { border-color: rgba(212,175,55,.45); }
        .alert { margin-top: 12px; padding: 12px 14px; border-radius: 14px; border: 1px solid var(--slate-200); background: var(--slate-50); }
        .alert.ok { border-color: rgba(20, 90, 60, .35); }
        .alert.err { border-color: rgba(185, 28, 28, .35); }
        .actions { margin-top: 14px; display:flex; gap: 10px; flex-wrap: wrap; }
        .btn { appearance:none; border:1px solid var(--slate-200); background: var(--white); color:#0b2d22; padding: 10px 14px; border-radius: 14px; font-weight: 650; cursor: pointer; }
        .btn-primary { border-color: rgba(212,175,55,.55); background: linear-gradient(135deg, rgba(212,175,55,.95), rgba(212,175,55,.25)); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="toplinks">
            <a href="index.php">← Terug</a>
            <a href="index.php?logout=1">Logout</a>
        </div>

        <div class="card">
            <h1>Profiel</h1>
            <p>Hier kun je je wachtwoord wijzigen. Werkt voor zowel studenten als admin accounts.</p>

            <?php if ($message): ?>
                <div class="alert ok"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert err"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <h2 style="margin-top:18px;">Wachtwoord wijzigen</h2>
            <form method="post">
                <input type="hidden" name="action" value="change_password" />

                <div class="row">
                    <label for="current_password">Huidig wachtwoord</label>
                    <input id="current_password" name="current_password" type="password" required />
                </div>

                <div class="row">
                    <label for="new_password">Nieuw wachtwoord</label>
                    <input id="new_password" name="new_password" type="password" required />
                </div>

                <div class="row">
                    <label for="confirm_password">Nieuw wachtwoord (herhaal)</label>
                    <input id="confirm_password" name="confirm_password" type="password" required />
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
