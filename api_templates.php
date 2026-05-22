<?php
include 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
        exit;
    }

    $template_name = mysqli_real_escape_string($conn, $data['template_name'] ?? '');
    $description = mysqli_real_escape_string($conn, $data['description'] ?? '');
    $relationship_type = mysqli_real_escape_string($conn, $data['relationship_type'] ?? 'mentor_startup');
    
    $id = mysqli_real_escape_string($conn, $data['id'] ?? '');
    
    // Remove explicit columns from rules_json
    unset($data['id']);
    unset($data['template_name']);
    unset($data['description']);
    unset($data['relationship_type']);
    
    $rules_json = mysqli_real_escape_string($conn, json_encode($data));
    
    if (!empty($id)) {
        $query = "UPDATE linkage_templates 
                  SET template_name='$template_name', description='$description', relationship_type='$relationship_type', rules_json='$rules_json' 
                  WHERE id='$id'";
    } else {
        $query = "INSERT INTO linkage_templates (template_name, description, relationship_type, rules_json) 
                  VALUES ('$template_name', '$description', '$relationship_type', '$rules_json')";
    }
              
    if (mysqli_query($conn, $query)) {
        $response_id = !empty($id) ? $id : mysqli_insert_id($conn);
        echo json_encode(['status' => 'success', 'message' => 'Template saved successfully', 'id' => $response_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM linkage_templates ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $templates = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $rules = json_decode($row['rules_json'], true);
        $row['duration_months'] = $rules['duration_months'] ?? 6;
        $row['industry_focus'] = $rules['industry_focus'] ?? ['All sectors'];
        $templates[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $templates]);
    exit;
}
?>
