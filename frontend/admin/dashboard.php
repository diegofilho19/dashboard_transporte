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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .pagination button {
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .pagination button:hover {
            background-color: #0056b3;
            color: white;
            border-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .pagination button.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .status-ativo {
            color: green;
            font-weight: bold;
        }

        .status-inativo {
            color: red;
            font-weight: bold;
        }

        /* Estilos do Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 10px;
            top: 5px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .foto-perfil {
            text-align: center;
            margin-bottom: 20px;
        }

        .foto-perfil img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
        }

        .info-aluno {
            margin-top: 20px;
        }

        .info-aluno p {
            margin: 10px 0;
            font-size: 16px;
        }
        .acoes-container {
    display: flex;
    justify-content: flex-start; /* Alinha os botões à esquerda */
    gap: 5px; /* Espaçamento entre os botões */
}

.acoes-container .btn {
    padding: 5px 10px; /* Ajuste o espaçamento interno dos botões */
    border-radius: 5px; /* Bordas arredondadas */
}

.btn-visualizar {
    background-color: gray;
    color: white;
}

.btn-editar {
    background-color: blue;
    color: white;
}

.btn-excluir {
    background-color: red;
    color: white;
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
            <a href="http://localhost/sistema_dashboard/frontend/admin/dashboard.php" class="menu-item d-flex align-items-center p-3 text-decoration-none">
                <i class="bi bi-people me-3"></i>
                <span class="text-white">Alunos</span>
            </a>
            <a href="http://localhost/sistema_dashboard/frontend/faculdades/faculdades.php" class="menu-item d-flex align-items-center p-3 text-decoration-none">
                <i class="bi bi-building me-3"></i>
                <span class="text-white">Faculdades</span>
            </a>
            <a href="http://localhost/sistema_dashboard/frontend/fiscais/fiscais.php" class="menu-item d-flex align-items-center p-3 text-decoration-none">
                <i class="bi bi-car-front me-3"></i>
                <span class="text-white">Motoristas</span>
            </a>
            <a href="javascript:void(0)" onclick="logout()" class="menu-item d-flex align-items-center p-3 text-decoration-none">
                <i class="bi bi-box-arrow-right me-3"></i>
                <span class="text-white">Sair</span>
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>ALUNOS CADASTRADOS</h1>
            <div class="controls d-flex gap-2">
                <input type="text" class="form-control search-bar" placeholder="Pesquisar...">
                <select class="form-select ordenar-select" aria-label="Ordenar alunos">
                    <option value="#">Filtrar</option>
                    <option value="mais-recentes">Mais recentes</option>
                    <option value="mais-antigos">Mais antigos</option>
                    <option value="a-z">A-Z</option>
                    <option value="z-a">Z-A</option>
                </select>
                <select class="form-select selectPag" aria-label="Registros por página">
                    <option value="5">5 registros</option>
                    <option value="10">10 registros</option>
                    <option value="20">20 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="alunosTable" class="table table-striped">
                <thead>
                    <tr>
                        <th onclick="sortTable('nome')">Nome do Aluno</th>
                        <th onclick="sortTable('faculdade')">Faculdade</th>
                        <th onclick="sortTable('cpf')">CPF</th>
                        <th onclick="sortTable('matricula')">Matrícula</th>
                        <th onclick="sortTable('cidade')">Cidade (Faculdade)</th>
                        <th onclick="sortTable('data')">Data de Inserção</th>
                        <th onclick="sortTable('status')">Status</th>
                        <th onclick="sortTable('motorista')">Motorista</th>
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
                            echo "<td style='color: " . ($status_classe == "status-ativo" ? "green" : "red") . "; font-weight: bold;'>" . $row["status"] . "</td>";

                            // Exibir o motorista com o formato desejado
                            $motorista_info = isset($row['nome_motorista']) ? $row['nome_motorista'] : 'Não disponível';
                            $carro_info = isset($row['nome_carro']) ? $row['nome_carro'] . ' - ' . $row['placa'] : 'Não disponível';
                            echo "<td>" . $motorista_info . ", " . $carro_info . "</td>";

                            echo "<td>
                                <div class='acoes-container'>
                                    <button onclick='abrirModal(\"" . $row["nome_completo"] . "\", \"" . $row["cpf"] . "\", \"" . $row['numero_tel'] . "\", \"" . $row["matricula"] . "\", \"" . $row["status"] . "\", \"" . $row["nome_faculdade"] .
                                "\", \"" . $row["cidade"] . "\", \"" . $row["foto"] . "\", \"" . $row['nome_motorista'] . "\", \"" . $row['nome_carro'] . "\", \"" . $row['placa'] . "\")' class='btn btn-secondary btn-sm'>Visualizar</button>
                                    <a href='../alunos/editar_alunos.php?cpf=" . $row["cpf"] . "' class='btn btn-primary btn-sm'>Editar</a>
                                    <button class='btn btn-danger btn-sm' onclick='excluirAluno(\"" . $row["cpf"] . "\")'>Excluir</button>
                                </div>
                            </td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>Nenhum aluno cadastrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div id="pagination" class="pagination"></div>
    </div>

    <!-- Modal de Visualização -->
    <div id="modalCarteira" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">&times;</span>
            <div class="foto-perfil">
                <img src="../imgs/default_profile.png" alt="Foto do aluno">
            </div>
            <div class="info-aluno">
                <p id="nomeCarteira"></p>
                <p id="cpfCarteira"></p>
                <p id="numeroCarteira"></p>
                <p id="matriculaCarteira"></p>
                <p id="statusCarteira"></p>
                <p id="faculdadeCarteira"></p>
                <p id="cidadeCarteira"></p>
                <p id="motoristaCarteira"></p>
                <p id="carroCarteira"></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Configuração da paginação e ordenação
        let currentPage = 1;
        let rowsPerPage = 5;
        let tableData = [];
        let currentSort = {
            column: '',
            ascending: true
        };

        // Função para inicializar a tabela
        function initializeTable() {
            const table = document.getElementById('alunosTable');
            const rows = Array.from(table.getElementsByTagName('tr')).slice(1); // Ignora o cabeçalho
            tableData = rows.map(row => ({
                element: row,
                nome: row.cells[0].textContent.toLowerCase(),
                faculdade: row.cells[1].textContent.toLowerCase(),
                cpf: row.cells[2].textContent.toLowerCase(),
                matricula: row.cells[3].textContent.toLowerCase(),
                cidade: row.cells[4].textContent.toLowerCase(),
                data: row.cells[5].textContent.toLowerCase(),
                status: row.cells[6].textContent.toLowerCase(),
                motorista: row.cells[7].textContent.toLowerCase()
            }));

            updateTable();
        }

        // Função de ordenação melhorada
        function sortTable(column) {
            let sortedData = [...tableData];

            // Se clicar na mesma coluna, inverte a ordem
            if (currentSort.column === column) {
                currentSort.ascending = !currentSort.ascending;
            } else {
                currentSort.column = column;
                currentSort.ascending = true;
            }

            sortedData.sort((a, b) => {
                let comparison = 0;

                switch (column) {
                    case 'data':
                        // Converte datas no formato dd/mm/yyyy para comparação
                        const dateA = a.data.split('/').reverse().join('-');
                        const dateB = b.data.split('/').reverse().join('-');
                        comparison = new Date(dateA) - new Date(dateB);
                        break;
                    default:
                        comparison = a[column].localeCompare(b[column]);
                }

                return currentSort.ascending ? comparison : -comparison;
            });

            currentPage = 1;
            tableData = sortedData;
            updateTable();
        }

        // Função para ordenar pelo select
        function handleSelectSort(value) {
            if (value === '#') return;

            switch (value) {
                case 'a-z':
                    sortTable('nome');
                    break;
                case 'z-a':
                    sortTable('nome');
                    currentSort.ascending = false;
                    break;
                case 'mais-recentes':
                    sortTable('data');
                    currentSort.ascending = false;
                    break;
                case 'mais-antigos':
                    sortTable('data');
                    currentSort.ascending = true;
                    break;
            }

            updateTable();
        }

        // Função para atualizar a exibição da tabela
        function updateTable(filteredData = tableData) {
            const table = document.getElementById('alunosTable');
            const tbody = table.getElementsByTagName('tbody')[0];
            const totalPages = Math.ceil(filteredData.length / rowsPerPage);

            // Limpa a tabela
            tbody.innerHTML = '';

            // Calcula o início e fim dos dados para a página atual
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedData = filteredData.slice(start, end);

            if (paginatedData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9">Nenhum aluno encontrado.</td></tr>';
            } else {
                // Adiciona as linhas filtradas e paginadas
                paginatedData.forEach(item => {
                    tbody.appendChild(item.element.cloneNode(true));
                });
            }

            // Atualiza a paginação
            updatePagination(totalPages, filteredData);
        }

        // Função para atualizar a paginação
        function updatePagination(totalPages, filteredData) {
            const paginationContainer = document.getElementById('pagination');
            paginationContainer.innerHTML = ''; // Limpa o container

            if (totalPages <= 1) {
                return; // Não mostra paginação se tiver apenas uma página
            }

            // Adiciona botão "Anterior"
            const prevButton = document.createElement('button');
            prevButton.innerHTML = '&lt;';
            prevButton.disabled = currentPage === 1;
            if (!prevButton.disabled) {
                prevButton.onclick = function() {
                    currentPage--;
                    updateTable(filteredData);
                };
            }
            paginationContainer.appendChild(prevButton);

            // Adiciona botões com números das páginas
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i;
                pageButton.className = currentPage === i ? 'active' : '';
                pageButton.onclick = function() {
                    currentPage = i;
                    updateTable(filteredData);
                };
                paginationContainer.appendChild(pageButton);
            }

            // Adiciona botão "Próximo"
            const nextButton = document.createElement('button');
            nextButton.innerHTML = '&gt;';
            nextButton.disabled = currentPage === totalPages;
            if (!nextButton.disabled) {
                nextButton.onclick = function() {
                    currentPage++;
                    updateTable(filteredData);
                };
            }
            paginationContainer.appendChild(nextButton);
        }

        // Função de pesquisa
        function searchTable(searchTerm = '') {
            if (!searchTerm) {
                searchTerm = document.querySelector('.search-bar').value.toLowerCase();
            }

            const filteredData = tableData.filter(item =>
                item.nome.includes(searchTerm) ||
                item.faculdade.includes(searchTerm) ||
                item.cpf.includes(searchTerm) ||
                item.matricula.includes(searchTerm) ||
                item.cidade.includes(searchTerm) ||
                item.data.includes(searchTerm) ||
                item.status.includes(searchTerm) ||
                item.motorista.includes(searchTerm)
            );

            currentPage = 1;
            updateTable(filteredData);
            return filteredData;
        }

        // Função para mudar o número de registros por página
        function changeRowsPerPage(value) {
            rowsPerPage = parseInt(value);
            currentPage = 1;
            const searchTerm = document.querySelector('.search-bar').value.toLowerCase();
            const filteredData = searchTable(searchTerm);
            updateTable(filteredData);
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            initializeTable();

            document.querySelector('.search-bar').addEventListener('input', () => searchTable());
            document.querySelector('.ordenar-select').addEventListener('change', (e) => handleSelectSort(e.target.value));
            document.querySelector('.selectPag').addEventListener('change', (e) => changeRowsPerPage(e.target.value));
        });

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