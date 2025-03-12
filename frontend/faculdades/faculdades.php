<?php
session_start();
require '../../backend/sistemas/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Consulta SQL para obter todos os fiscais
$sql = "SELECT * FROM faculdades";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Dashboard - Faculdades</title>
    <link rel="stylesheet" href="../css/faculdades.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        //Passando os dados php para o javascript
        const data = <?php echo isset($json_data) ? $json_data : '[]'; ?>
    </script>
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

    <div class="main-content">
        <div class="header">
            <h1>FACULDADES</h1>
            <div class="controls">
                <input type="text" class="search-bar" placeholder="Pesquisar...">
                <select class="ordenar-select" aria-label="Ordenar faculdades">
                    <option value="#">Filtrar</option>
                    <option value="mais-recentes">Mais recentes</option>
                    <option value="mais-antigos">Mais antigos</option>
                    <option value="a-z">A-Z</option>
                    <option value="z-a">Z-A</option>
                </select>
                <select class="selectPag" aria-label="Registros por página">
                    <option value="5">5 registros</option>
                    <option value="10">10 registros</option>
                    <option value="20">20 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
        </div>

        <div class="criar-fiscal">
            <a href="cadastrar_faculdade.php">Adicionar Nova Faculdade</a>
        </div>

        <table id="fiscaisTable">
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
                            <a href='editar_faculdade.php?id=" . $row["id"] . "' class='edit-button'>Editar</a>
                            <button class='btn-excluir' onclick='excluirFaculdade(" . $row["id"] . ")'>Excluir</button>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Nenhuma faculdade cadastrada.</td></tr>";
                }
                ?> 
            </tbody>
        </table>

        <!-- Adiciona o container da paginação após a tabela -->
        <div id="pagination"></div>

        <script>
            // Configuração da paginação
            let currentPage = 1;
            let rowsPerPage = 5;
            let tableData = [];

            // Função para inicializar a tabela
            function initializeTable() {
                const table = document.getElementById('fiscaisTable');
                const tbody = table.getElementsByTagName('tbody')[0];
                const rows = Array.from(tbody.getElementsByTagName('tr')); // Captura apenas as linhas do tbody
                
                // Verifica se há linhas disponíveis
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
                
                // Limpa a tabela
                tbody.innerHTML = '';
                
                // Calcula o início e fim dos dados para a página atual
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const paginatedData = filteredData.slice(start, end);
                
                if (paginatedData.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5">Nenhuma faculdade encontrada.</td></tr>';
                } else {
                    // Adiciona as linhas filtradas e paginadas
                    paginatedData.forEach(item => {
                        tbody.appendChild(item.element.cloneNode(true));
                        
                        // Revincula os eventos de onclick para os botões excluir
                        // Necessário porque cloneNode não mantém os eventos
                        const btnExcluir = tbody.lastElementChild.querySelector('.btn-excluir');
                        if (btnExcluir) {
                            const id = btnExcluir.getAttribute('onclick').match(/\d+/)[0];
                            btnExcluir.onclick = null; // Remove o evento anterior
                            btnExcluir.addEventListener('click', function() {
                                excluirFaculdade(id);
                            });
                        }
                    });
                }
                
                // Atualiza a paginação
                updatePagination(totalPages);
            }

            // Função para atualizar os controles de paginação
            function updatePagination(totalPages) {
                const paginationContainer = document.getElementById('pagination');
                let paginationHTML = '';
                
                if (totalPages > 0) {
                    // Botão anterior
                    paginationHTML += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&lt;</button>`;
                    
                    // Números das páginas - limita a mostrar no máximo 5 páginas
                    const maxPagesToShow = 5;
                    let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
                    let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
                    
                    // Ajusta startPage se necessário para mostrar o número correto de páginas
                    if (endPage - startPage + 1 < maxPagesToShow && startPage > 1) {
                        startPage = Math.max(1, endPage - maxPagesToShow + 1);
                    }
                    
                    // Primeira página e reticências, se necessário
                    if (startPage > 1) {
                        paginationHTML += `<button onclick="changePage(1)">1</button>`;
                        if (startPage > 2) {
                            paginationHTML += `<span>...</span>`;
                        }
                    }
                    
                    // Páginas numeradas
                    for (let i = startPage; i <= endPage; i++) {
                        paginationHTML += `<button onclick="changePage(${i})" class="${currentPage === i ? 'active' : ''}">${i}</button>`;
                    }
                    
                    // Última página e reticências, se necessário
                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            paginationHTML += `<span>...</span>`;
                        }
                        paginationHTML += `<button onclick="changePage(${totalPages})">${totalPages}</button>`;
                    }
                    
                    // Botão próximo
                    paginationHTML += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>&gt;</button>`;
                }
                
                paginationContainer.innerHTML = paginationHTML;
            }

            // Função para mudar de página
            function changePage(page) {
                // Garantir que a página esteja dentro dos limites
                const filteredData = getFilteredData();
                const totalPages = Math.ceil(filteredData.length / rowsPerPage);
                
                if (page < 1) page = 1;
                if (page > totalPages) page = totalPages;
                
                currentPage = page;
                updateTable(filteredData);
            }

            // Função para obter dados filtrados baseados na pesquisa atual
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
                currentPage = 1; // Voltar para a primeira página ao pesquisar
                updateTable(filteredData);
            }

            // Função de ordenação
            function sortTable(order) {
                if (order === '#') return; // Ignora a opção de placeholder
                
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
                        // Mantém a ordem original
                        break;
                }
                
                currentPage = 1;
                tableData = sortedData; // Atualiza os dados da tabela com a nova ordenação
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
                
                // Inicializa a tabela quando a página carregar
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
    </div>
</body>
</html>