<?php
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) redirect('login.php');

include 'includes/header.php';

// All elections
$stmt = $conn->prepare("SELECT * FROM elections ORDER BY created_at DESC");
$stmt->execute();
$elections = $stmt->fetchAll();

$selected_results = null;
$total_votes  = 0;
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

        foreach ($candidates_results as $cr) {
            $total_votes    += $cr['vote_count'];
            $chart_labels[]  = $cr['name'] . ' (' . $cr['party'] . ')';
            $chart_votes[]   = (int)$cr['vote_count'];
        }

        $selected_results = [
            'election' => $election_info,
            'results'  => $candidates_results
        ];
    }
}
?>

<div style="padding:2rem 0;">
    <h2 style="margin-bottom:2rem; font-size:2.2rem; text-align:center;" data-i18n="results_title">
        Election <span style="color:var(--accent-color);">Results</span>
    </h2>

    <?php if ($selected_results): ?>

        <!-- Header Card -->
        <div class="glass-card" style="margin-bottom:2rem;">
            <a href="results.php" style="color:var(--accent-color); text-decoration:none; font-size:0.9rem;">
                <i class="fas fa-arrow-left"></i> View All Results
            </a>
            <div style="text-align:center; margin-top:1.2rem;">
                <h3 style="color:var(--accent-color); font-size:1.7rem;">
                    <?php echo htmlspecialchars($selected_results['election']['title']); ?>
                </h3>
                <p style="opacity:0.8; margin-top:0.5rem; font-size:0.95rem;">
                    <?php echo htmlspecialchars($selected_results['election']['description']); ?>
                </p>
                <span style="display:inline-block; margin-top:0.8rem; padding:4px 16px; border-radius:20px; font-size:0.8rem; font-weight:600;
                    background:<?php echo $selected_results['election']['status']==='active'?'rgba(85,239,196,0.2)':'rgba(255,255,255,0.1)'; ?>;
                    color:<?php echo $selected_results['election']['status']==='active'?'#55efc4':'rgba(255,255,255,0.7)'; ?>;
                    border:1px solid <?php echo $selected_results['election']['status']==='active'?'#55efc4':'rgba(255,255,255,0.2)'; ?>;">
                    <?php echo strtoupper($selected_results['election']['status']); ?>
                </span>
            </div>
        </div>

        <?php if ($total_votes > 0): ?>
        <!-- ── Charts ─────────────────────────────────────────────────── -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:2rem; margin-bottom:2rem;">
            <div class="glass-card" style="text-align:center;">
                <h4 style="margin-bottom:1rem; opacity:0.8; font-size:0.95rem;">🍩 Vote Share Distribution</h4>
                <canvas id="votePieChart" height="240"></canvas>
            </div>
            <div class="glass-card">
                <h4 style="margin-bottom:1rem; opacity:0.8; font-size:0.95rem;">📊 Candidate Comparison</h4>
                <canvas id="voteBarChart" height="240"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Candidate Progress Bars ─────────────────────────────────── -->
        <div class="glass-card" style="margin-bottom:2rem;">
            <?php
            $palette_idx = 0;
            foreach ($selected_results['results'] as $idx => $candidate):
                $pct = ($total_votes > 0) ? round(($candidate['vote_count'] / $total_votes) * 100, 1) : 0;
                $color = $chart_colors[$palette_idx % count($chart_colors)];
                $palette_idx++;
            ?>
                <div style="margin-bottom:1.8rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.6rem;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <?php if ($idx === 0 && $total_votes > 0): ?>
                                <span style="font-size:1rem;">🏆</span>
                            <?php endif; ?>
                            <div>
                                <strong><?php echo htmlspecialchars($candidate['name']); ?></strong>
                                <span style="font-size:0.82rem; opacity:0.65; margin-left:6px;">
                                    <?php echo htmlspecialchars($candidate['party']); ?>
                                </span>
                            </div>
                        </div>
                        <span style="font-weight:700; color:<?php echo $color; ?>; font-size:1rem;">
                            <?php echo $candidate['vote_count']; ?> <span style="font-size:0.8rem; opacity:0.7;">(<?php echo $pct; ?>%)</span>
                        </span>
                    </div>
                    <div style="height:10px; background:rgba(255,255,255,0.08); border-radius:50px; overflow:hidden;">
                        <div class="prog-bar" data-width="<?php echo $pct; ?>"
                             style="width:0; height:100%; background:linear-gradient(90deg,<?php echo $color; ?>,<?php echo $color; ?>99); border-radius:50px; transition:width 1.2s ease-out;">
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div style="text-align:center; margin-top:1.5rem; padding-top:1rem; border-top:1px solid var(--glass-border);">
                <span style="font-size:1.1rem;">
                    Total Participation: <strong style="color:var(--accent-color);"><?php echo $total_votes; ?> Votes</strong>
                </span>
            </div>
        </div>

    <?php else: ?>

        <!-- Election List -->
        <?php if (empty($elections)): ?>
            <div class="glass-card" style="text-align:center; padding:3rem;">
                <i class="fas fa-poll" style="font-size:3rem; opacity:0.3; margin-bottom:1rem;"></i>
                <p style="opacity:0.6; font-size:1.1rem;">No elections available yet.</p>
            </div>
        <?php else: ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:2rem;">
                <?php foreach ($elections as $election):
                    $s = $conn->prepare("SELECT COUNT(*) FROM votes WHERE election_id = ?");
                    $s->execute([$election['id']]);
                    $cnt = $s->fetchColumn();
                    $sc = ['active'=>'#55efc4','completed'=>'#74b9ff','upcoming'=>'#fdcb6e'];
                    $color = $sc[$election['status']] ?? '#ffffff';
                ?>
                    <div class="glass-card">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.8rem;">
                            <h4 style="font-size:1.2rem;"><?php echo htmlspecialchars($election['title']); ?></h4>
                            <span style="padding:3px 12px; border-radius:20px; font-size:0.75rem; font-weight:600; color:<?php echo $color; ?>; border:1px solid <?php echo $color; ?>; background:<?php echo $color; ?>18; white-space:nowrap;">
                                <?php echo strtoupper($election['status']); ?>
                            </span>
                        </div>
                        <p style="font-size:0.88rem; opacity:0.7; margin-bottom:1.2rem;">
                            <i class="fas fa-vote-yea" style="color:var(--accent-color);"></i>
                            <strong><?php echo $cnt; ?></strong> votes cast so far
                        </p>
                        <a href="results.php?id=<?php echo $election['id']; ?>" class="btn btn-glass" style="width:100%; text-align:center;">
                            <i class="fas fa-chart-pie"></i> View Live Results
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php if ($selected_results && $total_votes > 0): ?>
<script>
Chart.defaults.color = 'rgba(255,255,255,0.75)';
const PALETTE = <?php echo json_encode(array_slice($chart_colors, 0, count($chart_labels))); ?>;
const LABELS  = <?php echo json_encode($chart_labels); ?>;
const VOTES   = <?php echo json_encode($chart_votes); ?>;

// Doughnut / Pie
new Chart(document.getElementById('votePieChart'), {
    type: 'doughnut',
    data: {
        labels: LABELS,
        datasets: [{ data: VOTES, backgroundColor: PALETTE.map(c=>c+'cc'), borderColor: PALETTE, borderWidth:2, hoverOffset:10 }]
    },
    options: {
        cutout: '55%',
        plugins: { legend: { position:'bottom', labels: { padding:12, font:{size:11}, boxWidth:14 } } },
        animation: { animateRotate:true, duration:1200 }
    }
});

// Bar
new Chart(document.getElementById('voteBarChart'), {
    type: 'bar',
    data: {
        labels: LABELS,
        datasets: [{ label:'Votes', data: VOTES, backgroundColor: PALETTE.map(c=>c+'bb'), borderColor: PALETTE, borderWidth:2, borderRadius:8 }]
    },
    options: {
        responsive:true,
        plugins: { legend:{display:false} },
        scales: {
            y:{ beginAtZero:true, ticks:{stepSize:1}, grid:{color:'rgba(255,255,255,0.05)'} },
            x:{ grid:{display:false}, ticks:{font:{size:10}, maxRotation:20} }
        },
        animation:{ duration:900 }
    }
});

// Animate progress bars
document.querySelectorAll('.prog-bar').forEach(b => {
    setTimeout(() => { b.style.width = b.dataset.width + '%'; }, 200);
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
