<?php
header("Content-Type: application/json");
session_start();
require './config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "SELECT id, nome, senha FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        // Verifica a senha
        if (password_verify($senha, $admin["senha"])) {
            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_nome"] = $admin["nome"];
            echo json_encode(["status" => "success", "message" => ""]);
            exit();
        } else {
            echo json_encode(["status" => "error", "message" => "Senha incorreta"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email incorreto"]);
    }
    $stmt->close();
    $conn->close(); 
}
?>
