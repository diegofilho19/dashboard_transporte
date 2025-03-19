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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            width: 200px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }

        .sidebar .logo {
            text-align: center;
            color: white;
            margin-bottom: 20px;
        }

        .sidebar .menu-items {
            display: flex;
            flex-direction: column;
        }

        .sidebar .menu-item {
            display: flex;
            align-items: center;
            padding: 10px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .sidebar .menu-item:hover {
            background-color: #495057;
        }

        .sidebar .menu-item a {
            color: white;
            text-decoration: none;
        }

        .sidebar .menu-item i {
            font-size: 1.2rem;
            color: white;
        }

        .main-content {
            margin-left: 200px;
            padding: 20px;
        }

        .header {
            margin-bottom: 20px;
        }

        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="logo text-center mb-4">
            <img src="../imgs/white_icon_transparent_background.png" alt="Logo - IVF" width="50">
            <h3 class="mt-2">DASHBOARD</h3>
        </div>
        <div class="menu-items">
            <div class="menu-item d-flex align-items-center p-3">
                <i class="bi bi-people me-3"></i>
                <a href="http://localhost/sistema_dashboard/frontend/admin/dashboard.php" class="text-white text-decoration-none">Alunos</a>
            </div>
            <div class="menu-item d-flex align-items-center p-3">
                <i class="bi bi-building me-3"></i>
                <a href="http://localhost/sistema_dashboard/frontend/faculdades/faculdades.php" class="text-white text-decoration-none">Faculdades</a>
            </div>
            <div class="menu-item d-flex align-items-center p-3">
                <i class="bi bi-car-front me-3"></i>
                <a href="http://localhost/sistema_dashboard/frontend/fiscais/fiscais.php" class="text-white text-decoration-none">Motoristas</a>
            </div>
            <div class="menu-item d-flex align-items-center p-3" onclick="logout()">
                <i class="bi bi-box-arrow-right me-3"></i>
                <span class="text-white">Sair</span>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>MOTORISTAS CADASTRADOS</h1>
            <div class="controls d-flex gap-2">
                <input type="text" class="form-control search-bar" placeholder="Pesquisar...">
                <select class="form-select ordenar-select" aria-label="Ordenar fiscais">
                    <option>Mais recentes</option>
                    <option>Mais antigos</option>
                    <option>A-Z</option>
                    <option>Z-A</option>
                </select>
            </div>
        </div>

        <div class="criar-fiscal mb-3">
            <!-- Botao do modal de cadastrado do motorista -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCadastrarFiscal">
                Cadastrar novo Motorista
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalCadastrarFiscal" tabindex="-1" aria-labelledby="modalCadastrarFiscalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCadastrarFiscalLabel">Cadastrar Novo Motorista</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="message" class="alert d-none"></div> <!-- Div para mensagens -->
                        <form method="post" id="formFiscal">
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome:</label>
                                <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome completo" required>
                            </div>
                            <div class="mb-3">
                                <label for="cnh" class="form-label">CNH:</label>
                                <input type="text" class="form-control" name="cnh" id="cnh" placeholder="Número da CNH" required>
                            </div>
                            <div class="mb-3">
                                <label for="nome_carro" class="form-label">Veículo:</label>
                                <input type="text" class="form-control" name="nome_carro" id="nome_carro" placeholder="Modelo do veículo" required>
                            </div>
                            <div class="mb-3">
                                <label for="placa" class="form-label">Placa:</label>
                                <input type="text" class="form-control" name="placa" id="placa" placeholder="Placa do veículo" required>
                            </div>
                            <div class="mb-3">
                                <label for="destino" class="form-label">Destino:</label>
                                <input type="text" class="form-control" name="destino" id="destino" placeholder="Destino" required>
                            </div>
                            <div class="mb-3">
                                <label for="numero" class="form-label">Telefone:</label>
                                <input type="tel" class="form-control" name="numero" id="numero" placeholder="Número de telefone" required>
                            </div>
                            <div class="mb-3">
                                <label for="turno" class="form-label">Turno:</label>
                                <select name="turno" id="turno" class="form-select" required>
                                    <option value="">Selecione um turno</option>
                                    <option value="Matutino">Matutino</option>
                                    <option value="Vespertino">Vespertino</option>
                                    <option value="Noturno">Noturno</option>
                                    <option value="Integral">Integral</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">Registrar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="fiscaisTable" class="table table-striped">
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
                            <button onclick='abrirModalFiscal(\"" . htmlspecialchars($row["nome"]) . "\", \"" . htmlspecialchars($row["cnh"]) . "\", \"" . htmlspecialchars($row["nome_carro"]) . "\", \"" . htmlspecialchars($row["placa"]) . "\", \"" . htmlspecialchars($row["destino"]) . "\", \"" . htmlspecialchars($row["numero"]) . "\", \"" . $row["turno"]. "\")' class='btn btn-secondary btn-sm'>Visualizar</button>
                            <button onclick='abrirModalEditarFiscal(" . $row["id"] . ", \"" . htmlspecialchars($row["nome"]) . "\", \"" . htmlspecialchars($row["cnh"]) . "\", \"" . htmlspecialchars($row["nome_carro"]) . "\", \"" . htmlspecialchars($row["placa"]) . "\", \"" . htmlspecialchars($row["destino"]) . "\", \"" . htmlspecialchars($row["numero"]) ."\")' class='btn btn-primary btn-sm'>Editar</button>
                            <button class='btn btn-danger btn-sm' onclick='excluirFiscal(" . $row["id"] . ")'>Excluir</button>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Nenhum fiscal cadastrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!--Modal de Fiscal, para visualizar as informaçoes-->
        <div class="modal fade" id="modalFiscal" tabindex="-1" aria-labelledby="modalFiscalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalFiscalLabel">DETALHES DO FISCAL</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="nomeFiscal">Nome: </p>
                        <p id="cnhFiscal">CNH: </p>
                        <p id="numeroFiscal">Número: </p>
                        <p id="carroFiscal">Carro: </p>
                        <p id="placaFiscal">Placa: </p>
                        <p id="destinoFiscal">Destino: </p>
                        <p id="turnoFiscal">Turno:</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar um fiscal -->

    <div class="modal fade" id="modalEditarFiscal" tabindex="-1" aria-labelledby="modalEditarFiscalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarFiscalLabel">Editar Motorista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editMessage" class="alert d-none"></div> <!-- Div para mensagens de edição -->
                    <form method="post" id="formEditarFiscal">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_nome" class="form-label">Nome:</label>
                            <input type="text" class="form-control" name="nome" id="edit_nome" placeholder="Nome completo" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_cnh" class="form-label">CNH:</label>
                            <input type="text" class="form-control" name="cnh" id="edit_cnh" placeholder="Número da CNH" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nome_carro" class="form-label">Veículo:</label>
                            <input type="text" class="form-control" name="nome_carro" id="edit_nome_carro" placeholder="Modelo do veículo" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_placa" class="form-label">Placa:</label>
                            <input type="text" class="form-control" name="placa" id="edit_placa" placeholder="Placa do veículo" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_destino" class="form-label">Destino:</label>
                            <input type="text" class="form-control" name="destino" id="edit_destino" placeholder="Destino" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_numero" class="form-label">Telefone:</label>
                            <input type="tel" class="form-control" name="numero" id="edit_numero" placeholder="Número de telefone" required>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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

        function abrirModalFiscal(nome, cnh, nome_carro, placa, destino, numero, turno) {
            document.getElementById("nomeFiscal").textContent = "Nome: " + nome;
            document.getElementById("cnhFiscal").textContent = "CNH: " + cnh;
            document.getElementById("numeroFiscal").textContent = "Número: " + numero;
            document.getElementById("carroFiscal").textContent = "Carro: " + nome_carro;
            document.getElementById("placaFiscal").textContent = "Placa: " + placa;
            document.getElementById("destinoFiscal").textContent = "Destino: " + destino;
            document.getElementById("turnoFiscal").textContent = "Turno: " + turno;


            var myModal = new bootstrap.Modal(document.getElementById('modalFiscal'), {
                keyboard: false
            });
            myModal.show();
        }

        function logout() {
            window.location.href = "http://localhost/sistema_dashboard/frontend/admin/login.php";
        }

        function abrirModalEditarFiscal(id, nome, cnh, nome_carro, placa, destino, numero) {
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_nome").value = nome;
            document.getElementById("edit_cnh").value = cnh;
            document.getElementById("edit_nome_carro").value = nome_carro;
            document.getElementById("edit_placa").value = placa;
            document.getElementById("edit_destino").value = destino;
            document.getElementById("edit_numero").value = numero;

            var editModal = new bootstrap.Modal(document.getElementById('modalEditarFiscal'), {
                keyboard: false
            });
            editModal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa a máscara no campo de telefone
            $('#numero').mask('(00) 00000-0000');

            // Inicializa a máscara no campo de telefone de edição
            $('#edit_numero').mask('(00) 00000-0000');

            const searchBar = document.querySelector('.search-bar');
            const ordenarSelect = document.querySelector('.ordenar-select');
            const table = document.getElementById('fiscaisTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            // Função para filtrar a tabela com base na pesquisa
            searchBar.addEventListener('input', function() {
                const searchText = searchBar.value.toLowerCase();
                Array.from(rows).forEach(function(row) {
                    const cells = row.getElementsByTagName('td');
                    let match = false;
                    Array.from(cells).forEach(function(cell) {
                        if (cell.textContent.toLowerCase().indexOf(searchText) > -1) {
                            match = true;
                        }
                    });
                    row.style.display = match ? '' : 'none';
                });
            });

            // Função para ordenar a tabela com base na seleção
            ordenarSelect.addEventListener('change', function() {
                const orderBy = ordenarSelect.value;
                const rowsArray = Array.from(rows);

                rowsArray.sort(function(a, b) {
                    const aValue = a.getElementsByTagName('td')[0].textContent.toLowerCase();
                    const bValue = b.getElementsByTagName('td')[0].textContent.toLowerCase();

                    if (orderBy === 'Mais recentes') {
                        return rowsArray.indexOf(b) - rowsArray.indexOf(a);
                    } else if (orderBy === 'Mais antigos') {
                        return rowsArray.indexOf(a) - rowsArray.indexOf(b);
                    } else if (orderBy === 'A-Z') {
                        return aValue.localeCompare(bValue);
                    } else if (orderBy === 'Z-A') {
                        return bValue.localeCompare(aValue);
                    }
                });

                // Reinserir as linhas ordenadas na tabela
                rowsArray.forEach(function(row) {
                    table.getElementsByTagName('tbody')[0].appendChild(row);
                });
            });

            // Manipulador para o formulário de edição
            $("#formEditarFiscal").submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: "../../backend/fiscais/processar_edicao_fiscal.php",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        let messageDiv = $("#editMessage");
                        messageDiv.removeClass("d-none").addClass(response.status === "success" ? "alert-success" : "alert-danger");
                        messageDiv.text(response.message);

                        if (response.status === "success") {
                            // Recarregar a página após 2 segundos para mostrar as alterações
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                    error: function(error) {
                        console.log("Erro ao enviar o formulário de edição.");
                        console.error(error);

                        let messageDiv = $("#editMessage");
                        messageDiv.removeClass("d-none").addClass("alert-danger");
                        messageDiv.text("Erro ao processar a solicitação. Tente novamente.");
                    }
                });
            });
        });
        $("#formFiscal").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "../../backend/fiscais/cadastrar_fiscal.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    let messageDiv = $("#message");
                    messageDiv.removeClass("d-none").addClass(response.status === "success" ? "alert-success" : "alert-danger");
                    messageDiv.text(response.message);
                },
                error: function(error) {
                    console.log("Erro ao enviar o formulário.");
                    console.error(error);
                }
            });
        });
    </script>
</body>

</html>