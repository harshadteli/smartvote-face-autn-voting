<?php
include '../includes/config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - E-Voting System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js for Admin Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        body {
            overflow-x: hidden; /* Prevent global horizontal scrolling */
            width: 100%;
            position: relative;
        }
        .admin-layout {
            display: flex;
            min-height: calc(100vh - 70px);
            width: 100%;
            max-width: 100vw;
        }
        .sidebar {
            width: 260px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border-right: 1px solid var(--glass-border);
            padding: 2rem 1rem;
            flex-shrink: 0;
        }
        .admin-main {
            flex: 1;
            padding: 2rem;
            width: calc(100% - 260px);
            max-width: 100%;
            overflow-x: hidden; /* Prevent tables from pushing width out */
        }
        .side-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
            margin-bottom: 0.5rem;
            white-space: nowrap;
        }
        .side-link:hover, .side-link.active {
            background: var(--glass-bg);
            color: var(--accent-color);
        }
        
        /* Mobile & Tablet Responsiveness */
        @media (max-width: 850px) {
            .admin-layout {
                flex-direction: column;
                width: 100%;
                max-width: 100vw;
                overflow-x: hidden;
            }
            .admin-menu-toggle {
                display: block !important;
            }
            .sidebar {
                display: none; /* Hidden by default on mobile */
                width: 100%;
                max-width: 100vw;
                box-sizing: border-box;
                padding: 1rem;
                border-right: none;
                border-bottom: 1px solid var(--glass-border);
                flex-direction: column;
                gap: 5px;
                background: rgba(45, 52, 54, 0.98);
                position: absolute;
                top: 70px;
                left: 0;
                z-index: 1000;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            }
            .sidebar.active {
                display: flex; /* Shown when toggled */
            }
            .side-link {
                margin-bottom: 0;
                padding: 1rem;
                font-size: 1rem;
                white-space: normal;
                background: rgba(255,255,255,0.05);
            }
            .admin-main {
                width: 100%;
                max-width: 100vw;
                box-sizing: border-box;
                padding: 1.2rem 1rem;
                overflow-x: hidden;
            }
            
            /* Keep top nav visible and neat on mobile */
            .nav-container nav {
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 0.8rem;
            }
            .nav-links {
                display: flex !important;
                position: static !important;
                flex-direction: row !important;
                background: transparent !important;
                padding: 0 !important;
                gap: 1rem !important;
                border: none !important;
                width: auto !important;
            }
            .nav-links a { font-size: 0.85rem; }
            .nav-links .btn { padding: 0.4rem 1rem; }
        }
        .admin-menu-toggle {
            display: none;
            background: transparent;
            border: none;
            color: var(--accent-color);
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <div class="container nav-container">
            <nav>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="admin-menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="index.php" class="logo">
                        <i class="fas fa-user-shield"></i> E-VOTE ADMIN
                    </a>
                </div>
                <ul class="nav-links">
                    <li><a href="../index.php">View Site</a></li>
                    <li><a href="../logout.php" class="btn btn-glass">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="admin-layout">
        <aside class="sidebar">
            <a href="index.php" class="side-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="elections.php" class="side-link"><i class="fas fa-vote-yea"></i> Manage Elections</a>
            <a href="candidates.php" class="side-link"><i class="fas fa-users"></i> Manage Candidates</a>
            <a href="voters.php" class="side-link"><i class="fas fa-user-friends"></i> Manage Voters</a>
            <a href="results.php" class="side-link"><i class="fas fa-chart-bar"></i> Election Results</a>
        </aside>
        <main class="admin-main">
            <div class="container">
