<?php
include 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$user_id    = $_SESSION['user_id'];
$election_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$election_id) redirect('dashboard.php');

// Already voted?
$stmt = $conn->prepare("SELECT id FROM votes WHERE user_id = ? AND election_id = ?");
$stmt->execute([$user_id, $election_id]);
if ($stmt->fetch()) redirect('dashboard.php?msg=already_voted');

// Fetch election
$stmt = $conn->prepare("SELECT * FROM elections WHERE id = ? AND status = 'active'");
$stmt->execute([$election_id]);
$election = $stmt->fetch();
if (!$election) redirect('dashboard.php');

// Fetch voter profile picture for face verification
$stmt = $conn->prepare("SELECT profile_picture, full_name, voter_id FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$voter = $stmt->fetch();
$profile_pic_url = $voter['profile_picture'] ? 'uploads/' . htmlspecialchars($voter['profile_picture']) : '';

// Handle vote submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['candidate_id'])) {
    $candidate_id = (int)$_POST['candidate_id'];
    try {
        $stmt = $conn->prepare("INSERT INTO votes (user_id, election_id, candidate_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$user_id, $election_id, $candidate_id])) {
            // Trigger voting receipt via GAS
            // Use strpos() for PHP 7 compatibility (not str_contains which needs PHP 8)
            if (GAS_WEBAPP_URL && strpos(GAS_WEBAPP_URL, 'YOUR_GOOGLE') === false) {
                $stmt2 = $conn->prepare("SELECT name FROM candidates WHERE id = ?");
                $stmt2->execute([$candidate_id]);
                $cand = $stmt2->fetch();

                // Fetch voter email from DB as fallback if session email is missing
                $voter_email = '';
                if (!empty($_SESSION['user_email'])) {
                    $voter_email = $_SESSION['user_email'];
                } else {
                    $stmt3 = $conn->prepare("SELECT email FROM users WHERE id = ?");
                    $stmt3->execute([$user_id]);
                    $voter_email = $stmt3->fetchColumn();
                }

                $receipt_data = [
                    'action'         => 'sendVotingReceipt',
                    'email'          => $voter_email,
                    'name'           => $_SESSION['user_name'] ?? 'Voter',
                    'election'       => $election['title'],
                    'candidate'      => isset($cand['name']) ? $cand['name'] : 'N/A',
                    'voted_at'       => date('d M Y, h:i A'),
                    'transaction_id' => 'TXN-' . strtoupper(bin2hex(random_bytes(5))),
                ];

                $ch = curl_init(GAS_WEBAPP_URL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($receipt_data));
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);          // GAS needs up to 20s cold start
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Required on some XAMPP setups
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                curl_exec($ch);
                curl_close($ch);
            }
            redirect('dashboard.php?msg=vote_success');
        }
    } catch (PDOException $e) {
        $message = "Error casting vote: " . $e->getMessage();
    }
}

// Fetch candidates
$stmt = $conn->prepare("SELECT * FROM candidates WHERE election_id = ?");
$stmt->execute([$election_id]);
$candidates = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Face Verification Modal — fully responsive -->
<style>
/* ── Responsive Face Modal ───────────────────────── */
#face-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.88);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(8px);
    padding: 1rem;
    overflow-y: auto;
}
#face-modal.open { display: flex !important; }

.face-modal-inner {
    width: 100%;
    max-width: 480px;
    text-align: center;
    padding: clamp(1.2rem, 4vw, 2.5rem);
    animation: slideUp 0.4s ease;
    border-radius: 20px;
}

.face-cam-wrap {
    position: relative;
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    border-radius: 14px;
    overflow: hidden;
    border: 2px solid var(--accent-color);
    box-shadow: 0 0 28px rgba(0,206,201,0.3);
    background: #000;
    aspect-ratio: 4/3;
}
#face-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
#face-canvas {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
}
.face-modal-btns {
    display: flex;
    gap: 0.8rem;
    margin-top: 1.2rem;
    justify-content: center;
    flex-wrap: wrap;
}
.face-modal-btns .btn {
    flex: 1 1 130px;
    max-width: 200px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
}
/* Mobile (<480px) tweaks */
@media (max-width: 480px) {
    .face-modal-inner { padding: 1rem; }
    .face-modal-inner h3 { font-size: 1.1rem; }
    .face-modal-inner p  { font-size: 0.82rem; }
}
/* Status badge */
.face-status-badge {
    margin: 0.8rem auto 1rem;
    padding: 0.6rem 1rem;
    border-radius: 30px;
    font-weight: 600;
    font-size: 0.85rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid var(--glass-border);
    transition: 0.4s;
    max-width: 340px;
}
.face-status-info    { background:rgba(74,144,226,0.2)!important; border-color:var(--primary-color)!important; color:#74b9ff!important; }
.face-status-success { background:rgba(85,239,196,0.2)!important; border-color:#55efc4!important; color:#55efc4!important; }
.face-status-error   { background:rgba(255,118,117,0.2)!important; border-color:#ff7675!important; color:#ff7675!important; }
@keyframes slideUp { from{transform:translateY(40px);opacity:0} to{transform:translateY(0);opacity:1} }
</style>

<div id="face-modal">
    <div class="glass-card face-modal-inner">
        <div style="font-size:2.5rem; margin-bottom:0.6rem;">👤</div>
        <h3 data-i18n="vote_face_title">Biometric Verification Required</h3>
        <p style="opacity:0.75; margin:0.5rem 0 0.8rem;" data-i18n="vote_face_desc">Allow camera access. Your face will be matched to your profile picture.</p>

        <div id="face-status" class="face-status-badge">Ready to scan</div>

        <div class="face-cam-wrap">
            <video id="face-video" autoplay muted playsinline></video>
            <canvas id="face-canvas"></canvas>
        </div>

        <div class="face-modal-btns">
            <button id="start-scan-btn" onclick="startFaceScan()" class="btn btn-primary">
                <i class="fas fa-camera"></i> <span data-i18n="vote_face_btn">Start Face Scan</span>
            </button>
            <button onclick="closeFaceModal()" class="btn btn-glass">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>
    </div>
</div>

<div style="padding: 2rem 0;">
    <div class="glass-card" style="margin-bottom: 2rem;">
        <a href="dashboard.php" style="color: var(--accent-color); text-decoration: none; font-size: 0.9rem;">
            <i class="fas fa-arrow-left"></i> <span data-i18n="vote_back">Back to Dashboard</span>
        </a>
        <h2 style="margin-top: 1rem; font-size: 2rem;"><?php echo htmlspecialchars($election['title']); ?></h2>
        <p style="opacity: 0.8; margin-top: 0.5rem;"><?php echo htmlspecialchars($election['description']); ?></p>
    </div>

    <?php if($message): ?>
        <div style="background: rgba(255, 118, 117, 0.2); border: 1px solid #ff7675; padding: 15px; border-radius: 10px; margin-bottom: 2rem;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Biometric verification status bar -->
    <div id="bio-status-bar" class="glass-card" style="margin-bottom:1.5rem; display:flex; align-items:center; gap:1rem; padding:1rem 1.5rem; border-left:4px solid #e17055;">
        <i class="fas fa-shield-halved" style="font-size:1.5rem; color:#e17055;"></i>
        <div>
            <strong id="bio-status-text">🔒 Identity Not Verified</strong>
            <p style="font-size:0.8rem; opacity:0.7; margin:0;">Face verification required to cast your vote.</p>
        </div>
        <button id="open-face-modal-btn" onclick="openFaceModal()" class="btn btn-primary" style="margin-left:auto; padding:0.6rem 1.2rem; font-size:0.85rem;">
            <i class="fas fa-camera"></i> Verify Now
        </button>
    </div>

    <h3 style="margin-bottom: 1.5rem;" data-i18n="vote_select">Select your candidate:</h3>

    <form id="vote-form" action="vote.php?id=<?php echo $election_id; ?>" method="POST" onsubmit="return submitVote(event)">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
            <?php foreach ($candidates as $candidate): ?>
                <div class="glass-card candidate-card" style="text-align: center; transition: 0.3s; position: relative;">
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.1); margin: 0 auto 1rem; border: 2px solid var(--accent-color); padding: 5px; overflow: hidden;">
                        <?php if($candidate['photo_url']): ?>
                            <img src="<?php echo htmlspecialchars($candidate['photo_url']); ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        <?php else: ?>
                            <i class="fas fa-user" style="font-size:4rem; color:var(--accent-color); margin-top:20px;"></i>
                        <?php endif; ?>
                    </div>
                    <h4 style="font-size: 1.2rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($candidate['name']); ?></h4>
                    <p style="color: var(--accent-color); font-weight: 600; margin-bottom: 1rem;"><?php echo htmlspecialchars($candidate['party']); ?></p>
                    <p style="font-size: 0.85rem; opacity: 0.7; margin-bottom: 1.5rem; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">
                        <?php echo htmlspecialchars($candidate['bio']); ?>
                    </p>
                    <label class="vote-checkbox" style="display:block; cursor:pointer;">
                        <input type="radio" name="candidate_id" value="<?php echo $candidate['id']; ?>" required style="display:none;">
                        <div class="vote-indicator" style="background:rgba(255,255,255,0.1); border:2px solid var(--glass-border); padding:0.8rem; border-radius:10px; font-weight:600; transition:0.3s;" data-i18n="vote_select_candidate">Select Candidate</div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 3rem; text-align: center;">
            <button type="submit" id="vote-submit-btn" class="btn btn-primary" style="padding:1.2rem 3rem; font-size:1.1rem; box-shadow:0 10px 25px rgba(74,144,226,0.4);" disabled>
                <i class="fas fa-paper-plane" style="margin-right:10px;"></i>
                <span data-i18n="vote_cast">Cast Your Vote Securely</span>
            </button>
            <p style="margin-top:1rem; opacity:0.6; font-size:0.9rem;" data-i18n="vote_warning">
                <i class="fas fa-info-circle"></i> Once cast, your vote cannot be changed.
            </p>
        </div>
    </form>
</div>

<style>
input[type="radio"]:checked + .vote-indicator {
    background: var(--accent-color) !important;
    color: white !important;
    border-color: var(--accent-color) !important;
    box-shadow: 0 0 15px rgba(0,206,201,0.5);
}
.candidate-card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.2); }
@media (max-width: 600px) { .candidate-card:hover { transform: none; } }
</style>

<!-- face-api.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script src="assets/js/face-verify.js"></script>
<script>
// Profile picture URL for reference comparison
const PROFILE_PIC_URL = '<?php echo $profile_pic_url; ?>';
let faceVerified = false;

function openFaceModal() {
    document.getElementById('face-modal').classList.add('open');
}
function closeFaceModal() {
    FaceVerify.stopCamera();
    document.getElementById('face-modal').classList.remove('open');
}

function setFaceStatus(msg, type) {
    const el = document.getElementById('face-status');
    el.textContent = msg;
    el.className = '';
    if (type === 'success') el.classList.add('face-status-success');
    else if (type === 'error')   el.classList.add('face-status-error');
    else                         el.classList.add('face-status-info');
}

async function startFaceScan() {
    const btn = document.getElementById('start-scan-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Scanning...';

    if (!PROFILE_PIC_URL) {
        setFaceStatus('No profile picture found. Please contact support.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-camera"></i> Start Face Scan';
        return;
    }

    const video  = document.getElementById('face-video');
    const canvas = document.getElementById('face-canvas');

    // MOBILE FIX: Request Camera IMMEDIATELY after user click to prevent gesture timeout
    try {
        if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
            setFaceStatus('HTTPS Required! Please use https://', 'error');
            btn.disabled = false; return;
        }
        FaceVerify.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        video.srcObject = FaceVerify.stream;
        await video.play();
    } catch(e) {
        setFaceStatus('Camera access denied. Please allow permissions.', 'error');
        btn.disabled = false; return;
    }

    // Load models
    setFaceStatus('Loading AI models…', 'info');
    const modelsOk = await FaceVerify.loadModels();
    if (!modelsOk) {
        setFaceStatus('Failed to load models. Check your connection.', 'error');
        btn.disabled = false; return;
    }

    // Load reference
    setFaceStatus('Analysing your profile picture…', 'info');
    const refResult = await FaceVerify.loadReferenceImage(PROFILE_PIC_URL);
    if (!refResult.success) {
        setFaceStatus(refResult.message, 'error');
        btn.disabled = false; return;
    }

    // Run live scan
    const match  = await FaceVerify.startVerification(video, canvas, setFaceStatus);

    if (match) {
        faceVerified = true;
        // Update status bar
        document.getElementById('bio-status-bar').style.borderLeftColor = '#55efc4';
        document.getElementById('bio-status-text').textContent = '✅ Identity Verified — You may now vote';
        document.getElementById('bio-status-bar').querySelector('p').textContent = 'Face matched to your registered profile.';
        document.getElementById('open-face-modal-btn').style.display = 'none';
        document.getElementById('vote-submit-btn').disabled = false;
        // Auto-close modal after 1.5s
        setTimeout(() => closeFaceModal(), 1500);
    } else {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-redo"></i> Retry Scan';
    }
}

function submitVote(e) {
    if (!faceVerified) {
        e.preventDefault();
        openFaceModal();
        return false;
    }
    return true;
}

// Also intercept any radio selection — ensure modal is shown if not verified
document.querySelectorAll('input[type="radio"]').forEach(r => {
    r.addEventListener('change', () => {
        if (!faceVerified) {
            setTimeout(() => openFaceModal(), 200);
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
