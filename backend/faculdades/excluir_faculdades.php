<?php
session_start();
require '../sistemas/config.php';

// Obtém o ID do fiscal da requisição GET
$id = $_GET['id'] ?? null;

if ($id) {
    // Exclui o fiscal do banco de dados
    $delete_sql = "DELETE FROM faculdades WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        $_SESSION['message'] = "Faculdade excluída com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao excluir faculdade.";
    }
} else {
    $_SESSION['error'] = "ID da faculdade não fornecida.";
}

// Redireciona de volta para a página de fiscais
header("Location: http://localhost/sistema_dashboard/frontend/faculdades/faculdades.php");
exit;
?>