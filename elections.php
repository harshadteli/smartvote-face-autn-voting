<?php
include 'includes/config.php';
include 'includes/header.php';

// Fetch all elections categorized by status
$stmt = $conn->prepare("SELECT * FROM elections ORDER BY start_date DESC");
$stmt->execute();
$all_elections = $stmt->fetchAll();

$active = [];
$upcoming = [];
$completed = [];

foreach ($all_elections as $election) {
    if ($election['status'] == 'active') $active[] = $election;
    elseif ($election['status'] == 'upcoming') $upcoming[] = $election;
    else $completed[] = $election;
}

// Get user votes to show "Already Voted" status
$voted_election_ids = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT election_id FROM votes WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $voted_election_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<div style="padding: 2rem 0;">
    <h2 style="margin-bottom: 2.5rem; font-size: 2.5rem; text-align: center;">Election <span style="color: var(--accent-color);">Portal</span></h2>

    <!-- Active Elections -->
    <section style="margin-bottom: 4rem;">
        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-vote-yea" style="color: #55efc4;"></i> Active Elections
        </h3>
        <?php if (empty($active)): ?>
            <p style="opacity: 0.6; font-style: italic;">No active elections at the moment.</p>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
                <?php foreach ($active as $election): ?>
                    <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between; border-left: 4px solid #55efc4;">
                        <div>
                            <h4 style="font-size: 1.4rem; color: var(--accent-color); margin-bottom: 1rem;"><?php echo htmlspecialchars($election['title']); ?></h4>
                            <p style="font-size: 0.95rem; opacity: 0.8; margin-bottom: 1.5rem;"><?php echo htmlspecialchars($election['description']); ?></p>
                            <div style="font-size: 0.85rem; opacity: 0.7; margin-bottom: 1.5rem;">
                                <i class="fas fa-calendar-check"></i> Ends: <?php echo date('M d, Y', strtotime($election['end_date'])); ?>
                            </div>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (in_array($election['id'], $voted_election_ids)): ?>
                                <button class="btn" style="background: rgba(85, 239, 196, 0.1); color: #55efc4; width: 100%; cursor: default;" disabled>
                                    <i class="fas fa-check-circle"></i> Vote Submitted
                                </button>
                            <?php else: ?>
                                <a href="vote.php?id=<?php echo $election['id']; ?>" class="btn btn-primary" style="text-align: center; width: 100%;">Cast My Vote</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-glass" style="text-align: center; width: 100%;">Login to Vote</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Upcoming Elections -->
    <section style="margin-bottom: 4rem;">
        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-calendar-alt" style="color: #ffeaa7;"></i> Upcoming Cycles
        </h3>
        <?php if (empty($upcoming)): ?>
            <p style="opacity: 0.6; font-style: italic;">No upcoming elections scheduled.</p>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
                <?php foreach ($upcoming as $election): ?>
                    <div class="glass-card" style="opacity: 0.9;">
                        <h4 style="font-size: 1.2rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($election['title']); ?></h4>
                        <p style="font-size: 0.9rem; opacity: 0.7; margin-bottom: 1.5rem;"><?php echo htmlspecialchars($election['description']); ?></p>
                        <div style="background: rgba(255, 234, 167, 0.1); color: #ffeaa7; padding: 10px; border-radius: 10px; font-size: 0.85rem; text-align: center;">
                            Starts on: <strong><?php echo date('M d, Y', strtotime($election['start_date'])); ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Completed Elections -->
    <section>
        <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-history" style="color: #ff7675;"></i> Completed Elections
        </h3>
        <?php if (empty($completed)): ?>
            <p style="opacity: 0.6; font-style: italic;">No completed election history.</p>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
                <?php foreach ($completed as $election): ?>
                    <div class="glass-card" style="background: rgba(0,0,0,0.2);">
                        <h4 style="font-size: 1.2rem; margin-bottom: 0.5rem; opacity: 0.8;"><?php echo htmlspecialchars($election['title']); ?></h4>
                        <p style="font-size: 0.85rem; opacity: 0.6; margin-bottom: 1.5rem;">Ended on: <?php echo date('M d, Y', strtotime($election['end_date'])); ?></p>
                        <a href="results.php?id=<?php echo $election['id']; ?>" class="btn btn-glass" style="width: 100%; text-align: center;">View Results Report</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
