<?php
session_start();
require '../../backend/sistemas/config.php';

$cpf = $_POST['cpf'] ?? null;

if ($cpf) {
    // Exclui o aluno do banco de dados
    $delete_sql = "DELETE FROM alunos WHERE cpf = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $cpf);

    if ($delete_stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "CPF não fornecido."]);
}

