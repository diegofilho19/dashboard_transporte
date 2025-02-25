<?php
header('Content-Type: application/json');
require '../sistemas/config.php';

$mensagem = ""; // Inicializa a variável para evitar erros

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); // Hash da senha

    // Verifica se o e-mail já existe
    $sql_check = "SELECT id FROM admins WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "E-mail já cadastrado!"]);
    } else {
        // Insere o novo admin
        $sql = "INSERT INTO admins (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nome, $email, $senha);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Admin cadastrado com sucesso!"]);
            exit;
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao cadastrar admin!"]);
        }
    }
    $stmt->close();
    $conn->close();
}
?>