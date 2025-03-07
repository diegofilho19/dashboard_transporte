<?php
session_start();
require '../../backend/sistemas/config.php';

// Consulta SQL (mantida)
$sql = "SELECT
    a.nome_completo,
    f.nome AS nome_faculdade,  -- Nome da faculdade
    a.cpf,
    a.numero_tel,
    a.matricula,
    f.cidade,  -- Cidade da faculdade
    a.data_insercao,
    a.status,
    a.foto,
    af.id_fiscal,
    fc.nome AS nome_motorista,  -- Nome do motorista
    fc.nome_carro,  -- Nome do carro
    fc.placa  -- Placa do carro
FROM
    alunos a
LEFT JOIN
    alunos_fiscais af ON a.id = af.id_aluno
LEFT JOIN
    faculdades f ON a.id_faculdade = f.id  -- Junção com a tabela de faculdades
LEFT JOIN
    fiscais fc ON af.id_fiscal = fc.id;";  // Junção com a tabela de fiscais

$result = $conn->query($sql);

// Função para formatar a data (melhor prática)
function formatarData($data)
{
    if ($data) { // Verifica se a data não é nula
        return date('d/m/Y', strtotime($data));
    } else {
        return ""; // Ou outra mensagem que indique data não disponível
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Alunos Cadastrados</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<html>
<div class="sidebar">
    <div class="logo">
        <img src="../imgs/base_icon_transparent_background.png" alt="Logo - IVF" width="50" height="100%">
        <h3>DASHBOARD</h3>
    </div>
    <div class="menu-item">
        <a href="http://localhost/sistema_dashboard/frontend/admin/dashboard.php"><span>👥</span>
            <span>Alunos</span></a>
    </div>
    <div class="menu-item">
        <a href="http://localhost/sistema_dashboard/frontend/fiscais/fiscais.php"><span>📋</span>
            <span>Fiscais</span></a>
    </div>
    <div class="menu-item" onclick="logout()">
        <span>🚪</span>
        <span>Sair</span>
    </div>
</div>

<body class="main-content">
    <div class="header">
        <h1>ALUNOS CADASTRADOS</h1>
        <div class="controls">
            <input type="text" class="search-bar" placeholder="Search...">
            <select class="ordenar-select" aria-label="Ordenar alunos">
                <option>Mais recentes</option>
                <option>Mais antigos</option>
                <option>A-Z</option>
                <option>Z-A</option>
            </select>
        </div>
    </div>

    <table id="alunosTable">
        <thead>
            <tr>
                <th onclick="ordenarPor('nome')">Nome do Aluno</th>
                <th onclick="ordenarPor('faculdade')">Faculdade</th>
                <th onclick="ordenarPor('cpf')">CPF</th>
                <th onclick="ordenarPor('matricula')">Matrícula</th>
                <th onclick="ordenarPor('cidade')">Cidade (Faculdade)</th>
                <th onclick="ordenarPor('data')">Data de Inserção</th>
                <th onclick="ordenarPor('status')">Status</th>
                <th onclick="ordenarPor('motorista')">Motorista</th> <!-- Novo cabeçalho para Motorista -->
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $status_classe = ($row["status"] == "Ativo") ? "status-ativo" : "status-inativo";
                    echo "<tr>";
                    echo "<td>" . $row["nome_completo"] . "</td>";
                    echo "<td>" . $row["nome_faculdade"] . "</td>";
                    echo "<td>" . $row["cpf"] . "</td>";
                    echo "<td>" . $row["matricula"] . "</td>";
                    echo "<td>" . $row["cidade"] . "</td>";
                    echo "<td>" . formatarData($row["data_insercao"]) . "</td>";
                    echo "<td class='" . $status_classe . "'>" . $row["status"] . "</td>";

                    // Exibir o motorista com o formato desejado
                    $motorista_info = isset($row['nome_motorista']) ? $row['nome_motorista'] : 'Não disponível';
                    $carro_info = isset($row['nome_carro']) ? $row['nome_carro'] . ' - ' . $row['placa'] : 'Não disponível';
                    echo "<td>" . $motorista_info . ", " . $carro_info . "</td>";

                    echo "<td>
                        <button onclick='abrirModal(\"" . $row["nome_completo"] . "\", \"" . $row["cpf"] . "\", \"" . $row['numero_tel'] . "\", \"" . $row["matricula"] . "\", \"" . $row["status"] . "\", \"" . $row["nome_faculdade"] . 
                        "\", \"" . $row["cidade"] . "\", \"" . $row["foto"] . "\", \"" . $row['nome_motorista'] . "\", \"" . $row['nome_carro'] . "\", \"" . $row['placa'] . "\")' class='visualizar btn-cinza'>Visualizar</button>
                        <a href='../alunos/editar_alunos.php?cpf=" . $row["cpf"] . "' class='edit-button'>Editar</a>
                        <form method='post' action='../../backend/alunos/processar_exclusao_aluno.php' style='display:inline;' class='excluir-aluno-form'>
                            <input type='hidden' name='cpf' value='" . $row["cpf"] . "'>
                            <button type='button' class='btn-excluir' onclick='excluirAluno(\"" . $row["cpf"] . "\")'>Excluir</button>
                        </form>
                    </td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Nenhum aluno cadastrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="modal-backdrop" id="modalCarteira" style="display: none;">
        <div class="carteira-estudante">
            <button class="fechar-modal" onclick="fecharModal()">×</button>
            <h2 class="titulo-carteira">CARTEIRA DO ESTUDANTE</h2>
            <div class="foto-perfil">
                <img src="" alt="Foto de perfil" width="100%" height="100%" style="border-radius: 50%;">
            </div>
            <div class="info-aluno">
                <p id="nomeCarteira">Nome: </p>
                <p id="cpfCarteira">CPF: </p>
                <p id="numeroCarteira">Número: </p>
                <p id="matriculaCarteira">Matrícula: </p>
                <p id="faculdadeCarteira">Faculdade: </p>
                <p id="cidadeCarteira">Cidade: </p>
                <p id="statusCarteira">Status: </p>
                <p id="motoristaCarteira">Motorista: </p> <!-- Novo campo para motorista -->
                <p id="carroCarteira">Carro: </p> <!-- Novo campo para carro e placa -->
            </div>
        </div>
    </div>

    <script>
        function abrirModal(nome, cpf, numero_tel, matricula, status, faculdade, cidade, foto, motorista, carro, placa) {
            document.getElementById("nomeCarteira").textContent = "Nome: " + nome;
            document.getElementById("cpfCarteira").textContent = "CPF: " + cpf;
            document.getElementById("numeroCarteira").textContent = "Número: " + numero_tel;
            document.getElementById("matriculaCarteira").textContent = "Matrícula: " + matricula;
            document.getElementById("statusCarteira").textContent = "Status: " + status;
            document.getElementById("faculdadeCarteira").textContent = "Faculdade: " + faculdade;
            document.getElementById("cidadeCarteira").textContent = "Cidade: " + cidade;

            // Adicionando informações do motorista
            document.getElementById("motoristaCarteira").textContent = "Motorista: " + motorista;
            document.getElementById("carroCarteira").textContent = "Carro: " + carro + " - " + placa;

            const fotoPerfil = document.querySelector('.foto-perfil img');
            const nomeArquivo = foto.split('/').pop();
            const caminhoCorreto = "../../backend/alunos/uploads/" + nomeArquivo;

            if (nomeArquivo && nomeArquivo.trim() !== "") {
                fotoPerfil.setAttribute('src', caminhoCorreto);
                fotoPerfil.setAttribute('alt', `Foto de ${nome}`);
                fotoPerfil.onerror = function() {
                    this.setAttribute('src', '../imgs/default_profile.png');
                    this.setAttribute('alt', 'Foto não encontrada');
                };
            } else {
                fotoPerfil.setAttribute('src', '../imgs/default_profile.png');
                fotoPerfil.setAttribute('alt', 'Foto de perfil não disponível');
            }

            document.getElementById("modalCarteira").style.display = "block"; // Mostra o modal
        }

        function fecharModal() {
            document.getElementById("modalCarteira").style.display = "none";
        }

        function logout() {
            window.location.href = "http://localhost/sistema_dashboard/frontend/admin/login.php";
        }

        function excluirAluno(cpf) {
            if (confirm("Tem certeza que deseja excluir este aluno?")) {
                $.ajax({
                    url: '../../backend/alunos/excluir_aluno.php',
                    type: 'POST',
                    data: {
                        cpf: cpf
                    },
                    success: function(response) {
                        alert("Aluno excluído com sucesso!");
                        location.reload(); // Recarrega a página para atualizar a lista
                    },
                    error: function() {
                        alert("Erro ao excluir aluno.");
                    }
                });
            }
        }
    </script>

</body>

</html>

</html>