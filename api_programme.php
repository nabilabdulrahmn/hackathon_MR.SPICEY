<?php
include 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit;
    }
    
    $programme_name = isset($data['programme_name']) ? mysqli_real_escape_string($conn, $data['programme_name']) : '';
    $location = isset($data['location']) ? mysqli_real_escape_string($conn, $data['location']) : '';
    $start_date = isset($data['start_date']) ? mysqli_real_escape_string($conn, $data['start_date']) : '';
    $end_date = isset($data['end_date']) ? mysqli_real_escape_string($conn, $data['end_date']) : '';
    $status = isset($data['status']) ? mysqli_real_escape_string($conn, $data['status']) : 'pending';
    
    if (isset($data['id']) && !empty($data['id'])) {
        $id = (int)$data['id'];
        $updates = [];
        if (!empty($programme_name)) $updates[] = "programme_name='$programme_name'";
        if (!empty($location)) $updates[] = "location='$location'";
        if (!empty($start_date)) $updates[] = "start_date='$start_date'";
        if (!empty($end_date)) $updates[] = "end_date='$end_date'";
        if (!empty($status)) $updates[] = "status='$status'";

        if (empty($updates)) {
            echo json_encode(['status' => 'error', 'message' => 'No fields to update']);
            exit;
        }

        $query = "UPDATE programmes SET " . implode(', ', $updates) . " WHERE id=$id";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Programme updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
    } else {
        $template_id = (int)(isset($data['template_id']) ? $data['template_id'] : 1);
        if (empty($programme_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Programme name is required']);
            exit;
        }
        $query = "INSERT INTO programmes (programme_name, template_id, location, start_date, end_date, status) 
                  VALUES ('$programme_name', $template_id, '$location', " . ($start_date ? "'$start_date'" : "NULL") . ", " . ($end_date ? "'$end_date'" : "NULL") . ", '$status')";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Programme created successfully', 'id' => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
        }
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $query = "SELECT p.*, t.template_name, t.rules_json 
                  FROM programmes p 
                  LEFT JOIN linkage_templates t ON p.template_id = t.id 
                  WHERE p.id=$id";
        $result = mysqli_query($conn, $query);
        $data = mysqli_fetch_assoc($result);
        if ($data) {
           echo json_encode(['status' => 'success', 'data' => $data]);
        } else {
           echo json_encode(['status' => 'error', 'message' => 'Programme not found']);
        }
    } else {
        $query = "SELECT p.*, t.template_name FROM programmes p LEFT JOIN linkage_templates t ON p.template_id = t.id ORDER BY p.id DESC";
        $result = mysqli_query($conn, $query);
        $programmes = [];
        while($row = mysqli_fetch_assoc($result)) {
            $programmes[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $programmes]);
    }
    exit;
}
?>
