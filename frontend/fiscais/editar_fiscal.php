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
    <style>
        /* Estilos adicionais para as mensagens */
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Editar Fiscal</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']); 
            ?>
        </div>
    <?php endif; ?>

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

    <script>
        $(document).ready(function() {
            $("#formEditAluno").submit(function(event) {
                event.preventDefault(); // Impede o envio padrão do formulário

                var formData = $(this).serialize(); // Serializa os dados do formulário

                $.ajax({
                    url: "../../backend/fiscais/processar_edicao_fiscal.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            const messageElement = document.getElementById("message");
                            messageElement.innerHTML = "Fiscal editado com sucesso!";
                            messageElement.className = "success-message";
                            messageElement.style.display = "block";

                            // Opcional: redirecionar após alguns segundos
                            setTimeout(function() {
                                window.location.href = "fiscais.php";
                            }, 2000);
                        } else {
                            const messageElement = document.getElementById("message");
                            messageElement.innerHTML = response.message;
                            messageElement.className = "alert error";
                            messageElement.style.display = "block";
                        }
                        window.scrollTo(0, 0);

                    },
                    error: function(xhr, status, error) {
                        const messageElement = document.getElementById("message");
                        messageElement.innerHTML = "Erro ao processar a requisição. Por favor, tente novamente.";
                        messageElement.className = "alert error";
                        messageElement.style.display = "block";
                        console.error("Erro AJAX:", xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>

</html>