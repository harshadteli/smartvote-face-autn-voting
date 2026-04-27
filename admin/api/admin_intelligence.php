<?php
/**
 * admin/api/admin_intelligence.php
 * Secure API endpoint — Admin chatbot live data engine.
 * Returns JSON reports on elections, voters, votes, candidates.
 */
include '../../includes/config.php';

// Security: admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');
$report = isset($_GET['report']) ? $_GET['report'] : 'summary';

// ── Helper ────────────────────────────────────────────────────
function q($conn, $sql, $params = []) {
    $s = $conn->prepare($sql);
    $s->execute($params);
    return $s;
}

switch ($report) {

    // ═══════════════════════════════════════════════════════════
    case 'summary':
    // ═══════════════════════════════════════════════════════════
        $total_voters    = q($conn,"SELECT COUNT(*) FROM users WHERE role='voter'")->fetchColumn();
        $active_voters   = q($conn,"SELECT COUNT(*) FROM users WHERE role='voter' AND status='active'")->fetchColumn();
        $pending_voters  = q($conn,"SELECT COUNT(*) FROM users WHERE role='voter' AND status='pending'")->fetchColumn();
        $blocked_voters  = q($conn,"SELECT COUNT(*) FROM users WHERE role='voter' AND status='blocked'")->fetchColumn();
        $total_elections = q($conn,"SELECT COUNT(*) FROM elections")->fetchColumn();
        $active_elec     = q($conn,"SELECT COUNT(*) FROM elections WHERE status='active'")->fetchColumn();
        $upcoming_elec   = q($conn,"SELECT COUNT(*) FROM elections WHERE status='upcoming'")->fetchColumn();
        $completed_elec  = q($conn,"SELECT COUNT(*) FROM elections WHERE status='completed'")->fetchColumn();
        $total_votes     = q($conn,"SELECT COUNT(*) FROM votes")->fetchColumn();
        $total_candidates= q($conn,"SELECT COUNT(*) FROM candidates")->fetchColumn();
        $voters_voted    = q($conn,"SELECT COUNT(DISTINCT user_id) FROM votes")->fetchColumn();
        $turnout_pct     = $total_voters > 0 ? round(($voters_voted / $total_voters) * 100, 1) : 0;

        // Today's stats
        $today_votes     = q($conn,"SELECT COUNT(*) FROM votes WHERE DATE(voted_at) = CURDATE()")->fetchColumn();
        $today_reg       = q($conn,"SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()")->fetchColumn();

        echo json_encode([
            'report' => 'summary',
            'data'   => [
                'voters'      => ['total'=>$total_voters,'active'=>$active_voters,'pending'=>$pending_voters,'blocked'=>$blocked_voters,'voted'=>$voters_voted,'turnout'=>$turnout_pct],
                'elections'   => ['total'=>$total_elections,'active'=>$active_elec,'upcoming'=>$upcoming_elec,'completed'=>$completed_elec],
                'votes'       => ['total'=>$total_votes,'today'=>$today_votes],
                'candidates'  => ['total'=>$total_candidates],
                'registrations'=> ['today'=>$today_reg],
            ]
        ]);
        break;

    // ═══════════════════════════════════════════════════════════
    case 'elections':
    // ═══════════════════════════════════════════════════════════
        $rows = q($conn,"
            SELECT e.*,
                COUNT(DISTINCT c.id) as candidate_count,
                COUNT(DISTINCT v.id) as vote_count
            FROM elections e
            LEFT JOIN candidates c ON c.election_id = e.id
            LEFT JOIN votes v ON v.election_id = e.id
            GROUP BY e.id
            ORDER BY e.created_at DESC
        ")->fetchAll();
        echo json_encode(['report'=>'elections','data'=>$rows]);
        break;

    // ═══════════════════════════════════════════════════════════
    case 'top_candidates':
    // ═══════════════════════════════════════════════════════════
        $rows = q($conn,"
            SELECT c.name, c.party, e.title as election,
                COUNT(v.id) as votes,
                ROUND(COUNT(v.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM votes WHERE election_id = c.election_id),0),1) as pct
            FROM candidates c
            LEFT JOIN votes v ON v.candidate_id = c.id
            LEFT JOIN elections e ON e.id = c.election_id
            GROUP BY c.id
            ORDER BY votes DESC
            LIMIT 10
        ")->fetchAll();
        echo json_encode(['report'=>'top_candidates','data'=>$rows]);
        break;

    // ═══════════════════════════════════════════════════════════
    case 'recent_votes':
    // ═══════════════════════════════════════════════════════════
        $rows = q($conn,"
            SELECT u.full_name, e.title as election,
                   DATE_FORMAT(v.voted_at,'%d %b %Y %H:%i') as voted_at
            FROM votes v
            JOIN users u ON u.id = v.user_id
            JOIN elections e ON e.id = v.election_id
            ORDER BY v.voted_at DESC
            LIMIT 8
        ")->fetchAll();
        echo json_encode(['report'=>'recent_votes','data'=>$rows]);
        break;

    // ═══════════════════════════════════════════════════════════
    case 'pending_voters':
    // ═══════════════════════════════════════════════════════════
        $rows = q($conn,"
            SELECT full_name, email,
                   DATE_FORMAT(created_at,'%d %b %Y') as registered
            FROM users WHERE role='voter' AND status='pending'
            ORDER BY created_at DESC LIMIT 10
        ")->fetchAll();
        $count = q($conn,"SELECT COUNT(*) FROM users WHERE role='voter' AND status='pending'")->fetchColumn();
        echo json_encode(['report'=>'pending_voters','data'=>$rows,'total'=>$count]);
        break;

    // ═══════════════════════════════════════════════════════════
    case 'voter_turnout':
    // ═══════════════════════════════════════════════════════════
        $rows = q($conn,"
            SELECT e.title,
                COUNT(DISTINCT v.user_id) as voted,
                (SELECT COUNT(*) FROM users WHERE role='voter') as total_voters,
                ROUND(COUNT(DISTINCT v.user_id)*100.0 / NULLIF((SELECT COUNT(*) FROM users WHERE role='voter'),0),1) as pct
            FROM elections e
            LEFT JOIN votes v ON v.election_id = e.id
            GROUP BY e.id
            ORDER BY e.created_at DESC
        ")->fetchAll();
        echo json_encode(['report'=>'voter_turnout','data'=>$rows]);
        break;

    // ═══════════════════════════════════════════════════════════
    case 'daily_trend':
    // ═══════════════════════════════════════════════════════════
        $rows = q($conn,"
            SELECT DATE_FORMAT(voted_at,'%d %b') as day, COUNT(*) as votes
            FROM votes
            WHERE voted_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
            GROUP BY DATE(voted_at)
            ORDER BY DATE(voted_at) ASC
        ")->fetchAll();
        echo json_encode(['report'=>'daily_trend','data'=>$rows]);
        break;

    // ═══════════════════════════════════════════════════════════
    case 'winner':
    // ═══════════════════════════════════════════════════════════
        $rows = q($conn,"
            SELECT e.title as election, e.status,
                   c.name as winner, c.party,
                   COUNT(v.id) as votes
            FROM elections e
            JOIN candidates c ON c.election_id = e.id
            LEFT JOIN votes v ON v.candidate_id = c.id
            GROUP BY e.id, c.id
            HAVING votes = (
                SELECT MAX(vc) FROM (
                    SELECT COUNT(id) as vc FROM votes WHERE election_id = e.id GROUP BY candidate_id
                ) t
            )
            ORDER BY e.created_at DESC
        ")->fetchAll();
        echo json_encode(['report'=>'winner','data'=>$rows]);
        break;

    default:
        echo json_encode(['error' => 'Unknown report type']);
}
?>
