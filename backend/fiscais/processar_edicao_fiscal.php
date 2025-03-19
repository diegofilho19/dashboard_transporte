<?php
session_start();
require '../sistemas/config.php';
header('Content-Type: application/json');

// Obtém os dados do fiscal da requisição POST
$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? null;
$cnh = $_POST['cnh'] ?? null;
$nome_carro = $_POST['nome_carro'] ?? null;
$placa = $_POST['placa'] ?? null;
$destino = $_POST['destino'] ?? null;
$numero = $_POST['numero'] ?? null;

// Verificar se todos os campos necessários foram fornecidos
if (!$id || !$nome || !$cnh || !$nome_carro || !$placa || !$destino) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Todos os campos são obrigatórios.'
    ]);
    exit;
}

try {
    // Atualiza os dados do fiscal
    $update_sql = "UPDATE fiscais SET nome=?, cnh=?, nome_carro=?, placa=?, destino=?";
    $params = [$nome, $cnh, $nome_carro, $placa, $destino];
    $types = "sssss";
    
    // Adiciona o número de telefone se fornecido
    if ($numero) {
        $update_sql .= ", numero=?";
        $params[] = $numero;
        $types .= "s";
    }
    
    $update_sql .= " WHERE id=?";
    $params[] = $id;
    $types .= "i";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param($types, ...$params);
    
    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Motorista atualizado com sucesso!";
        echo json_encode([
            'status' => 'success',
            'message' => 'Motorista atualizado com sucesso!'
        ]);
    } else {
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erro ao atualizar Motorista.";
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao atualizar motorista: ' . $e->getMessage()
    ]);
}
?>