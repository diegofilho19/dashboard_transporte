<?php
session_start();
require '../../backend/sistemas/config.php';
// Obtém o ID do faculdades da query string
$id = $_GET['id'] ?? null;
if ($id) {
    // Busca os dados do faculdades no banco de dados
    $sql = "SELECT * FROM faculdades WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $faculdades = $result->fetch_assoc();
    } else {
        echo "Faculdade não encontrada.";
        exit;
    }
} else {
    echo "ID do faculdade não fornecido.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Faculdade</title>
    <link rel="stylesheet" href="../css/editar_faculdade.css">
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
        <h2>Editar faculdades</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message">
            <?php 
                echo $_SESSION['message']; 
                unset($_SESSION['message']); 
            ?>
        </div>
    <?php endif; ?>

        <form id="formEditFacul" method="post" action="../../backend/faculdades/editar_faculdades.php">
            Nome: <input type="text" name="nome" value="<?= htmlspecialchars($faculdades['nome']) ?>" required><br>
            Sigla: <input type="text" name="sigla" value="<?= htmlspecialchars($faculdades['sigla']) ?>" required><br>
            Cidade: <input type="text" name="cidade" value="<?= htmlspecialchars($faculdades['cidade']) ?>" required><br>
            Tipo: <select name="tipo" id="tipo" value="<?= htmlspecialchars($faculdades['tipo']) ?>"required>
                <option value="">Selecione o tipo</option>
                <option value="Pública">Pública</option>
                <option value="Privada">Privada</option>
                <option value="Estadual">Estadual</option>
            </select><br>
            <input type="hidden" name="id" value="<?= htmlspecialchars($faculdades['id']) ?>">
            <input type="submit" value="SALVAR">
            <a href="faculdades.php" class="cancel-button">Cancelar</a>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#formEditFacul").submit(function(event) {
                event.preventDefault(); // Impede o envio padrão do formulário

                var formData = $(this).serialize(); // Serializa os dados do formulário

                $.ajax({
                    url: "../../backend/faculdades/editar_faculdades.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            const messageElement = document.getElementById("message");
                            messageElement.innerHTML = "Faculdade editada com sucesso!";
                            messageElement.className = "success-message";
                            messageElement.style.display = "block";

                            // Opcional: redirecionar após alguns segundos
                            setTimeout(function() {
                                window.location.href = "faculdades.php";
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