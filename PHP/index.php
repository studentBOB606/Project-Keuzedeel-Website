<?php
session_start();
require_once '../bootstrap.php';

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
	Auth::logout();
	header('Location: index.php');
	exit;
}

// Get current user status
$isLoggedIn = Auth::isLoggedIn();
$isAdmin = Auth::isAdmin();
$isStudent = Auth::isStudent();

// Get student data if logged in as student
$studentData = null;
$studentKeuzedelen = [];
if ($isStudent && isset($_SESSION['student_id'])) {
	$db = Database::getInstance();
	// First get the student's basic info
	$stmt = $db->prepare("SELECT studentnummer, naam, klas FROM student WHERE id = ?");
	$stmt->bind_param("i", $_SESSION['student_id']);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows === 1) {
		$basicInfo = $result->fetch_assoc();
		$studentData = $basicInfo;
		
		// Get all keuzedelen for this student
		$keuzedelenStmt = $db->prepare("SELECT * FROM student WHERE studentnummer = ? ORDER BY opleiding");
		$keuzedelenStmt->bind_param("s", $basicInfo['studentnummer']);
		$keuzedelenStmt->execute();
		$keuzedelenResult = $keuzedelenStmt->get_result();
		while ($row = $keuzedelenResult->fetch_assoc()) {
			$studentKeuzedelen[] = $row;
		}
	}
}
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
				<div class="hero-badge">Student Portaal</div>
				<h1>Welkom terug<?php if ($studentData): ?>, <?php echo htmlspecialchars($studentData['naam']); ?><?php endif; ?>!</h1>
				<p class="hero-subtitle">Bekijk je keuzedelen, volg je voortgang en beheer je account in één overzichtelijke omgeving.</p>
			</div>

			<?php if ($studentData): ?>
			<!-- Student Info Dashboard -->
			<div style="max-width: 1200px; margin: 0 auto;">
				<!-- Student Info Row -->
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px;">
					<div style="padding: 28px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 20px; border: 2px solid #6ee7b7; box-shadow: 0 8px 24px rgba(0, 0, 0, .12); transition: transform .3s, box-shadow .3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 32px rgba(0, 0, 0, .18)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px rgba(0, 0, 0, .12)';">
						<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
							<div style="width: 48px; height: 48px; background: linear-gradient(135deg, #059669, #10b981); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 12px rgba(5, 150, 105, .4);">👤</div>
							<div>
								<div style="color: var(--slate-600); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Studentnummer</div>
								<div style="color: var(--green-950); font-size: 24px; font-weight: 800; line-height: 1.2;"><?php echo htmlspecialchars($studentData['studentnummer']); ?></div>
							</div>
						</div>
					</div>
					
					<div style="padding: 28px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 20px; border: 2px solid #fbbf24; box-shadow: 0 8px 24px rgba(0, 0, 0, .12); transition: transform .3s, box-shadow .3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 32px rgba(0, 0, 0, .18)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px rgba(0, 0, 0, .12)';">
						<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
							<div style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b, #fbbf24); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 12px rgba(245, 158, 11, .4);">🎓</div>
							<div>
								<div style="color: var(--slate-600); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Klas</div>
								<div style="color: var(--green-950); font-size: 24px; font-weight: 800; line-height: 1.2;"><?php echo htmlspecialchars($studentData['klas']); ?></div>
							</div>
						</div>
					</div>
					
					<div style="padding: 28px; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 20px; border: 2px solid #60a5fa; box-shadow: 0 8px 24px rgba(0, 0, 0, .12); transition: transform .3s, box-shadow .3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 32px rgba(0, 0, 0, .18)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 24px rgba(0, 0, 0, .12)';">
						<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
							<div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3b82f6, #60a5fa); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 4px 12px rgba(59, 130, 246, .4);">📚</div>
							<div>
								<div style="color: var(--slate-600); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;">Keuzedelen</div>
								<div style="color: var(--green-950); font-size: 24px; font-weight: 800; line-height: 1.2;"><?php echo count($studentKeuzedelen); ?></div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Keuzedelen Cards -->
				<div class="dashboard-card" style="padding: 40px;">
					<div style="display: flex; align-items: center; gap: 16px; margin-bottom: 32px;">
						<div style="width: 56px; height: 56px; background: linear-gradient(135deg, var(--gold-500), var(--gold-400)); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 8px 24px rgba(212, 175, 55, .25);">📖</div>
						<div>
							<h2 style="margin: 0; font-size: 32px; font-weight: 800; color: var(--green-950);">Mijn Keuzedelen</h2>
							<p style="margin: 4px 0 0; color: var(--slate-600); font-size: 15px;">Bekijk je ingeschreven vakken en hun voortgang</p>
						</div>
					</div>
					
					<div style="display: grid; gap: 20px;">
						<?php foreach ($studentKeuzedelen as $keuzedeel): ?>
							<div style="padding: 24px; background: linear-gradient(135deg, rgba(255, 255, 255, .95) 0%, rgba(248, 250, 252, .95) 100%); border-radius: 16px; border: 2px solid rgba(203, 213, 225, .4); box-shadow: 0 4px 16px rgba(0, 0, 0, .08); display: flex; justify-content: space-between; align-items: center; transition: all .3s;" onmouseover="this.style.transform='translateX(8px)'; this.style.boxShadow='0 8px 24px rgba(0, 0, 0, .15)'; this.style.borderColor='rgba(212, 175, 55, .5)';" onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 4px 16px rgba(0, 0, 0, .08)'; this.style.borderColor='rgba(203, 213, 225, .4)';">
								<div style="flex: 1;">
									<div style="color: var(--green-950); font-size: 20px; font-weight: 800; margin-bottom: 6px;"><?php echo htmlspecialchars(OpleidingHelper::getName($keuzedeel['opleiding'])); ?></div>
									<div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; background: rgba(100, 116, 139, .08); border-radius: 8px;">
										<span style="color: var(--slate-600); font-size: 13px; font-weight: 600;"><?php echo htmlspecialchars($keuzedeel['opleiding']); ?></span>
									</div>
								</div>
								<div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
									<div style="padding: 12px 20px; border-radius: 12px; font-weight: 800; font-size: 24px; min-width: 100px; text-align: center; <?php 
										if ($keuzedeel['score'] === null) echo 'background: linear-gradient(135deg, rgba(100, 116, 139, .15), rgba(100, 116, 139, .08)); color: var(--slate-600); border: 2px solid rgba(100, 116, 139, .2);';
										elseif ($keuzedeel['score'] >= 5.5) echo 'background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; border: 2px solid #059669; box-shadow: 0 4px 12px rgba(5, 150, 105, .2);';
										else echo 'background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; border: 2px solid #dc2626; box-shadow: 0 4px 12px rgba(220, 38, 38, .2);';
									?>">
										<?php 
											if ($keuzedeel['score'] === null) {
												echo '<span style="font-size: 14px; font-weight: 600;">Niet beoordeeld</span>';
											} else {
												echo number_format($keuzedeel['score'], 1);
											}
										?>
									</div>
									<?php if ($keuzedeel['score'] !== null): ?>
										<div style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; <?php 
											if ($keuzedeel['score'] >= 5.5) echo 'background: #059669; color: white;';
											else echo 'background: #dc2626; color: white;';
										?>">
											<?php echo $keuzedeel['score'] >= 5.5 ? '✓ VOLDOENDE' : '✗ ONVOLDOENDE'; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					
					<div class="hero-actions" style="justify-content: center; margin-top: 32px; gap: 16px;">
						<a class="btn btn-primary" href="keuzedeel.php" style="padding: 16px 32px; font-size: 16px;">📚 Bekijk alle details</a>
						<a class="btn btn-secondary" href="profile.php" style="padding: 16px 32px; font-size: 16px;">⚙️ Account Instellingen</a>
					</div>
				</div>
			</div>

			<!-- Quick Links -->
			<div class="features" style="margin-top: 32px;">
				<div class="feature-card">
					<div class="feature-icon">📚</div>
					<h3>Keuzedelen Overzicht</h3>
					<p>Bekijk alle beschikbare keuzedelen en je voortgang voor dit studiejaar.</p>
					<a href="keuzedeel.php" class="feature-link">Bekijken</a>
				</div>
				
				<div class="feature-card">
					<div class="feature-icon">⚙️</div>
					<h3>Account Beheer</h3>
					<p>Pas je wachtwoord aan en beheer je persoonlijke gegevens.</p>
					<a href="profile.php" class="feature-link">Instellingen</a>
				</div>
			</div>
			<?php else: ?>
			<!-- Fallback if no student data -->
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
			<?php endif; ?>

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

