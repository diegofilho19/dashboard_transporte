<?php
session_start();
require '../../backend/sistemas/config.php';

// Obtém os dados do fiscal da requisição POST
$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? null;
$cnh = $_POST['cnh'] ?? null;
$nome_carro = $_POST['nome_carro'] ?? null;
$placa = $_POST['placa'] ?? null;
$destino = $_POST['destino'] ?? null;

if ($id) {
    // Atualiza os dados do fiscal
    $update_sql = "UPDATE fiscais SET nome=?, cnh=?, nome_carro=?, placa=?, destino=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssi", $nome, $cnh, $nome_carro, $placa, $destino, $id);

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Fiscal atualizado com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao atualizar fiscal.";
    }
} else {
    $_SESSION['error'] = "ID do fiscal não fornecido.";
}

// Redireciona de volta para a página de fiscais
header("Location: http://localhost/sistema_dashboard/frontend/fiscais/fiscais.php");
exit;
?>