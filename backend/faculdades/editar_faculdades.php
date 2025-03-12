<?php
session_start();
require '../sistemas/config.php';
// Obtém os dados da faculdade e da requisição POST
$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? null;
$sigla = $_POST['sigla'] ?? null;
$cidade = $_POST['cidade'] ?? null;
$tipo = $_POST['tipo'] ?? null;

if ($id) {
    // Atualiza os dados do fiscal
    $update_sql = "UPDATE faculdades SET nome=?, sigla=?, cidade=?, tipo=?  WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $nome, $sigla, $cidade, $tipo, $id);
    
    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Faculdade atualizada com sucesso!";
        // Redireciona de volta para a página de edição do fiscal
        header("Location: ../../frontend/faculdades/editar_faculdade.php?id=" . $id);
        exit;
    } else {
        $_SESSION['error'] = "Erro ao atualizar faculdade.";
        header("Location: ../../frontend/faculdades/editar_faculdade.php?id=" . $id);
        exit;
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID não fornecido.'
    ]);
}
?>