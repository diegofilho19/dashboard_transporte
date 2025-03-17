<?php
// Arquivo: get_faculdades.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
// Include your database connection
require '../sistemas/config.php';

// Set the content type header BEFORE any output
header('Content-Type: application/json');

// Handle any errors
try {
    $sql = "SELECT id, sigla, cidade FROM faculdades";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Database query failed: " . $conn->error);
    }

    $faculdades = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $faculdades[] = $row;
        }
    }
    
    echo json_encode($faculdades);
} catch (Exception $e) {
    // Return a proper JSON error
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();