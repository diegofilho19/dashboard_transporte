<?php
session_start();
require_once __DIR__ . '/../sistemas/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Obtenha os dados do formulário
    $nome = $_POST['nome'];
    $sigla = $_POST['sigla'];
    $cidade = $_POST['cidade'];
    $tipo = $_POST['tipo'];

    // Insira os dados no banco de dados
    $sql = "INSERT INTO faculdades (nome, sigla, cidade, tipo) VALUES (?, ?, ?, ?)"; // Altere para numero
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $sigla, $cidade, $tipo); // Altere para $numero

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Faculdade cadastrada com sucesso!"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao cadastrar faculdade!"]);
    }

    $stmt->close();
    $conn->close();
}