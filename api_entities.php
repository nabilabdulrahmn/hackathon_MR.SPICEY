<?php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity_type = strtolower(mysqli_real_escape_string($conn, $_POST['entity_type'] ?? ''));
    if ($entity_type == 'service provider') {
        $entity_type = 'service_provider';
    }
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $industry = mysqli_real_escape_string($conn, $_POST['industry'] ?? '');
    $location = mysqli_real_escape_string($conn, $_POST['location'] ?? '');
    $bio = mysqli_real_escape_string($conn, $_POST['bio'] ?? '');
    
    // Set some defaults for the missing fields from the form
    $stage = 'Unknown';
    $verified = 0;
    $profile_json = '{}';
    
    $query = "INSERT INTO entities (entity_type, name, email, phone, industry, location, bio, stage, verified, profile_json) 
              VALUES ('$entity_type', '$name', '$email', '$phone', '$industry', '$location', '$bio', '$stage', $verified, '$profile_json')";
              
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Entity added successfully', 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "
        SELECT e.*, 
               (SELECT COUNT(*) FROM relationships WHERE entity_a_id = e.id OR entity_b_id = e.id) as active_engagements,
               (SELECT AVG(ro.rating) FROM relationship_outcomes ro JOIN relationships r ON ro.relationship_id = r.id WHERE r.entity_a_id = e.id OR r.entity_b_id = e.id) as avg_rating
        FROM entities e 
        ORDER BY e.created_at DESC
    ";
    
    $result = mysqli_query($conn, $query);
    $entities = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Map entity_type to display names
        $type_map = [
            'startup' => 'Startup',
            'mentor' => 'Mentor',
            'partner' => 'Partner',
            'service_provider' => 'Service Provider'
        ];
        $row['display_type'] = $type_map[$row['entity_type']] ?? ucfirst($row['entity_type']);
        
        // Map type to avatar
        $avatar_map = [
            'startup' => '🌱',
            'mentor' => '👤',
            'partner' => '🤝',
            'service_provider' => '🛠️'
        ];
        $row['avatar_icon'] = $avatar_map[$row['entity_type']] ?? '🏢';
        
        $entities[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $entities]);
    exit;
}
?>
