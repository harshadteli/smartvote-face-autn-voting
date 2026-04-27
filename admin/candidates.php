<?php
include 'includes/header.php';

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    if ($stmt->execute([id])) {
        $message = "Candidate removed successfully!";
        $action = 'view';
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $election_id = (int)$_POST['election_id'];
    $party = sanitize($_POST['party']);
    $bio = sanitize($_POST['bio']);
    $photo_url = sanitize($_POST['photo_url']);

    if (isset($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE candidates SET name=?, election_id=?, party=?, bio=?, photo_url=? WHERE id=?");
        if ($stmt->execute([$name, $election_id, $party, $bio, $photo_url, $id])) {
            $message = "Candidate updated successfully!";
            $action = 'view';
        }
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO candidates (name, election_id, party, bio, photo_url) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $election_id, $party, $bio, $photo_url])) {
            $message = "Candidate added successfully!";
            $action = 'view';
        }
    }
}

// Fetch Candidates with Election Title
$stmt = $conn->prepare("
    SELECT c.*, e.title as election_title 
    FROM candidates c 
    LEFT JOIN elections e ON c.election_id = e.id 
    ORDER BY e.created_at DESC, c.name ASC
");
$stmt->execute();
$candidates = $stmt->fetchAll();

// Fetch Elections for dropdown
$elections = $conn->query("SELECT id, title FROM elections")->fetchAll();

// Fetch single for edit
$candidate_to_edit = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    foreach ($candidates as $c) {
        if ($c['id'] == $id) {
            $candidate_to_edit = $c;
            break;
        }
    }
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Manage <span style="color: var(--accent-color);">Candidates</span></h2>
    <?php if ($action == 'view'): ?>
        <a href="candidates.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Candidate</a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div style="background: rgba(85, 239, 196, 0.2); border: 1px solid #55efc4; padding: 15px; border-radius: 10px; margin-bottom: 2rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($action == 'add' || $action == 'edit'): ?>
    <div class="glass-card" style="max-width: 800px;">
        <h3><?php echo ($action == 'edit') ? 'Edit Candidate' : 'Add New Candidate'; ?></h3>
        <form action="candidates.php" method="POST" style="margin-top: 1.5rem;">
            <?php if ($candidate_to_edit): ?>
                <input type="hidden" name="id" value="<?php echo $candidate_to_edit['id']; ?>">
            <?php endif; ?>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Election</label>
                <select name="election_id" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(0, 0, 0, 0.5); color: white; outline: none;">
                    <option value="">Select Election</option>
                    <?php foreach($elections as $e): ?>
                        <option value="<?php echo $e['id']; ?>" <?php echo (isset($candidate_to_edit) && $candidate_to_edit['election_id'] == $e['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($e['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Candidate Name</label>
                <input type="text" name="name" value="<?php echo $candidate_to_edit['name'] ?? ''; ?>" required style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Party / Affiliation</label>
                <input type="text" name="party" value="<?php echo $candidate_to_edit['party'] ?? ''; ?>" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Photo URL (Online Image)</label>
                <input type="url" name="photo_url" value="<?php echo $candidate_to_edit['photo_url'] ?? ''; ?>" placeholder="https://example.com/photo.jpg" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;">
            </div>
            
            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Bio / Description</label>
                <textarea name="bio" rows="4" style="width: 100%; padding: 0.8rem; border-radius: 10px; border: 1px solid var(--glass-border); background: rgba(255, 255, 255, 0.1); color: white; outline: none;"><?php echo $candidate_to_edit['bio'] ?? ''; ?></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary"><?php echo ($action == 'edit') ? 'Update Candidate' : 'Save Candidate'; ?></button>
                <a href="candidates.php" class="btn btn-glass">Cancel</a>
            </div>
        </form>
    </div>
<?php else: ?>
<div class="glass-card" style="padding: 1rem;">
        <div style="overflow-x: auto; width: 100%;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border); text-align: left;">
                        <th style="padding: 15px;">Candidate</th>
                        <th style="padding: 15px;">Election</th>
                        <th style="padding: 15px;">Party</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px; display: flex; align-items: center; gap: 10px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); overflow: hidden;">
                                <?php if($candidate['photo_url']): ?>
                                    <img src="<?php echo htmlspecialchars($candidate['photo_url']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="fas fa-user" style="padding: 10px; color: var(--accent-color);"></i>
                                <?php endif; ?>
                            </div>
                            <strong><?php echo htmlspecialchars($candidate['name']); ?></strong>
                        </td>
                        <td style="padding: 15px; font-size: 0.85rem; opacity: 0.8;">
                            <?php echo htmlspecialchars($candidate['election_title']); ?>
                        </td>
                        <td style="padding: 15px; font-weight: 500;">
                            <?php echo htmlspecialchars($candidate['party']); ?>
                        </td>
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 10px;">
                                <a href="candidates.php?action=edit&id=<?php echo $candidate['id']; ?>" title="Edit" style="color: var(--accent-color);"><i class="fas fa-edit"></i></a>
                                <a href="candidates.php?action=delete&id=<?php echo $candidate['id']; ?>" title="Delete" style="color: #ff7675;" onclick="return confirm('Are you sure you want to remove this candidate?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($candidates)): ?>
                    <tr><td colspan="4" style="padding: 30px; text-align: center; opacity: 0.5;">No candidates added yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
