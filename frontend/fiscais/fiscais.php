<?php
session_start();
require '../../backend/sistemas/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Consulta SQL para obter todos os fiscais
$sql = "SELECT * FROM fiscais";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Dashboard - Motoristas</title>
    <link rel="stylesheet" href="../css/fiscais.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../imgs/base_icon_transparent_background.png" alt="Logo - IVF" width="50" height="100%">
            <h3>DASHBOARD</h3>
        </div>

        <div class="menu-item">
            <a href="http://localhost/sistema_dashboard/frontend/admin/dashboard.php"><span>👥</span>
                <span>Alunos</span>
        </div></a>

        <div class="menu-item">
            <a href="http://localhost/sistema_dashboard/frontend/faculdades/faculdades.php"><span>🏫</span>
                <span>Faculdades</span></a>
        </div>

        <div class="menu-item">
            <a href="http://localhost/sistema_dashboard/frontend/fiscais/fiscais.php" style="display: flex; align-items: center; text-decoration: none;" ><span><img style="display: flex; justify-content: center; align-items: center;" width="20" height="20" src="https://img.icons8.com/color/48/driver.png" alt="driver"/></span>
                <span style="padding-left: 5px;" > Motoristas</span>
        </div></a>

        <div class="menu-item" onclick="logout()">
            <span>🚪</span>
            <span>Sair</span>
        </div>
    </div>

    <body class="main-content">
        <div class="header">
            <h1>MOTORISTAS CADASTRADOS</h1>
            <div class="controls">
                <input type="text" class="search-bar" placeholder="Search...">
                <select class="ordenar-select" aria-label="Ordenar fiscais">
                    <option>Mais recentes</option>
                    <option>Mais antigos</option>
                    <option>A-Z</option>
                    <option>Z-A</option>
                </select>
            </div>
        </div>

        <div class="criar-fiscal">
            <a href="cadastrar_fiscal.php">Cadastrar novo Motorista</a>
        </div>

        <table id="fiscaisTable">
            <thead>
                <tr>
                    <th>Nome do Fiscal</th>
                    <th>CNH</th>
                    <th>Carro</th>
                    <th>Placa</th>
                    <th>Destino</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["nome"] . "</td>";
                        echo "<td>" . $row["cnh"] . "</td>";
                        echo "<td>" . $row["nome_carro"] . "</td>";
                        echo "<td>" . $row["placa"] . "</td>";
                        echo "<td>" . $row["destino"] . "</td>";
                        echo "<td>
                            <button onclick='abrirModalFiscal(\"" . htmlspecialchars($row["nome"]) . "\", \"" . htmlspecialchars($row["cnh"]) . "\", \"" . htmlspecialchars($row["nome_carro"]) . "\", \"" . htmlspecialchars($row["placa"]) . "\", \"" . htmlspecialchars($row["destino"]) . "\")' class='visualizar btn-cinza'>Visualizar</button>
                            <a href='editar_fiscal.php?id=" . $row["id"] . "' class='edit-button'>Editar</a>
                            <button class='btn-excluir' onclick='excluirFiscal(" . $row["id"] . ")'>Excluir</button>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Nenhum fiscal cadastrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="modal-backdrop" id="modalFiscal" style="display: none;">
            <div class="carteira-fiscal">
                <button class="fechar-modal" onclick="fecharModal()">×</button>
                <h2 class="titulo-carteira">DETALHES DO FISCAL</h2>
                <div class="info-fiscal">
                    <p id="nomeFiscal">Nome: </p>
                    <p id="cnhFiscal">CNH: </p>
                    <p id="carroFiscal">Carro: </p>
                    <p id="placaFiscal">Placa: </p>
                    <p id="destinoFiscal">Destino: </p>
                </div>
            </div>
        </div>

        <script>
            function excluirFiscal(id) {
                if (confirm("Tem certeza que deseja excluir este fiscal?")) {
                    $.ajax({
                        url: '../../backend/fiscais/excluir_fiscal.php',
                        type: 'GET',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            alert("Fiscal excluído com sucesso!");
                            location.reload(); // Recarrega a página para atualizar a lista
                        },
                        error: function() {
                            alert("Erro ao excluir fiscal.");
                        }
                    });
                }
            }

            function abrirModalFiscal(nome, cnh, nome_carro, placa, destino) {
                document.getElementById("nomeFiscal").textContent = "Nome: " + nome;
                document.getElementById("cnhFiscal").textContent = "CNH: " + cnh;
                document.getElementById("carroFiscal").textContent = "Carro: " + nome_carro;
                document.getElementById("placaFiscal").textContent = "Placa: " + placa;
                document.getElementById("destinoFiscal").textContent = "Destino: " + destino;

                document.getElementById("modalFiscal").style.display = "block"; // Mostra o modal
            }

            function fecharModal() {
                document.getElementById("modalFiscal").style.display = "none"; // Esconde o modal
            }

            function logout() {
                window.location.href = "http://localhost/sistema_dashboard/frontend/admin/login.php";
            }
        </script>

    </body>

</html>