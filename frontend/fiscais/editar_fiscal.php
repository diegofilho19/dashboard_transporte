<?php
session_start();
require '../../backend/sistemas/config.php';

// Obtém o ID do fiscal da query string
$id = $_GET['id'] ?? null;

if ($id) {
    // Busca os dados do fiscal no banco de dados
    $sql = "SELECT * FROM fiscais WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $fiscal = $result->fetch_assoc();
    } else {
        echo "Fiscal não encontrado.";
        exit;
    }
} else {
    echo "ID do fiscal não fornecido.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Fiscal</title>
    <link rel="stylesheet" href="../css/editar_fiscal.css">
</head>
<body>
    <div class="container">
        <h2>Editar Fiscal</h2>
        <form method="post" action="../../backend/fiscais/processar_edicao_fiscal.php">
            Nome: <input type="text" name="nome" value="<?= htmlspecialchars($fiscal['nome']) ?>" required><br>
            CNH: <input type="text" name="cnh" value="<?= htmlspecialchars($fiscal['cnh']) ?>" required><br>
            Carro: <input type="text" name="nome_carro" value="<?= htmlspecialchars($fiscal['nome_carro']) ?>" required><br>
            Placa: <input type="text" name="placa" value="<?= htmlspecialchars($fiscal['placa']) ?>" required><br>
            Destino: <input type="text" name="destino" value="<?= htmlspecialchars($fiscal['destino']) ?>" required><br>
            <input type="hidden" name="id" value="<?= htmlspecialchars($fiscal['id']) ?>">
            <input type="submit" value="Salvar">
            <a href="fiscais.php" class="cancel-button">Cancelar</a>
        </form>
    </div>
</body>
</html>