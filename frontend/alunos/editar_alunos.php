<?php
session_start();
require '../../backend/sistemas/config.php';

// Obtém o CPF do aluno da query string
$cpf = $_GET['cpf'] ?? null;

if ($cpf) {
    // Busca os dados do aluno no banco de dados
    $sql = "SELECT * FROM alunos WHERE cpf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
    } else {
        echo "Aluno não encontrado.";
        exit;
    }
} else {
    echo "CPF não fornecido.";
    exit;
}

// Busca os fiscais cadastrados
$fiscais_sql = "SELECT * FROM fiscais";
$fiscais_result = $conn->query($fiscais_sql);

// Consulta para obter o fiscal atual do aluno
$sql_fiscal_atual = "SELECT id_fiscal FROM alunos_fiscais WHERE id_aluno = ?";
$stmt_fiscal = $conn->prepare($sql_fiscal_atual);
$stmt_fiscal->bind_param("i", $aluno['id']);
$stmt_fiscal->execute();
$result_fiscal = $stmt_fiscal->get_result();
$fiscal_atual = $result_fiscal->fetch_assoc();
$id_fiscal_atual = $fiscal_atual['id_fiscal'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Aluno</title>
    <link rel="stylesheet" href="../css/editar_aluno.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        #message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: none;
            font-weight: 500;
        }
        
        #message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        #message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Editar Aluno</h2>
        <div id="message" class="alert"></div>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message"><?= $_SESSION['message'];
                                            unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form id="formEditAluno" method="post" action="../../backend/alunos/processar_edicao_aluno.php">
            Nome Completo: <input type="text" name="nome_completo" value="<?= htmlspecialchars($aluno['nome_completo']) ?>" required><br>
            CPF: <input type="text" name="cpf" value="<?= htmlspecialchars($aluno['cpf']) ?>" required readonly><br>
            Matrícula: <input type="text" name="matricula" value="<?= htmlspecialchars($aluno['matricula']) ?>" required><br>
            Número: <input type="text" name="numero_tel" value="<?= htmlspecialchars($aluno['numero_tel']) ?>" required><br>
            
            <!-- Adicionar campo de Status -->
            Status:
            <select name="status" required>
                <option value="ativo" <?= ($aluno['status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= ($aluno['status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>SS
            </select><br>

            <!-- Campo de seleção para fiscais -->
            Nome do Motorista:
            <select name="id_fiscal" class="selectFiscal" required>
                <option value="">Selecione um fiscal</option>
                <?php while ($fiscal = $fiscais_result->fetch_assoc()): ?>
                    <option value="<?= $fiscal['id'] ?>" <?= ($id_fiscal_atual == $fiscal['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fiscal['nome']) . ' - ' . htmlspecialchars($fiscal['nome_carro']) . ' - ' . htmlspecialchars($fiscal['placa']) ?>
                    </option>
                <?php endwhile; ?>
            </select><br>

            <input type="submit" value="Salvar">
            <a href="../admin/dashboard.php" class="cancel-button">Cancelar</a>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $("#formEditAluno").submit(function(event) {
            event.preventDefault(); // Impede o envio padrão do formulário

            var formData = $(this).serialize(); // Serializa os dados do formulário

            $.ajax({
                url: "../../backend/alunos/processar_edicao_aluno.php",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    const messageElement = document.getElementById("message");
                    
                    if (response.status === "success") {
                        messageElement.innerHTML = response.message;
                        messageElement.className = "alert success";
                    } else {
                        messageElement.innerHTML = response.message;
                        messageElement.className = "alert error";
                    }
                    
                    // Garantir que a mensagem seja visível
                    messageElement.style.display = "block";
                    
                    // Rolar para o topo para mostrar a mensagem
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