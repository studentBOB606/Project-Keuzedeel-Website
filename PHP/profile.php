<?php
session_start();

$role = $_SESSION['role'] ?? null;
$isLoggedIn = isset($_SESSION['student_id']) || $role !== null;
$isAdmin = $role === 'admin';

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword === '' || $confirmPassword === '' || $currentPassword === '') {
        $error = 'Vul alle velden in.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Nieuwe wachtwoorden komen niet overeen.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Kies een wachtwoord van minimaal 6 tekens.';
    } else {
        if (!isset($_SESSION['student_id'])) {
            // Admin password change is not possible until an admin table/login exists.
            $error = 'Admin wachtwoord wijzigen is nog niet ingesteld (er is nog geen admin-account tabel/login).';
        } else {
            $conn = new mysqli('localhost', 'root', '', 'studenten');
            if ($conn->connect_error) {
                $error = 'Database verbinding mislukt.';
            } else {
                $studentId = (int)$_SESSION['student_id'];

                $stmt = $conn->prepare('SELECT password_hash FROM student WHERE id = ?');
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
                        $update = $conn->prepare('UPDATE student SET password_hash = ? WHERE id = ?');
                        $update->bind_param('si', $newHash, $studentId);
                        if ($update->execute()) {
                            $message = 'Wachtwoord gewijzigd.';
                        } else {
                            $error = 'Kon wachtwoord niet wijzigen.';
                        }
                    }
                }

                $conn->close();
            }
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
            <p>Hier kun je je gegevens aanpassen. Voor nu is wachtwoord wijzigen beschikbaar voor student-accounts.</p>

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

            <?php if ($isAdmin): ?>
                <div class="alert" style="margin-top:14px;">
                    Admin tip: om dit ook voor admins te laten werken, heb je een admin tabel + login nodig (met een password_hash kolom), dan kan ik dezelfde flow voor admin toevoegen.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
