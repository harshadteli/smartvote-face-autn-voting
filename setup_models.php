<?php
/**
 * setup_models.php
 * Run this ONCE from your browser to download face-api.js model files.
 * Visit: http://localhost/evoting1.2/setup_models.php
 *
 * Models are fetched from the official face-api.js GitHub releases
 * and saved to /assets/models/ which face-verify.js reads.
 */

$modelsDir = __DIR__ . '/assets/models/';
if (!is_dir($modelsDir)) mkdir($modelsDir, 0755, true);

// Models to download from the official face-api.js GitHub CDN
$baseUrl = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';

$files = [
    // SSD MobileNet v1
    'ssd_mobilenetv1_model-weights_manifest.json',
    'ssd_mobilenetv1_model-shard1',
    'ssd_mobilenetv1_model-shard2',
    // Face Landmark 68 Net
    'face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    // Face Recognition Net
    'face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1',
    'face_recognition_model-shard2',
];

$results = [];
foreach ($files as $file) {
    $dest = $modelsDir . $file;
    if (file_exists($dest)) {
        $results[] = ['file' => $file, 'status' => 'already_exists', 'size' => filesize($dest)];
        continue;
    }
    $url  = $baseUrl . $file;
    $data = @file_get_contents($url);
    if ($data !== false) {
        file_put_contents($dest, $data);
        $results[] = ['file' => $file, 'status' => 'downloaded', 'size' => strlen($data)];
    } else {
        $results[] = ['file' => $file, 'status' => 'failed', 'size' => 0];
    }
}

// Check if allow_url_fopen is enabled
$fopen = ini_get('allow_url_fopen');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Face-API Model Installer</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<main class="container" style="padding:3rem 0;">
    <div class="glass-card" style="max-width:700px; margin:auto;">
        <h2 style="text-align:center; margin-bottom:0.5rem;">
            <i class="fas fa-robot" style="color:var(--accent-color);"></i>
            Face-API.js Model Installer
        </h2>
        <p style="text-align:center; opacity:0.7; margin-bottom:2rem; font-size:0.9rem;">
            Downloads AI model weights to <code>/assets/models/</code> for biometric face verification.
        </p>

        <?php if (!$fopen): ?>
        <div style="background:rgba(255,118,117,0.2); border:1px solid #ff7675; padding:1rem; border-radius:10px; margin-bottom:1.5rem;">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Warning:</strong> <code>allow_url_fopen</code> is disabled in your php.ini.
            Enable it or download models manually and place them in <code>/assets/models/</code>.
        </div>
        <?php endif; ?>

        <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
            <thead>
                <tr style="border-bottom:1px solid var(--glass-border);">
                    <th style="padding:10px; text-align:left;">File</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px; text-align:right;">Size</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $r): ?>
                <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                    <td style="padding:10px; font-size:0.8rem; font-family:monospace;"><?php echo htmlspecialchars($r['file']); ?></td>
                    <td style="padding:10px; text-align:center;">
                        <?php if ($r['status'] === 'downloaded'): ?>
                            <span style="color:#55efc4;">✅ Downloaded</span>
                        <?php elseif ($r['status'] === 'already_exists'): ?>
                            <span style="color:var(--accent-color);">✔ Exists</span>
                        <?php else: ?>
                            <span style="color:#ff7675;">❌ Failed</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px; text-align:right; opacity:0.6; font-size:0.8rem;">
                        <?php echo $r['size'] > 0 ? round($r['size']/1024/1024,2).' MB' : '—'; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        $all_ok = !in_array('failed', array_column($results, 'status'));
        ?>
        <div style="margin-top:2rem; text-align:center;">
            <?php if ($all_ok): ?>
                <div style="background:rgba(85,239,196,0.2); border:1px solid #55efc4; padding:1.2rem; border-radius:12px;">
                    <i class="fas fa-check-circle" style="color:#55efc4; font-size:1.5rem;"></i>
                    <p style="margin:0.5rem 0 0; color:#55efc4; font-weight:600;">All models ready! Biometric face verification is now active.</p>
                </div>
                <a href="index.php" class="btn btn-primary" style="margin-top:1.5rem; padding:0.8rem 2rem;">
                    <i class="fas fa-home"></i> Go to Homepage
                </a>
            <?php else: ?>
                <p style="color:#ff7675; margin-bottom:1rem;">Some downloads failed. Check your internet connection and try again.</p>
                <a href="setup_models.php" class="btn btn-primary" style="padding:0.8rem 2rem;">
                    <i class="fas fa-redo"></i> Retry Download
                </a>
            <?php endif; ?>
        </div>

        <div style="margin-top:2rem; padding:1rem; background:rgba(0,0,0,0.2); border-radius:10px; font-size:0.8rem; opacity:0.7;">
            <strong>Manual alternative:</strong> Download the
            <a href="https://github.com/justadudewhohacks/face-api.js/tree/master/weights" target="_blank" style="color:var(--accent-color);">
                model weight files
            </a>
            from GitHub and place them in <code>/evoting1.2/assets/models/</code>.
        </div>
    </div>
</main>
</body>
</html>
