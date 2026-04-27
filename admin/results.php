<?php
include 'includes/header.php';

// Fetch all elections
$stmt = $conn->prepare("SELECT * FROM elections ORDER BY created_at DESC");
$stmt->execute();
$elections = $stmt->fetchAll();

$selected_results = null;
$chart_labels = [];
$chart_votes  = [];
$chart_colors = ['#00cec9','#6c5ce7','#fdcb6e','#e17055','#55efc4','#74b9ff','#fd79a8','#a29bfe'];

if (isset($_GET['id'])) {
    $election_id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM elections WHERE id = ?");
    $stmt->execute([$election_id]);
    $election_info = $stmt->fetch();

    if ($election_info) {
        $stmt = $conn->prepare("
            SELECT c.*, COUNT(v.id) as vote_count
            FROM candidates c
            LEFT JOIN votes v ON c.id = v.candidate_id
            WHERE c.election_id = ?
            GROUP BY c.id
            ORDER BY vote_count DESC
        ");
        $stmt->execute([$election_id]);
        $candidates_results = $stmt->fetchAll();

        $total_votes = 0;
        foreach ($candidates_results as $cr) $total_votes += $cr['vote_count'];

        // Build chart data
        foreach ($candidates_results as $cr) {
            $chart_labels[] = $cr['name'];
            $chart_votes[]  = (int)$cr['vote_count'];
        }

        $selected_results = [
            'election' => $election_info,
            'results'  => $candidates_results,
            'total_votes' => $total_votes
        ];
    }
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <h2>Election <span style="color:var(--accent-color);">Results Tracking</span></h2>
    <?php if ($selected_results): ?>
        <button onclick="window.print()" class="btn btn-glass"><i class="fas fa-print"></i> Export to PDF</button>
    <?php endif; ?>
</div>

<?php if ($selected_results): ?>

    <!-- Back + Election Header -->
    <div class="glass-card" style="margin-bottom:2rem;">
        <a href="results.php" style="color:var(--accent-color); text-decoration:none; font-size:0.9rem;">
            <i class="fas fa-arrow-left"></i> Back to Overview
        </a>
        <div style="text-align:center; margin:1.5rem 0 0.5rem;">
            <h3 style="font-size:1.8rem;"><?php echo htmlspecialchars($selected_results['election']['title']); ?></h3>
            <p style="opacity:0.7;">Final Tally & Participation Report</p>
            <span style="display:inline-block; margin-top:0.5rem; padding:4px 16px; border-radius:20px; font-size:0.8rem; font-weight:600;
                background:<?php echo $selected_results['election']['status']==='active'?'rgba(85,239,196,0.2)':'rgba(255,255,255,0.1)'; ?>;
                color:<?php echo $selected_results['election']['status']==='active'?'#55efc4':'rgba(255,255,255,0.7)'; ?>;
                border:1px solid <?php echo $selected_results['election']['status']==='active'?'#55efc4':'rgba(255,255,255,0.2)'; ?>;">
                <?php echo strtoupper($selected_results['election']['status']); ?>
            </span>
        </div>
    </div>

    <!-- ── Live Charts Row ────────────────────────────────────────────── -->
    <?php if ($selected_results['total_votes'] > 0): ?>
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:2rem; margin-bottom:2.5rem;">

        <!-- Doughnut -->
        <div class="glass-card" style="text-align:center;">
            <h4 style="margin-bottom:1rem; opacity:0.85; font-size:1rem;">🍩 Vote Share</h4>
            <canvas id="donutChart" height="260"></canvas>
        </div>

        <!-- Horizontal Bar -->
        <div class="glass-card">
            <h4 style="margin-bottom:1rem; opacity:0.85; font-size:1rem;">📊 Votes Breakdown</h4>
            <canvas id="barChart" height="260"></canvas>
        </div>

    </div>
    <?php endif; ?>

    <!-- ── Candidate Cards ─────────────────────────────────────────────── -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:2rem; margin-bottom:2rem;">
        <?php foreach ($selected_results['results'] as $index => $candidate):
            $pct = ($selected_results['total_votes'] > 0)
                   ? round(($candidate['vote_count'] / $selected_results['total_votes']) * 100, 1) : 0;
            $is_winner = ($index === 0 && $selected_results['total_votes'] > 0);
        ?>
            <div class="glass-card" style="text-align:center; position:relative;
                border-color:<?php echo $is_winner ? 'var(--accent-color)' : 'var(--glass-border)'; ?>;
                <?php echo $is_winner ? 'box-shadow:0 0 30px rgba(0,206,201,0.25);' : ''; ?>">

                <?php if ($is_winner): ?>
                    <div style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:var(--accent-color); color:#fff; padding:4px 18px; border-radius:20px; font-size:0.78rem; font-weight:700; white-space:nowrap; box-shadow:0 4px 12px rgba(0,206,201,0.4);">
                        🏆 WINNER
                    </div>
                <?php endif; ?>

                <div style="width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,0.1); margin:1rem auto; overflow:hidden; border:2px solid var(--accent-color);">
                    <img src="<?php echo $candidate['photo_url'] ?: '../assets/img/voter-avatar.png'; ?>"
                         style="width:100%; height:100%; object-fit:cover;"
                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($candidate['name']); ?>&background=random'">
                </div>

                <h4 style="font-size:1.15rem;"><?php echo htmlspecialchars($candidate['name']); ?></h4>
                <p style="font-size:0.8rem; opacity:0.6; margin-bottom:1rem;"><?php echo htmlspecialchars($candidate['party']); ?></p>

                <!-- Progress bar -->
                <div style="background:rgba(255,255,255,0.08); border-radius:50px; height:8px; margin-bottom:0.6rem; overflow:hidden;">
                    <div style="height:100%; width:<?php echo $pct; ?>%; background:linear-gradient(90deg,var(--accent-color),var(--primary-color)); border-radius:50px; transition:1.2s ease;" class="progress-bar"></div>
                </div>

                <div style="font-size:1.6rem; font-weight:800; color:var(--accent-color);"><?php echo $candidate['vote_count']; ?></div>
                <div style="font-size:0.85rem; opacity:0.7;"><?php echo $pct; ?>% of total</div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Summary Strip -->
    <div class="glass-card" style="margin-top:1rem;">
        <div style="display:flex; justify-content:center; gap:4rem; flex-wrap:wrap; text-align:center; padding:0.5rem 0;">
            <div>
                <div style="font-size:1.4rem; font-weight:700; color:var(--accent-color);"><?php echo $selected_results['total_votes']; ?></div>
                <div style="font-size:0.8rem; opacity:0.6;" data-i18n="results_total">Total Ballots</div>
            </div>
            <div>
                <div style="font-size:1.4rem; font-weight:700; color:var(--accent-color);"><?php echo count($selected_results['results']); ?></div>
                <div style="font-size:0.8rem; opacity:0.6;" data-i18n="results_candidates">Candidates</div>
            </div>
            <div>
                <div style="font-size:1.4rem; font-weight:700; color:var(--accent-color);"><?php echo strtoupper($selected_results['election']['status']); ?></div>
                <div style="font-size:0.8rem; opacity:0.6;" data-i18n="results_status">Election Status</div>
            </div>
        </div>
    </div>

<?php else: ?>

    <!-- Election List Cards -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:2rem;">
        <?php foreach ($elections as $election): ?>
            <div class="glass-card">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                    <h4 style="font-size:1.2rem;"><?php echo htmlspecialchars($election['title']); ?></h4>
                    <span style="font-size:0.7rem; opacity:0.6;"><?php echo date('Y', strtotime($election['created_at'])); ?></span>
                </div>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM votes WHERE election_id = ?");
                $stmt->execute([$election['id']]);
                $count = $stmt->fetchColumn();
                $statusColors = ['active'=>'#55efc4','completed'=>'#74b9ff','upcoming'=>'#fdcb6e'];
                $sc = $statusColors[$election['status']] ?? '#ffffff';
                ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                    <p style="font-size:0.9rem; opacity:0.8; margin:0;">
                        <strong><?php echo $count; ?></strong> Votes Cast
                    </p>
                    <span style="background:rgba(255,255,255,0.08); color:<?php echo $sc; ?>; padding:3px 12px; border-radius:20px; font-size:0.75rem; border:1px solid <?php echo $sc; ?>;">
                        <?php echo strtoupper($election['status']); ?>
                    </span>
                </div>
                <div style="display:flex; gap:10px;">
                    <a href="results.php?id=<?php echo $election['id']; ?>" class="btn btn-primary" style="flex:1; text-align:center; font-size:0.85rem;">
                        <i class="fas fa-chart-pie"></i> View Results
                    </a>
                    <a href="elections.php?action=edit&id=<?php echo $election['id']; ?>" class="btn btn-glass" style="font-size:0.85rem;"><i class="fas fa-cog"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($elections)): ?>
            <div class="glass-card" style="text-align:center; padding:3rem; grid-column:1/-1;">
                <i class="fas fa-poll" style="font-size:3rem; opacity:0.3; margin-bottom:1rem;"></i>
                <p style="opacity:0.6; font-size:1.1rem;">No elections yet.</p>
            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

<!-- Chart.js rendering (only when results are showing) -->
<?php if ($selected_results && !empty($chart_labels)): ?>
<script>
Chart.defaults.color = 'rgba(255,255,255,0.75)';
const PALETTE = <?php echo json_encode(array_slice($chart_colors, 0, count($chart_labels))); ?>;
const LABELS  = <?php echo json_encode($chart_labels); ?>;
const VOTES   = <?php echo json_encode($chart_votes); ?>;

// Doughnut
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: LABELS,
        datasets: [{ data: VOTES, backgroundColor: PALETTE.map(c=>c+'cc'), borderColor: PALETTE, borderWidth: 2, hoverOffset: 12 }]
    },
    options: {
        cutout: '60%',
        plugins: { legend: { position:'bottom', labels: { padding:14, font:{size:11} } } },
        animation: { animateRotate:true, duration:1200 }
    }
});

// Horizontal Bar
new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: LABELS,
        datasets: [{ label:'Votes', data: VOTES, backgroundColor: PALETTE.map(c=>c+'bb'), borderColor: PALETTE, borderWidth:2, borderRadius:8 }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend:{display:false} },
        scales: {
            x: { beginAtZero:true, ticks:{stepSize:1}, grid:{color:'rgba(255,255,255,0.05)'} },
            y: { grid:{display:false}, ticks:{font:{size:12}} }
        },
        animation: { duration:900 }
    }
});

// Animate progress bars
document.querySelectorAll('.progress-bar').forEach(b => {
    const w = b.style.width; b.style.width='0';
    setTimeout(()=>{ b.style.width=w; }, 300);
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
