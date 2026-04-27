<?php
include 'includes/header.php';

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// Handle Status Updates (Verify/Block)
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = sanitize($_GET['status']);
    
    if (in_array($status, ['active', 'blocked', 'pending'])) {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'voter'");
        if ($stmt->execute([$status, $id])) {
            $message = "Voter status updated to " . ucfirst($status) . ".";
        }
    }
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Don't delete self
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'voter'");
        if ($stmt->execute([$id])) {
            $message = "Voter account deleted successfully.";
        }
    }
}

// Fetch Voters
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'voter' ORDER BY created_at DESC");
$stmt->execute();
$voters = $stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Manage <span style="color: var(--accent-color);">Voters</span></h2>
    <div style="font-size: 0.9rem; opacity: 0.7;">
        Total: <?php echo count($voters); ?> registered voters
    </div>
</div>

<?php if ($message): ?>
    <div style="background: rgba(85, 239, 196, 0.2); border: 1px solid #55efc4; padding: 15px; border-radius: 10px; margin-bottom: 2rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="glass-card" style="padding: 1rem;">
    <div style="overflow-x: auto; width: 100%;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--glass-border); text-align: left;">
                    <th style="padding: 15px;">Name</th>
                    <th style="padding: 15px;">Email</th>
                    <th style="padding: 15px;">Status</th>
                    <th style="padding: 15px;">Registered</th>
                    <th style="padding: 15px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($voters as $voter): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 15px;">
                        <strong><?php echo htmlspecialchars($voter['full_name']); ?></strong>
                    </td>
                    <td style="padding: 15px; font-size: 0.9rem; opacity: 0.8;">
                        <?php echo htmlspecialchars($voter['email']); ?>
                    </td>
                    <td style="padding: 15px;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; 
                            background: <?php echo ($voter['status'] == 'active' ? 'rgba(85, 239, 196, 0.2)' : ($voter['status'] == 'pending' ? 'rgba(255, 234, 167, 0.2)' : 'rgba(255, 118, 117, 0.2)')); ?>; 
                            color: <?php echo ($voter['status'] == 'active' ? '#55efc4' : ($voter['status'] == 'pending' ? '#ffeaa7' : '#ff7675')); ?>;">
                            <?php echo $voter['status']; ?>
                        </span>
                    </td>
                    <td style="padding: 15px; font-size: 0.85rem; opacity: 0.6;">
                        <?php echo date('M d, Y', strtotime($voter['created_at'])); ?>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 15px; font-size: 1.1rem;">
                            <?php if ($voter['status'] != 'active'): ?>
                                <a href="voters.php?id=<?php echo $voter['id']; ?>&status=active" title="Verify Voter" style="color: #55efc4;"><i class="fas fa-check-circle"></i></a>
                            <?php else: ?>
                                <a href="voters.php?id=<?php echo $voter['id']; ?>&status=blocked" title="Block Voter" style="color: #fab1a0;"><i class="fas fa-ban"></i></a>
                            <?php endif; ?>
                            
                            <a href="voters.php?action=delete&id=<?php echo $voter['id']; ?>" title="Delete Voter" style="color: #ff7675;" onclick="return confirm('Are you sure you want to permanently delete this voter account?')"><i class="fas fa-trash-alt"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($voters)): ?>
                <tr><td colspan="5" style="padding: 30px; text-align: center; opacity: 0.5;">No voters registered yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
