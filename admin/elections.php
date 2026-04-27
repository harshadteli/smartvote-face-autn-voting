<?php
include 'includes/header.php';

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM elections WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = "Election deleted successfully!";
        $action = 'view';
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    if (isset($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE elections SET title=?, description=?, start_date=?, end_date=?, status=? WHERE id=?");
        if ($stmt->execute([$title, $description, $start_date, $end_date, $status, $id])) {
            $message = "Election updated successfully!";
            $action = 'view';
        }
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO elections (title, description, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $description, $start_date, $end_date, $status])) {
            $message = "Election created successfully!";
            $action = 'view';
        }
    }
}

// Fetch Elections
$stmt = $conn->prepare("SELECT * FROM elections ORDER BY created_at DESC");
$stmt->execute();
$elections = $stmt->fetchAll();

// Fetch single for edit
$election_to_edit = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    foreach ($elections as $e) {
        if ($e['id'] == $id) {
            $election_to_edit = $e;
            break;
        }
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Manage <span style="color: var(--accent-color);">Elections</span></h2>
    <?php if ($action == 'view'): ?>
        <a href="elections.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Create New Election</a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div style="background: rgba(85, 239, 196, 0.2); border: 1px solid #55efc4; padding: 15px; border-radius: 10px; margin-bottom: 2rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
    <div class="glass-card" style="max-width: 800px;">
        <h3><?php echo ($action == 'edit') ? 'Edit Election' : 'Create New Election'; ?></h3>
        <form action="elections.php" method="POST" style="margin-top: 1.5rem;">
            <?php if ($election_to_edit): ?>
                <input type="hidden" name="id" value="<?php echo $election_to_edit['id']; ?>">
            <?php endif; ?>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Election Title</label>
                <input type="text" name="title" value="<?php echo $election_to_edit['title'] ?? ''; ?>" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Description</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;"><?php echo $election_to_edit['description'] ?? ''; ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">Start Date</label>
                    <input type="date" name="start_date" value="<?php echo $election_to_edit['start_date'] ?? ''; ?>" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem;">End Date</label>
                    <input type="date" name="end_date" value="<?php echo $election_to_edit['end_date'] ?? ''; ?>" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
                </div>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Status</label>
                <select name="status" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(0, 0, 0, 0.5); color: white; outline: none;">
                    <option value="upcoming" <?php echo (isset($election_to_edit) && $election_to_edit['status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="active" <?php echo (isset($election_to_edit) && $election_to_edit['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="completed" <?php echo (isset($election_to_edit) && $election_to_edit['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary"><?php echo ($action == 'edit') ? 'Update Election' : 'Save Election'; ?></button>
                <a href="elections.php" class="btn btn-glass">Cancel</a>
            </div>
        </form>
    </div>
<?php else: ?>
<div class="glass-card" style="padding: 1rem;">
        <div style="overflow-x: auto; width: 100%;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border); text-align: left;">
                        <th style="padding: 15px;">Title</th>
                        <th style="padding: 15px;">Timeline</th>
                        <th style="padding: 15px;">Status</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($elections as $election): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px;">
                            <strong><?php echo htmlspecialchars($election['title']); ?></strong>
                        </td>
                        <td style="padding: 15px; font-size: 0.85rem; opacity: 0.8;">
                            <?php echo date('M d', strtotime($election['start_date'])); ?> - <?php echo date('M d, Y', strtotime($election['end_date'])); ?>
                        </td>
                        <td style="padding: 15px;">
                            <span style="padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; background: <?php 
                                echo ($election['status'] == 'active' ? 'rgba(85, 239, 196, 0.2)' : ($election['status'] == 'upcoming' ? 'rgba(255, 234, 167, 0.2)' : 'rgba(255, 118, 117, 0.2)')); ?>; color: <?php 
                                echo ($election['status'] == 'active' ? '#55efc4' : ($election['status'] == 'upcoming' ? '#ffeaa7' : '#ff7675')); ?>;">
                                <?php echo $election['status']; ?>
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 10px;">
                                <a href="elections.php?action=edit&id=<?php echo $election['id']; ?>" title="Edit" style="color: var(--accent-color);"><i class="fas fa-edit"></i></a>
                                <a href="elections.php?action=delete&id=<?php echo $election['id']; ?>" title="Delete" style="color: #ff7675;" onclick="return confirm('Are you sure you want to delete this election?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($elections)): ?>
                    <tr><td colspan="4" style="padding: 30px; text-align: center; opacity: 0.5;">No elections found. Create your first one!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
