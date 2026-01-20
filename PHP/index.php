<?php
session_start();

if (isset($_GET['logout']) && $_GET['logout'] === '1') {
	$_SESSION = [];
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	session_destroy();
	header('Location: index.php');
	exit;
}

$role = $_SESSION['role'] ?? null; // expected values: 'admin' or 'student'
$isLoggedIn = isset($_SESSION['student_id']) || $role !== null;
$isAdmin = $role === 'admin';
$isStudent = ($role === 'student') || ($role === null && isset($_SESSION['student_id']));
?>
<!DOCTYPE html>
<html lang="nl">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="../CSS/style.css" />
	<title>Student Portaal - School</title>
</head>
<body>
	<!-- Header -->
	<header class="header">
		<div class="header-inner">
			<a class="brand" href="index.php">
				<span class="brand-logo">K</span>
				<span class="brand-text">
					<strong>Student Portaal</strong>
					<small>School voor Onderwijs & Educatie</small>
				</span>
			</a>

			<nav class="nav" aria-label="Hoofdnavigatie">
				<?php if ($isLoggedIn): ?>
					<a class="nav-icon" href="profile.php" title="Profiel">
						<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
							<path d="M12 12c2.76 0 5-2.24 5-5S14.76 2 12 2 7 4.24 7 7s2.24 5 5 5zm0 2c-3.34 0-10 1.67-10 5v3h20v-3c0-3.33-6.66-5-10-5z" />
						</svg>
					</a>
					<a href="index.php?logout=1">Uitloggen</a>
				<?php else: ?>
					<a href="login.php">Inloggen</a>
				<?php endif; ?>
			</nav>
		</div>
	</header>

	<!-- Main Content -->
	<div class="container">
		<?php if ($isAdmin): ?>
			<!-- Admin Dashboard -->
			<div class="hero">
				<div class="hero-badge">Admin Dashboard</div>
				<h1>Welkom terug, beheerder</h1>
				<p class="hero-subtitle">Beheer keuzedelen, bekijk statistieken en pas studentgegevens en cijfers aan vanuit één centrale omgeving.</p>
			</div>

			<div class="dashboard-grid">
				<div class="dashboard-card">
					<div class="status-badge">Actief beheerder</div>
					<h2>Beheeropties</h2>
					<p>Gebruik de onderstaande tools om het keuzedelenportaal te beheren en studentcijfers bij te werken.</p>
					
					<div class="hero-actions" style="justify-content: flex-start; margin-top: 24px;">
						<a class="btn btn-primary" href="keuzedeel.php">Keuzedelen & cijfers</a>
						<a class="btn btn-secondary" href="profile.php">Instellingen</a>
					</div>
				</div>

				<div class="sidebar-card">
					<h3>Snelle toegang</h3>
					<p>Direct naar de belangrijkste tools en functies.</p>
					<div class="info-list">
						<div class="info-item">
							<span class="info-label">Rol</span>
							<span class="info-value">Administrator</span>
						</div>
						<div class="info-item">
							<span class="info-label">Functie</span>
							<span class="info-value">Cijferbeheer</span>
						</div>
					</div>
				</div>
			</div>

		<?php elseif ($isStudent): ?>
			<!-- Student Dashboard -->
			<div class="hero">
				<div class="hero-badge">Student Portal</div>
				<h1>Welkom terug</h1>
				<p class="hero-subtitle">Bekijk je keuzedelen, volg je voortgang en beheer je account in één overzichtelijke omgeving.</p>
			</div>

			<div class="features">
				<div class="feature-card">
					<div class="feature-icon">📚</div>
					<h3>Mijn keuzedelen</h3>
					<p>Bekijk je ingeschreven keuzedelen, cijfers en voortgang voor dit studiejaar.</p>
					<a href="keuzedeel.php" class="feature-link">Keuzedelen bekijken</a>
				</div>
				
				<div class="feature-card">
					<div class="feature-icon">⚙️</div>
					<h3>Account instellingen</h3>
					<p>Pas je accountgegevens aan en wijzig je wachtwoord wanneer nodig.</p>
					<a href="profile.php" class="feature-link">Naar instellingen</a>
				</div>
			</div>

		<?php else: ?>
			<!-- Public Welcome Page -->
			<div class="hero">
				<div class="hero-badge">School voor Onderwijs & Educatie</div>
				<h1>Welkom bij het Keuzedeel Portal</h1>
				<p class="hero-subtitle">Het centrale platform voor studenten en docenten om keuzedelen te beheren, voortgang te volgen en resultaten in te zien. Log in om toegang te krijgen tot jouw persoonlijke dashboard.</p>
				
				<div class="hero-actions">
					<a href="login.php" class="btn btn-primary">Inloggen</a>
					<a href="#info" class="btn btn-secondary">Meer informatie</a>
				</div>
			</div>

			<div class="features" id="info">
				<div class="feature-card">
					<div class="feature-icon">🎓</div>
					<h3>Voor studenten</h3>
					<p>Bekijk je ingeschreven keuzedelen, volg je voortgang en check je cijfers en beoordelingen in een overzichtelijk dashboard.</p>
					<a href="login.php" class="feature-link">Student login</a>
				</div>
				
				<div class="feature-card">
					<div class="feature-icon">👨‍🏫</div>
					<h3>Voor docenten & beheerders</h3>
					<p>Beheer keuzedelen, pas cijfers aan en volg de voortgang van studenten in een centraal beheerpaneel.</p>
					<a href="login.php" class="feature-link">Admin login</a>
				</div>
				
				<div class="feature-card">
					<div class="feature-icon">📊</div>
					<h3>Overzichtelijk & veilig</h3>
					<p>Alle gegevens worden veilig opgeslagen. Eenvoudig inloggen met je studentnummer en persoonlijke wachtwoord.</p>
					<a href="login.php" class="feature-link">Aan de slag</a>
				</div>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>

