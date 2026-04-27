<?php
include '../includes/config.php';

header('Content-Type: application/json');

$response = [
    'status' => 'success',
    'elections' => [],
    'summary' => ''
];

try {
    // Get active elections and their candidates
    $stmt = $conn->query("SELECT id, title, description, status FROM elections WHERE status IN ('active', 'upcoming')");
    $elections = $stmt->fetchAll();

    foreach ($elections as $election) {
        $candidate_stmt = $conn->prepare("SELECT name, party FROM candidates WHERE election_id = ?");
        $candidate_stmt->execute([$election['id']]);
        $candidates = $candidate_stmt->fetchAll();
        
        $response['elections'][] = [
            'title' => $election['title'],
            'status' => $election['status'],
            'candidates' => $candidates
        ];
    }

    // Create a text summary
    $summary = "We currently have " . count($elections) . " active/upcoming elections. ";
    foreach ($response['elections'] as $e) {
        $summary .= "The '" . $e['title'] . "' is " . $e['status'] . " with " . count($e['candidates']) . " candidates. ";
    }
    $response['summary'] = $summary;

} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
