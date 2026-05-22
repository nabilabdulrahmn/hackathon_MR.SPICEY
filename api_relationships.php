<?php
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['action'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }

    $action = $data['action']; // 'approve', 'reject', 'conclude'
    $programme_id = (int)(isset($data['programme_id']) ? $data['programme_id'] : 1);
    $mentor_name = mysqli_real_escape_string($conn, isset($data['mentor_name']) ? $data['mentor_name'] : '');
    $startup_name = mysqli_real_escape_string($conn, isset($data['startup_name']) ? $data['startup_name'] : '');
    $score = (float)(isset($data['match_score']) ? $data['match_score'] : 0);
    
    if (empty($mentor_name) || empty($startup_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing entity names']);
        exit;
    }
    
    // Helper to gracefully get or create an entity by name
    function getOrCreateEntity($conn, $name, $type) {
        $q = "SELECT id FROM entities WHERE name = '$name' LIMIT 1";
        $res = mysqli_query($conn, $q);
        if ($row = mysqli_fetch_assoc($res)) {
            return $row['id'];
        } else {
            mysqli_query($conn, "INSERT INTO entities (name, entity_type) VALUES ('$name', '$type')");
            return mysqli_insert_id($conn);
        }
    }
    
    $mentor_id = getOrCreateEntity($conn, $mentor_name, 'mentor');
    $startup_id = getOrCreateEntity($conn, $startup_name, 'startup');
    
    $status = 'proposed';
    if ($action === 'approve') $status = 'active';
    if ($action === 'reject') $status = 'rejected';
    if ($action === 'conclude') $status = 'concluded';

    $query = "INSERT INTO relationships (programme_id, entity_a_id, entity_b_id, relationship_type, match_score, status, activated_at, concluded_at) 
              VALUES ($programme_id, $mentor_id, $startup_id, 'mentor_startup', $score, '$status', " . ($action==='approve' ? 'NOW()' : 'NULL') . ", " . ($action==='conclude' ? 'NOW()' : 'NULL') . ")
              ON DUPLICATE KEY UPDATE 
              status='$status', 
              activated_at=IF('$action'='approve', NOW(), activated_at),
              concluded_at=IF('$action'='conclude', NOW(), concluded_at)";
              
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Relationship updated']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $programme_id = (int)(isset($_GET['programme_id']) ? $_GET['programme_id'] : 1);
    $startup_name = isset($_GET['startup_name']) ? mysqli_real_escape_string($conn, $_GET['startup_name']) : '';
    $status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
    
    $where = "r.programme_id = $programme_id";
    if (!empty($startup_name)) {
        $where .= " AND e_startup.name = '$startup_name'";
    }
    if (!empty($status)) {
        $where .= " AND r.status = '$status'";
    }

    $query = "SELECT r.*, 
                     e_mentor.name AS mentor_name, e_mentor.expertise AS mentor_expertise, e_mentor.verified AS mentor_verified,
                     e_startup.name AS startup_name 
              FROM relationships r
              JOIN entities e_mentor ON r.entity_a_id = e_mentor.id
              JOIN entities e_startup ON r.entity_b_id = e_startup.id
              WHERE $where
              ORDER BY r.match_score DESC";
              
    $result = mysqli_query($conn, $query);
    $data = [];
    while($row = mysqli_fetch_assoc($result)) {
        $row['risk_flags'] = json_decode(isset($row['risk_flags_json']) ? $row['risk_flags_json'] : '[]', true) ?: [];
        $row['mentor_expertise'] = json_decode(isset($row['mentor_expertise']) ? $row['mentor_expertise'] : '[]', true) ?: [];
        $data[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}
?>
