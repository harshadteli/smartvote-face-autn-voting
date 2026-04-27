<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>E-Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Online E-Voting System">
    <meta name="keywords" content="voting">
    <meta name="subject" content="E-Voting System">
    <meta name="copyright" content="Harshad Teli">
    <meta name="language" content="English">
    <meta name="robots" content="index,follow">
    <meta name="revised" content="saturday,April,25th,2026,8:45 pm">
    <meta name="abstract" content="HarshTech  specializes in custom Software Development and Web design solutions.">
    <meta name="topic" content="Software Development">
    <meta name="summary" content="HarshTech Provides innovative technology solutions for business">
    <meta name="classification" content="business">
    <meta name="author" content="Harshad Teli">
    <meta name="author" content="harshadteli697@gmail.com">
    <meta name="designer" content="Harshad Teli">
    <meta name="reply-to" content="harshtech417@gmail.com">
    <meta name="owner" content="Harshad Teli">
    <meta name="directory" content="Technology">
    <meta name="pagename" content="Online E-Voting System build with Best Security and Modern Technology.">
    <meta name="category" content="Web Development">
    <meta name="coverage" content="Worldwide">
    <meta name="distribution" content="Global">
    <link rel="shortcut icon" href="#" type="image/x-icon">
  <!-- Open Graph Tags (Whatsapp,Facebook,twitter,Instagram,Linkdin) -->
    <meta property="og:title" content="E-Voting System">
    <meta property="og:description" content="Online E-Voting System.">
    <meta property="og:image" content="#">
    <meta property="og:url" content="http://evoting.page.gd">
  <!-- For the twitter Card -->
   <meta name="twitter:Card" content="summary_large_image">
   <meta name="twitter:title" content="NCKBCS:Payment-info-testing">
   <meta name="twitter:description" content="This page contain the form of payment-info for New Horizon
 -2k26 event.This Project is developed for the Testing Purpose by the Developer.">
 <meta name="twitter:image" content="https://harshadteli.github.io/payment-info-testing
/banner.png">
<link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- i18n must load before DOM so translations apply on DOMContentLoaded -->
    <script src="assets/js/i18n.js"></script>
    <style>
        .lang-toggle-wrap {
            display: flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,0.08);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            padding: 3px 6px;
        }
        .lang-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 20px;
            transition: 0.25s;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.5px;
        }
        .lang-btn:hover { color: white; background: rgba(255,255,255,0.1); }
        .lang-btn.active { background: var(--accent-color); color: white; }
    </style>
</head>
<body>
    <header>
        <div class="container nav-container">
            <nav>
                <a href="index.php" class="logo">
                    <i class="fas fa-vote-yea"></i> E-VOTE
                </a>
                <div class="menu-toggle" id="mobile-menu">
                    <i class="fas fa-bars"></i>
                </div>
                <ul class="nav-links" id="nav-list">
                    <li><a href="index.php" data-i18n="nav_home">Home</a></li>
                    <li><a href="elections.php" data-i18n="nav_elections">Elections</a></li>
                    <li><a href="results.php" data-i18n="nav_results">Results</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li class="admin-item"><a href="admin/index.php" class="btn btn-admin"><i class="fas fa-shield-halved"></i> <span data-i18n="nav_admin">Admin Panel</span></a></li>
                        <?php endif; ?>
                        <li><a href="about_developer.php" data-i18n="nav_about">About Developer</a></li>
                        <li><a href="dashboard.php" class="btn btn-glass" data-i18n="nav_dashboard">Dashboard</a></li>
                        <li><a href="logout.php" class="logout-link" data-i18n="nav_logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="about_developer.php" data-i18n="nav_about">About Developer</a></li>
                        <li><a href="login.php" class="btn btn-glass" data-i18n="nav_login">Login</a></li>
                        <li><a href="register.php" class="btn btn-primary" data-i18n="nav_register">Register</a></li>
                    <?php endif; ?>
                    <!-- Language Toggle -->
                    <li class="lang-toggle-wrap">
                        <button class="lang-btn active" data-lang="en" title="English">EN</button>
                        <button class="lang-btn" data-lang="hi" title="Hindi">हि</button>
                        <button class="lang-btn" data-lang="mr" title="Marathi">म</button>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
