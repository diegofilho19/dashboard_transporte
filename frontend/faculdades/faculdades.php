<?php
session_start();
require '../../backend/sistemas/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Consulta SQL para obter todas as faculdades
$sql = "SELECT * FROM faculdades";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Dashboard - Faculdades</title>
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
        }
        .pagination button {
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            cursor: pointer;
        }
        .pagination button.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
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
            <h1>FACULDADES</h1>
            <div class="controls d-flex gap-2">
                <input type="text" class="form-control search-bar" placeholder="Pesquisar...">
                <select class="form-select ordenar-select" aria-label="Ordenar faculdades">
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

        <div class="criar-fiscal mb-3">
            <a href="cadastrar_faculdade.php" class="btn btn-primary">Adicionar Nova Faculdade</a>
        </div>

        <div class="table-responsive">
            <table id="fiscaisTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome da Faculdade</th>
                        <th>Sigla</th>
                        <th>Cidade</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["nome"] . "</td>";
                            echo "<td>" . $row["sigla"] . "</td>";
                            echo "<td>" . $row["cidade"] . "</td>";
                            echo "<td>" . $row["tipo"] . "</td>";
                            echo "<td>
                                <a href='editar_faculdade.php?id=" . $row["id"] . "' class='btn btn-primary btn-sm'>Editar</a>
                                <button class='btn btn-danger btn-sm' onclick='excluirFaculdade(" . $row["id"] . ")'>Excluir</button>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhuma faculdade cadastrada.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div id="pagination" class="pagination d-flex justify-content-center"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Configuração da paginação
        let currentPage = 1;
        let rowsPerPage = 5;
        let tableData = [];

        // Função para inicializar a tabela
        function initializeTable() {
            const table = document.getElementById('fiscaisTable');
            const tbody = table.getElementsByTagName('tbody')[0];
            const rows = Array.from(tbody.getElementsByTagName('tr'));
            
            if (rows.length > 0) {
                tableData = rows.map(row => ({
                    element: row,
                    nome: row.cells[0]?.textContent.toLowerCase() || '',
                    sigla: row.cells[1]?.textContent.toLowerCase() || '',
                    cidade: row.cells[2]?.textContent.toLowerCase() || '',
                    tipo: row.cells[3]?.textContent.toLowerCase() || ''
                }));
            }
            
            updateTable();
        }

        // Função para atualizar a exibição da tabela
        function updateTable(filteredData = tableData) {
            const table = document.getElementById('fiscaisTable');
            const tbody = table.getElementsByTagName('tbody')[0];
            const totalPages = Math.ceil(filteredData.length / rowsPerPage);
            
            tbody.innerHTML = '';
            
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedData = filteredData.slice(start, end);
            
            if (paginatedData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">Nenhuma faculdade encontrada.</td></tr>';
            } else {
                paginatedData.forEach(item => {
                    tbody.appendChild(item.element.cloneNode(true));
                    
                    const btnExcluir = tbody.lastElementChild.querySelector('.btn-danger');
                    if (btnExcluir) {
                        const id = btnExcluir.getAttribute('onclick').match(/\d+/)[0];
                        btnExcluir.onclick = null;
                        btnExcluir.addEventListener('click', function() {
                            excluirFaculdade(id);
                        });
                    }
                });
            }
            
            updatePagination(totalPages);
        }

        // Função para atualizar os controles de paginação
        function updatePagination(totalPages) {
            const paginationContainer = document.getElementById('pagination');
            let paginationHTML = '';
            
            if (totalPages > 0) {
                paginationHTML += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&lt;</button>`;
                
                const maxPagesToShow = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
                let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
                
                if (endPage - startPage + 1 < maxPagesToShow && startPage > 1) {
                    startPage = Math.max(1, endPage - maxPagesToShow + 1);
                }
                
                if (startPage > 1) {
                    paginationHTML += `<button onclick="changePage(1)">1</button>`;
                    if (startPage > 2) {
                        paginationHTML += `<span>...</span>`;
                    }
                }
                
                for (let i = startPage; i <= endPage; i++) {
                    paginationHTML += `<button onclick="changePage(${i})" class="${currentPage === i ? 'active' : ''}">${i}</button>`;
                }
                
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        paginationHTML += `<span>...</span>`;
                    }
                    paginationHTML += `<button onclick="changePage(${totalPages})">${totalPages}</button>`;
                }
                
                paginationHTML += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>&gt;</button>`;
            }
            
            paginationContainer.innerHTML = paginationHTML;
        }

        // Função para mudar de página
        function changePage(page) {
            const filteredData = getFilteredData();
            const totalPages = Math.ceil(filteredData.length / rowsPerPage);
            
            if (page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            currentPage = page;
            updateTable(filteredData);
        }

        // Função para obter dados filtrados
        function getFilteredData() {
            const searchTerm = document.querySelector('.search-bar').value.toLowerCase();
            
            return tableData.filter(item => 
                item.nome.includes(searchTerm) ||
                item.sigla.includes(searchTerm) ||
                item.cidade.includes(searchTerm) ||
                item.tipo.includes(searchTerm)
            );
        }

        // Função de pesquisa
        function searchTable() {
            const filteredData = getFilteredData();
            currentPage = 1;
            updateTable(filteredData);
        }

        // Função de ordenação
        function sortTable(order) {
            if (order === '#') return;
            
            let sortedData = [...tableData];
            
            switch(order) {
                case 'a-z':
                    sortedData.sort((a, b) => a.nome.localeCompare(b.nome));
                    break;
                case 'z-a':
                    sortedData.sort((a, b) => b.nome.localeCompare(a.nome));
                    break;
                case 'mais-recentes':
                    sortedData.reverse();
                    break;
                case 'mais-antigos':
                    break;
            }
            
            currentPage = 1;
            tableData = sortedData;
            const filteredData = getFilteredData();
            updateTable(filteredData);
        }

        // Função para mudar o número de registros por página
        function changeRowsPerPage(value) {
            rowsPerPage = parseInt(value);
            currentPage = 1;
            const filteredData = getFilteredData();
            updateTable(filteredData);
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.search-bar').addEventListener('input', searchTable);
            document.querySelector('.ordenar-select').addEventListener('change', (e) => sortTable(e.target.value));
            document.querySelector('.selectPag').addEventListener('change', (e) => changeRowsPerPage(e.target.value));
            
            initializeTable();
        });

        function excluirFaculdade(id) {
            if (confirm("Tem certeza que deseja excluir esta faculdade?")) {
                $.ajax({
                    url: '../../backend/faculdades/excluir_faculdades.php',
                    type: 'GET',
                    data: { id: id },
                    success: function(response) {
                        alert("Faculdade excluída com sucesso!");
                        location.reload();
                    },
                    error: function() {
                        alert("Erro ao excluir faculdade.");
                    }
                });
            }
        }

        function logout() {
            window.location.href = "http://localhost/sistema_dashboard/frontend/admin/login.php";
        }
    </script>
</body>
</html>