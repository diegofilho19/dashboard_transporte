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
    $cnh = $_POST['cnh'];
    $nome_carro = $_POST['nome_carro'];
    $placa = $_POST['placa'];
    $destino = $_POST['destino'];
    $numero = $_POST['numero']; // Altere para $numero
    $turno = $_POST['turno'];

    // Insira os dados no banco de dados
    $sql = "INSERT INTO fiscais (nome, cnh, nome_carro, placa, destino, numero, turno) VALUES (?, ?, ?, ?, ?, ?, ?)"; // Altere para numero
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nome, $cnh, $nome_carro, $placa, $destino, $numero, $turno); // Altere para $numero

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Motorista cadastrado com sucesso!"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Erro ao cadastrar motorista!"]);
    }

    $stmt->close();
    $conn->close();
}