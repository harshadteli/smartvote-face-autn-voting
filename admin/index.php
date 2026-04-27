<?php
include 'includes/header.php';

// Fetch stats
$total_voters    = $conn->query("SELECT COUNT(*) FROM users WHERE role='voter'")->fetchColumn();
$active_elections= $conn->query("SELECT COUNT(*) FROM elections WHERE status='active'")->fetchColumn();
$total_votes     = $conn->query("SELECT COUNT(*) FROM votes")->fetchColumn();
$pending_voters  = $conn->query("SELECT COUNT(*) FROM users WHERE status='pending'")->fetchColumn();
$total_elections = $conn->query("SELECT COUNT(*) FROM elections")->fetchColumn();

// Recent activity (recent votes)
$stmt = $conn->prepare("
    SELECT v.*, u.full_name as voter_name, e.title as election_title
    FROM votes v
    JOIN users u ON v.user_id = u.id
    JOIN elections e ON v.election_id = e.id
    ORDER BY v.voted_at DESC LIMIT 8
");
$stmt->execute();
$recent_votes = $stmt->fetchAll();

// ── Chart Data ────────────────────────────────────────────────────────────

// Votes per election (all elections with ≥1 candidate)
$stmt = $conn->prepare("
    SELECT e.title, COUNT(v.id) as cnt
    FROM elections e
    LEFT JOIN votes v ON e.id = v.election_id
    GROUP BY e.id ORDER BY cnt DESC LIMIT 8
");
$stmt->execute();
$vote_per_election = $stmt->fetchAll();

// Voter registration trend — registrations per day (last 14 days)
$stmt = $conn->query("
    SELECT DATE(created_at) as day, COUNT(*) as cnt
    FROM users WHERE role='voter'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
    GROUP BY day ORDER BY day ASC
");
$reg_trend = $stmt->fetchAll();

// Status breakdown of voters
$stmt = $conn->query("SELECT status, COUNT(*) as cnt FROM users WHERE role='voter' GROUP BY status");
$voter_status = $stmt->fetchAll();
?>

<h2 style="margin-bottom:2rem;">Admin <span style="color:var(--accent-color);">Dashboard</span></h2>

<!-- ── Stat Cards ─────────────────────────────────────────────────────────── -->
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1.5rem; margin-bottom:2.5rem;">
    <div class="glass-card stat-card" style="text-align:center;">
        <i class="fas fa-users" style="font-size:2rem; color:var(--accent-color); margin-bottom:1rem;"></i>
        <h3 style="font-size:1.8rem;"><?php echo $total_voters; ?></h3>
        <p style="opacity:0.7;">Total Voters</p>
    </div>
    <div class="glass-card stat-card" style="text-align:center;">
        <i class="fas fa-vote-yea" style="font-size:2rem; color:#55efc4; margin-bottom:1rem;"></i>
        <h3 style="font-size:1.8rem;"><?php echo $active_elections; ?></h3>
        <p style="opacity:0.7;">Active Elections</p>
    </div>
    <div class="glass-card stat-card" style="text-align:center;">
        <i class="fas fa-poll" style="font-size:2rem; color:var(--primary-color); margin-bottom:1rem;"></i>
        <h3 style="font-size:1.8rem;"><?php echo $total_votes; ?></h3>
        <p style="opacity:0.7;">Total Votes Cast</p>
    </div>
    <div class="glass-card stat-card" style="text-align:center;">
        <i class="fas fa-user-clock" style="font-size:2rem; color:#fab1a0; margin-bottom:1rem;"></i>
        <h3 style="font-size:1.8rem;"><?php echo $pending_voters; ?></h3>
        <p style="opacity:0.7;">Pending Approval</p>
    </div>
    <div class="glass-card stat-card" style="text-align:center;">
        <i class="fas fa-calendar-alt" style="font-size:2rem; color:#a29bfe; margin-bottom:1rem;"></i>
        <h3 style="font-size:1.8rem;"><?php echo $total_elections; ?></h3>
        <p style="opacity:0.7;">Total Elections</p>
    </div>
</div>

<!-- ── Live Analytics Charts ─────────────────────────────────────────────── -->
<h3 style="margin-bottom:1.5rem; font-size:1.3rem; border-left:4px solid var(--accent-color); padding-left:1rem;">
    📊 Live Election Analytics
</h3>
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(340px,1fr)); gap:2rem; margin-bottom:2.5rem;">

    <!-- Horizontal Bar — Votes per Election -->
    <div class="glass-card">
        <h4 style="margin-bottom:1rem; opacity:0.85; font-size:0.95rem;">🏆 Votes per Election</h4>
        <canvas id="votePerElectionChart" height="200"></canvas>
    </div>

    <!-- Doughnut — Voter Participation -->
    <div class="glass-card" style="text-align:center;">
        <h4 style="margin-bottom:1rem; opacity:0.85; font-size:0.95rem;">🗳️ Overall Participation</h4>
        <canvas id="participationChart" height="200"></canvas>
        <?php
        $not_voted = max(0, $total_voters - (int)$conn->query("SELECT COUNT(DISTINCT user_id) FROM votes")->fetchColumn());
        $did_vote  = $total_voters - $not_voted;
        ?>
        <p style="font-size:0.8rem; opacity:0.55; margin-top:0.6rem;"><?php echo $did_vote; ?> voted · <?php echo $not_voted; ?> not yet voted</p>
    </div>

    <!-- Doughnut — Voter Status breakdown -->
    <div class="glass-card" style="text-align:center;">
        <h4 style="margin-bottom:1rem; opacity:0.85; font-size:0.95rem;">👥 Voter Status Breakdown</h4>
        <canvas id="voterStatusChart" height="200"></canvas>
    </div>

    <!-- Line — Daily registrations -->
    <div class="glass-card">
        <h4 style="margin-bottom:1rem; opacity:0.85; font-size:0.95rem;">📅 New Registrations (14 days)</h4>
        <canvas id="regTrendChart" height="200"></canvas>
    </div>

</div>

<!-- ── Recent Activity + Quick Actions ───────────────────────────────────── -->
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(400px,1fr)); gap:2rem;">
    <div class="glass-card">
        <h3 style="margin-bottom:1.5rem; display:flex; align-items:center; gap:10px;">
            <i class="fas fa-history" style="color:var(--accent-color);"></i> Recent Voting Activity
        </h3>
        <table style="width:100%; border-collapse:collapse; font-size:0.88rem;">
            <thead>
                <tr style="border-bottom:1px solid var(--glass-border); text-align:left;">
                    <th style="padding:10px;">Voter</th>
                    <th style="padding:10px;">Election</th>
                    <th style="padding:10px;">Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_votes as $vote): ?>
                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                    <td style="padding:10px;"><?php echo htmlspecialchars($vote['voter_name']); ?></td>
                    <td style="padding:10px; font-size:0.82rem; opacity:0.85;"><?php echo htmlspecialchars($vote['election_title']); ?></td>
                    <td style="padding:10px; opacity:0.55; font-size:0.8rem;"><?php echo date('H:i, d M', strtotime($vote['voted_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($recent_votes)): ?>
                <tr><td colspan="3" style="padding:20px; text-align:center; opacity:0.5;">No recent activity.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="glass-card">
        <h3 style="margin-bottom:1.5rem;">Quick Actions</h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
            <a href="elections.php?action=add" class="btn btn-primary" style="text-align:center; padding:1.5rem 0;">
                <i class="fas fa-plus-circle"></i><br> Create Election
            </a>
            <a href="candidates.php?action=add" class="btn btn-glass" style="text-align:center; padding:1.5rem 0;">
                <i class="fas fa-plus-circle"></i><br> Add Candidate
            </a>
            <a href="voters.php" class="btn btn-glass" style="text-align:center; padding:1.5rem 0;">
                <i class="fas fa-user-check"></i><br> Verify Voters
            </a>
            <a href="results.php" class="btn btn-glass" style="text-align:center; padding:1.5rem 0;">
                <i class="fas fa-file-export"></i><br> Export Results
            </a>
        </div>
    </div>
</div>

<!-- Chart.js is loaded via main header -->
<script>
Chart.defaults.color = 'rgba(255,255,255,0.75)';
Chart.defaults.borderColor = 'rgba(255,255,255,0.07)';
const P = ['#00cec9','#6c5ce7','#fdcb6e','#e17055','#55efc4','#74b9ff','#fd79a8','#a29bfe'];

// 1. Horizontal Bar — votes per election
new Chart(document.getElementById('votePerElectionChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($vote_per_election, 'title') ?: ['No elections yet']); ?>,
        datasets:[{
            label: 'Votes',
            data: <?php echo json_encode(array_column($vote_per_election, 'cnt') ?: [0]); ?>,
            backgroundColor: P.map(c=>c+'bb'),
            borderColor: P,
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options:{
        indexAxis:'y',
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{
            x:{beginAtZero:true, ticks:{stepSize:1}, grid:{color:'rgba(255,255,255,0.05)'}},
            y:{grid:{display:false}, ticks:{font:{size:11}}}
        },
        animation:{duration:900}
    }
});

// 2. Doughnut — participation
new Chart(document.getElementById('participationChart'), {
    type:'doughnut',
    data:{
        labels:['Voted','Not Yet'],
        datasets:[{
            data:[<?php echo $did_vote; ?>,<?php echo $not_voted; ?>],
            backgroundColor:['rgba(0,206,201,0.8)','rgba(255,255,255,0.1)'],
            borderColor:['#00cec9','rgba(255,255,255,0.2)'],
            borderWidth:2,hoverOffset:10
        }]
    },
    options:{
        cutout:'68%',
        plugins:{legend:{position:'bottom',labels:{padding:14,font:{size:12}}}},
        animation:{animateRotate:true,duration:1100}
    }
});

// 3. Doughnut — voter status
const vsLabels = <?php echo json_encode(array_column($voter_status,'status') ?: ['none']); ?>;
const vsCounts = <?php echo json_encode(array_column($voter_status,'cnt') ?: [0]); ?>;
const vsColors = {'active':'rgba(0,206,201,0.8)','pending':'rgba(253,203,110,0.8)','blocked':'rgba(225,112,85,0.8)'};
new Chart(document.getElementById('voterStatusChart'), {
    type:'doughnut',
    data:{
        labels: vsLabels,
        datasets:[{
            data: vsCounts,
            backgroundColor: vsLabels.map(l=>vsColors[l]||'rgba(255,255,255,0.2)'),
            borderWidth:2,hoverOffset:8
        }]
    },
    options:{
        cutout:'60%',
        plugins:{legend:{position:'bottom',labels:{padding:14,font:{size:12}}}},
        animation:{animateRotate:true,duration:1100}
    }
});

// 4. Line — daily registrations
const regDays   = <?php echo json_encode(array_column($reg_trend,'day') ?: []); ?>;
const regCounts = <?php echo json_encode(array_column($reg_trend,'cnt') ?: []); ?>;
new Chart(document.getElementById('regTrendChart'), {
    type:'line',
    data:{
        labels: regDays.length ? regDays : ['No data'],
        datasets:[{
            label:'Registrations',
            data: regCounts.length ? regCounts : [0],
            borderColor:'#6c5ce7',
            backgroundColor:'rgba(108,92,231,0.15)',
            fill:true,
            tension:0.4,
            pointBackgroundColor:'#6c5ce7',
            pointRadius:5,
            pointHoverRadius:8,
            borderWidth:2
        }]
    },
    options:{
        responsive:true,
        plugins:{legend:{display:false}},
        scales:{
            y:{beginAtZero:true,ticks:{stepSize:1},grid:{color:'rgba(255,255,255,0.05)'}},
            x:{grid:{display:false},ticks:{maxTicksLimit:7,font:{size:10}}}
        },
        animation:{duration:1000}
    }
});
</script>

<style>
.stat-card { transition:0.3s; }
.stat-card:hover { transform:translateY(-4px); background:rgba(255,255,255,0.25); }
</style>

<?php include 'includes/footer.php'; ?>
