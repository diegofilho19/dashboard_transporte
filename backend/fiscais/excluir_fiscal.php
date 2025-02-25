<?php
session_start();
require '../sistemas/config.php';

// Obtém o ID do fiscal da requisição GET
$id = $_GET['id'] ?? null;

if ($id) {
    // Exclui o fiscal do banco de dados
    $delete_sql = "DELETE FROM fiscais WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        $_SESSION['message'] = "Fiscal excluído com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao excluir fiscal.";
    }
} else {
    $_SESSION['error'] = "ID do fiscal não fornecido.";
}

// Redireciona de volta para a página de fiscais
header("Location: http://localhost/sistema_dashboard/frontend/fiscais/fiscais.php");
exit;
?>