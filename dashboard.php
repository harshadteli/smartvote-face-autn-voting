<?php
include 'includes/config.php';
if (!isset($_SESSION['user_id'])) redirect('login.php');

include 'includes/header.php';

$user_id = $_SESSION['user_id'];

// User vote count
$stmt = $conn->prepare("SELECT COUNT(*) FROM votes WHERE user_id = ?");
$stmt->execute([$user_id]);
$votes_cast = $stmt->fetchColumn();

// Active elections
$stmt = $conn->prepare("SELECT * FROM elections WHERE status = 'active' ORDER BY end_date ASC");
$stmt->execute();
$active_elections = $stmt->fetchAll();

// Already voted IDs
$stmt = $conn->prepare("SELECT election_id FROM votes WHERE user_id = ?");
$stmt->execute([$user_id]);
$voted_election_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ── Analytics Data ────────────────────────────────────────────────────────
// 1. Gender-wise turnout (requires gender column; graceful fallback)
$gender_data = [];
try {
    $g = $conn->query("SELECT gender, COUNT(*) as cnt FROM users
                       JOIN votes ON users.id = votes.user_id
                       WHERE users.role='voter'
                       GROUP BY gender");
    $gender_data = $g->fetchAll();
} catch (PDOException $e) { /* column may not exist */ }

// 2. Votes per active election (bar chart)
$election_labels = [];
$election_counts  = [];
foreach ($active_elections as $el) {
    $s = $conn->prepare("SELECT COUNT(*) FROM votes WHERE election_id = ?");
    $s->execute([$el['id']]);
    $election_labels[] = $el['title'];
    $election_counts[]  = (int)$s->fetchColumn();
}

// 3. Voter participation: voted vs not voted (total registered voters)
$total_voters = (int)$conn->query("SELECT COUNT(*) FROM users WHERE role='voter'")->fetchColumn();
$total_votes_cast = (int)$conn->query("SELECT COUNT(DISTINCT user_id) FROM votes")->fetchColumn();
$not_voted = max(0, $total_voters - $total_votes_cast);

// Success / already-voted toast message
$toast = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'vote_success')   $toast = 'success';
    if ($_GET['msg'] === 'already_voted')  $toast = 'already';
}
?>

<!-- Toast Notification -->
<?php if ($toast): ?>
<div id="toast-msg" style="position:fixed; top:80px; right:20px; z-index:9999; padding:1rem 1.5rem; border-radius:14px; font-weight:600; animation:slideInRight 0.4s ease;
     background:<?php echo $toast==='success' ? 'rgba(85,239,196,0.2)' : 'rgba(255,118,117,0.2)'; ?>;
     border:1px solid <?php echo $toast==='success' ? '#55efc4' : '#ff7675'; ?>; backdrop-filter:blur(10px);">
    <?php echo $toast === 'success'
        ? '🗳️ Your vote has been cast successfully!'
        : '⚠️ You have already voted in this election.'; ?>
    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:white;margin-left:1rem;cursor:pointer;font-size:1rem;">×</button>
</div>
<style>@keyframes slideInRight{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}</style>
<script>setTimeout(()=>{ const t=document.getElementById('toast-msg'); if(t) t.style.animation='slideInRight 0.4s ease reverse'; setTimeout(()=>t&&t.remove(),400); }, 5000);</script>
<?php endif; ?>

<div class="dashboard-header" style="padding:2rem 0;">
    <div class="glass-card" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div>
            <h2 style="font-size:1.8rem;">
                <span data-i18n="dash_welcome">Welcome</span>,
                <span style="color:var(--accent-color);"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>!
            </h2>
            <p style="opacity:0.8;"><span data-i18n="dash_voter_id">Voter ID</span>: #<?php echo str_pad($user_id, 5, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div style="display:flex; gap:2rem; flex-wrap:wrap;">
            <div style="text-align:center;">
                <h3 style="font-size:1.5rem; color:var(--accent-color);"><?php echo $votes_cast; ?></h3>
                <p style="font-size:0.9rem; opacity:0.7;" data-i18n="dash_votes_cast">Votes Cast</p>
            </div>
            <div style="text-align:center;">
                <h3 style="font-size:1.5rem; color:var(--accent-color);"><?php echo count($active_elections); ?></h3>
                <p style="font-size:0.9rem; opacity:0.7;" data-i18n="dash_active_elections">Active Elections</p>
            </div>
            <div style="text-align:center;">
                <h3 style="font-size:1.5rem; color:#55efc4;"><?php echo $total_voters; ?></h3>
                <p style="font-size:0.9rem; opacity:0.7;">Total Voters</p>
            </div>
        </div>
    </div>
</div>

<!-- ── ANALYTICS ROW ─────────────────────────────────────────────────────── -->
<section style="padding:1rem 0 2rem;">
    <h3 style="margin-bottom:1.5rem; font-size:1.4rem; border-left:4px solid var(--accent-color); padding-left:1rem;" data-i18n="dash_voting_trends">
        Your Voting Trends
    </h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:2rem;">

        <!-- Doughnut: Participation -->
        <div class="glass-card" style="text-align:center;">
            <h4 style="margin-bottom:1rem; font-size:1rem; opacity:0.85;">🗳️ Voter Participation</h4>
            <canvas id="participationChart" height="220"></canvas>
            <p style="font-size:0.8rem; opacity:0.55; margin-top:0.8rem;">
                <?php echo $total_votes_cast; ?> voted · <?php echo $not_voted; ?> pending
            </p>
        </div>

        <!-- Bar: Votes per Election -->
        <div class="glass-card">
            <h4 style="margin-bottom:1rem; font-size:1rem; opacity:0.85;">📊 Votes per Active Election</h4>
            <canvas id="electionBarChart" height="220"></canvas>
        </div>

        <?php if (!empty($gender_data)): ?>
        <!-- Pie: Gender-wise turnout -->
        <div class="glass-card" style="text-align:center;">
            <h4 style="margin-bottom:1rem; font-size:1rem; opacity:0.85;">👥 Gender-wise Turnout</h4>
            <canvas id="genderChart" height="220"></canvas>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ── ACTIVE ELECTIONS ──────────────────────────────────────────────────── -->
<section class="elections-section" style="padding:2rem 0;">
    <h3 style="margin-bottom:1.5rem; font-size:1.5rem; border-left:4px solid var(--accent-color); padding-left:1rem;" data-i18n="dash_active_title">Active Elections</h3>

    <?php if (empty($active_elections)): ?>
        <div class="glass-card" style="text-align:center; padding:3rem;">
            <i class="fas fa-calendar-times" style="font-size:3rem; opacity:0.3; margin-bottom:1rem;"></i>
            <p style="font-size:1.2rem; opacity:0.6;" data-i18n="dash_no_elections">No active elections at the moment.</p>
        </div>
    <?php else: ?>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:2rem;">
            <?php foreach ($active_elections as $election): ?>
                <div class="glass-card election-card" style="display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                            <h4 style="font-size:1.3rem; color:var(--accent-color);"><?php echo htmlspecialchars($election['title']); ?></h4>
                            <span style="background:rgba(85,239,196,0.2); color:#55efc4; padding:4px 10px; border-radius:20px; font-size:0.8rem; border:1px solid #55efc4;">Active</span>
                        </div>
                        <p style="font-size:0.9rem; opacity:0.8; margin-bottom:1.5rem;"><?php echo htmlspecialchars($election['description']); ?></p>
                        <div style="font-size:0.85rem; opacity:0.7; margin-bottom:1rem;">
                            <i class="fas fa-clock"></i> <span data-i18n="dash_ends">Ends</span>: <?php echo date('M d, Y', strtotime($election['end_date'])); ?>
                        </div>
                    </div>
                    <?php if (in_array($election['id'], $voted_election_ids)): ?>
                        <button class="btn" style="background:rgba(255,255,255,0.1); color:#55efc4; width:100%; cursor:default;" disabled>
                            <i class="fas fa-check-circle"></i> <span data-i18n="dash_already_voted">Already Voted</span>
                        </button>
                    <?php else: ?>
                        <a href="vote.php?id=<?php echo $election['id']; ?>" class="btn btn-primary" style="text-align:center; width:100%;">
                            <span data-i18n="dash_vote_now">Vote Now</span> <i class="fas fa-arrow-right" style="margin-left:5px;"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- ── QUICK ACTIONS ─────────────────────────────────────────────────────── -->
<section class="profile-summary" style="padding:2rem 0;">
    <div class="glass-card">
        <h3 style="margin-bottom:1.5rem;" data-i18n="dash_quick_actions">Quick Actions</h3>
        <div style="display:flex; gap:1rem; flex-wrap:wrap;">
            <a href="profile.php" class="btn btn-glass"><i class="fas fa-user-edit"></i> <span data-i18n="dash_edit_profile">Edit Profile</span></a>
            <a href="results.php" class="btn btn-glass"><i class="fas fa-poll"></i> <span data-i18n="dash_view_results">View Results</span></a>
            <a href="logout.php" class="btn btn-glass" style="color:#ff7675; border-color:rgba(255,118,117,0.3);"><i class="fas fa-sign-out-alt"></i> <span data-i18n="nav_logout">Logout</span></a>
        </div>
    </div>
</section>

<script>
// ── Chart.js — Shared style defaults ────────────────────────────────────────
Chart.defaults.color = 'rgba(255,255,255,0.75)';
Chart.defaults.borderColor = 'rgba(255,255,255,0.08)';
const PALETTE = ['#00cec9','#6c5ce7','#fdcb6e','#e17055','#55efc4','#74b9ff','#fd79a8'];

// 1. Doughnut — Participation
new Chart(document.getElementById('participationChart'), {
    type: 'doughnut',
    data: {
        labels: ['Voted', 'Not Yet Voted'],
        datasets: [{
            data: [<?php echo $total_votes_cast; ?>, <?php echo $not_voted; ?>],
            backgroundColor: ['rgba(0,206,201,0.8)', 'rgba(255,255,255,0.12)'],
            borderColor:     ['#00cec9','rgba(255,255,255,0.2)'],
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } }
        },
        animation: { animateRotate: true, duration: 1200 }
    }
});

// 2. Bar — Votes per election
new Chart(document.getElementById('electionBarChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($election_labels ?: ['No elections']); ?>,
        datasets: [{
            label: 'Votes Cast',
            data: <?php echo json_encode($election_counts ?: [0]); ?>,
            backgroundColor: PALETTE.map(c => c + 'cc'),
            borderColor: PALETTE,
            borderWidth: 2,
            borderRadius: 8,
            hoverBackgroundColor: PALETTE
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
                grid: { color: 'rgba(255,255,255,0.06)' }
            },
            x: { grid: { display: false } }
        },
        animation: { duration: 1000 }
    }
});

<?php if (!empty($gender_data)): ?>
// 3. Pie — Gender turnout
new Chart(document.getElementById('genderChart'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($gender_data, 'gender')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($gender_data, 'cnt')); ?>,
            backgroundColor: PALETTE.map(c => c + 'cc'),
            borderColor: PALETTE,
            borderWidth: 2,
            hoverOffset: 8
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } } },
        animation: { animateRotate: true, duration: 1200 }
    }
});
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
