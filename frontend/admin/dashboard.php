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
    a.turno,
    a.compMatricula,
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
    <link rel="stylesheet" href="../css/dashboard.css">
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
                <button class="btn btn-success" onclick="abrirModalCadastro()">Novo Aluno</button>
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
                <button onclick='abrirModal(\"" . $row["nome_completo"] . "\", \"" . $row["cpf"] . "\", \"" . $row['numero_tel'] . "\", \"" . $row["matricula"] . "\", \"" . $row["turno"] . "\", \"" . $row["status"] . "\", \"" . $row["nome_faculdade"] .
                                "\", \"" . $row["cidade"] . "\", \"" . $row["foto"] . "\", \"" . $row['nome_motorista'] . "\", \"" . $row['nome_carro'] . "\", \"" . $row['placa'] . "\", \"" . $row["compMatricula"] . "\")' class='btn btn-secondary btn-sm'>Visualizar</button>
                <button onclick='editarAluno(\"" . $row["cpf"] . "\", \"" . $row["nome_completo"] . "\", \"" . $row["matricula"] . "\", \"" . $row["numero_tel"] . "\", \"" . $row["status"] . "\", \"" . $row["id_fiscal"] . "\")' class='btn btn-primary btn-sm'>Editar</button>
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
                <p id="turnoCarteira"></p>
                <p id="statusCarteira"></p>
                <p id="faculdadeCarteira"></p>
                <p id="cidadeCarteira"></p>
                <p id="motoristaCarteira"></p>
                <p id="carroCarteira"></p>
                <div id="compMatriculaCarteira">
                    <p>Comprovante de Matrícula: <a href="#" id="linkComprovante" target="_blank">Visualizar</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Cadastro de Aluno -->
    <div id="modalCadastro" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModalCadastro()">&times;</span>
            <h2 class="text-center mb-4">Cadastrar Novo Aluno</h2>
            <form id="formCadastroAluno" enctype="multipart/form-data">
                <div id="mensagem-cadastro" class="alert" style="display: none; margin-bottom: 15px;"></div>
                <div class="mb-3">
                    <label for="nome_completo" class="form-label">Nome Completo:</label>
                    <input type="text" class="form-control" id="nome_completo" name="nome_completo" required>
                </div>
                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF:</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" maxlength="14" required>
                </div>
                <div class="mb-3">
                    <label for="matricula" class="form-label">Matrícula:</label>
                    <input type="text" class="form-control" id="matricula" name="matricula" required>
                </div>
                <div class="mb-3">
                    <label for="numero_tel" class="form-label">Número de Telefone:</label>
                    <input type="text" class="form-control" id="numero_tel" name="numero_tel" maxlength="15" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha:</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
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

                <div class="mb-3">
                    <label for="id_faculdade" class="form-label">Faculdade</label>
                    <select class="form-select" id="id_faculdade" name="id_faculdade" required>
                        <option value="">Selecione uma faculdade:</option>
                        <?php
                        $faculdades_sql = "SELECT id, nome, cidade FROM faculdades ORDER BY nome";
                        $faculdades_result = $conn->query($faculdades_sql);
                        while ($faculdade = $faculdades_result->fetch_assoc()) {
                            echo "<option value='" . $faculdade['id'] . "'>" . $faculdade['nome'] . " - " . $faculdade['cidade'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="curso" class="form-label">Curso:</label>
                    <input type="text" class="form-control" id="curso" name="curso" required>
                </div>
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto:</label>
                    <input type="file" class="form-control" id="foto" name="foto">
                </div>
                <div class="mb-3">
                    <label for="compMatricula" class="form-label">Comprovante de Matricula:</label>
                    <input type="file" class="form-control" id="compMatricula" name="compMatricula" accept=".pdf, .jpg, .jpeg, .png">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edição de Aluno -->
    <div id="modalEditar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModalEditar()">&times;</span>
            <h2 class="text-center mb-4">Editar Aluno</h2>
            <form id="formEditarAluno">
                <input type="hidden" id="edit_cpf" name="cpf">
                <div class="mb-3">
                    <label for="edit_nome_completo" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="edit_nome_completo" name="nome_completo" required>
                </div>
                <div class="mb-3">
                    <label for="edit_matricula" class="form-label">Matrícula</label>
                    <input type="text" class="form-control" id="edit_matricula" name="matricula" required>
                </div>
                <div class="mb-3">
                    <label for="edit_numero_tel" class="form-label">Número de Telefone</label>
                    <input type="text" class="form-control" id="edit_numero_tel" name="numero_tel" maxlength="15" required>
                </div>
                <div class="mb-3">
                    <label for="edit_status" class="form-label">Status</label>
                    <select class="form-select" id="edit_status" name="status" required>
                        <option value="Ativo">Ativo</option>
                        <option value="Inativo">Inativo</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="edit_id_fiscal" class="form-label">Motorista</label>
                    <select class="form-select" id="edit_id_fiscal" name="id_fiscal" required>
                        <option value="">Selecione um motorista</option>
                        <?php
                        $fiscais_sql = "SELECT id, nome, nome_carro, placa FROM fiscais ORDER BY nome";
                        $fiscais_result = $conn->query($fiscais_sql);
                        while ($fiscal = $fiscais_result->fetch_assoc()) {
                            echo "<option value='" . $fiscal['id'] . "'>" . $fiscal['nome'] . " (" . $fiscal['nome_carro'] . " - " . $fiscal['placa'] . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
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

        function abrirModal(nome, cpf, numero_tel, matricula, turno, status, faculdade, cidade, foto, motorista, carro, placa, compMatricula) {
            document.getElementById("nomeCarteira").textContent = "Nome: " + nome;
            document.getElementById("cpfCarteira").textContent = "CPF: " + cpf;
            document.getElementById("numeroCarteira").textContent = "Número: " + numero_tel;
            document.getElementById("matriculaCarteira").textContent = "Matrícula: " + matricula;
            document.getElementById("turnoCarteira").textContent = "Turno: " + turno;
            document.getElementById("statusCarteira").textContent = "Status: " + status;
            document.getElementById("faculdadeCarteira").textContent = "Faculdade: " + faculdade;
            document.getElementById("cidadeCarteira").textContent = "Cidade: " + cidade;

            // Adicionando informações do motorista
            document.getElementById("motoristaCarteira").textContent = "Motorista: " + motorista;
            document.getElementById("carroCarteira").textContent = "Carro: " + carro + " - " + placa;

            const fotoPerfil = document.querySelector('.foto-perfil img');
            const nomeArquivo = foto.split('/').pop();
            const caminhoCorreto = "../../backend/alunos/uploads/fotoAluno/" + nomeArquivo;

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

            // Configurar o link do comprovante de matrícula
            const linkComprovante = document.getElementById("linkComprovante");
            if (compMatricula && compMatricula.trim() !== "") {
                // Verifica se o caminho já é completo ou precisa ser construído
                let caminhoComprovante;
                if (compMatricula.includes('/uploads/comprovantes/')) {
                    caminhoComprovante = "../../backend" + compMatricula.substring(compMatricula.indexOf('/alunos/'));
                } else {
                    caminhoComprovante = "../../backend/alunos/uploads/comprovantes/" + compMatricula.split('/').pop();
                }
                
                linkComprovante.href = caminhoComprovante;
                linkComprovante.style.display = "inline";
                document.getElementById("compMatriculaCarteira").style.display = "block";
            } else {
                document.getElementById("compMatriculaCarteira").style.display = "none";
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

        // Funções para o modal de cadastro
        function abrirModalCadastro() {
            document.getElementById("modalCadastro").style.display = "block";
        }

        function fecharModalCadastro() {
            document.getElementById("modalCadastro").style.display = "none";
            document.getElementById("formCadastroAluno").reset();
        }

        // Envio do formulário de cadastro
        $(document).ready(function() {
            // Máscara para CPF
            $("#cpf").on("input", function() {
                var value = $(this).val().replace(/\D/g, '');
                if (value.length > 9) {
                    value = value.replace(/^(\d{3})(\d{3})(\d{3})/, "$1.$2.$3-");
                } else if (value.length > 6) {
                    value = value.replace(/^(\d{3})(\d{3})/, "$1.$2.");
                } else if (value.length > 3) {
                    value = value.replace(/^(\d{3})/, "$1.");
                }
                $(this).val(value);
            });

            // Máscara para Telefone
            $("#numero_tel").on("input", function() {
                var value = $(this).val().replace(/\D/g, '');
                if (value.length > 10) {
                    value = value.replace(/^(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
                } else if (value.length > 6) {
                    value = value.replace(/^(\d{2})(\d{4})/, "($1) $2-");
                } else if (value.length > 2) {
                    value = value.replace(/^(\d{2})/, "($1) ");
                }
                $(this).val(value);
            });

            // Também adicionar máscaras para o modal de edição
            $("#edit_numero_tel").on("input", function() {
                var value = $(this).val().replace(/\D/g, '');
                if (value.length > 10) {
                    value = value.replace(/^(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
                } else if (value.length > 6) {
                    value = value.replace(/^(\d{2})(\d{4})/, "($1) $2-");
                } else if (value.length > 2) {
                    value = value.replace(/^(\d{2})/, "($1) ");
                }
                $(this).val(value);
            });
        });

        $("#formCadastroAluno").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            // Verifique se o arquivo foi selecionado
            var compMatricula = $('#compMatricula')[0].files[0];
            if (compMatricula) {
                formData.append('compMatricula', compMatricula);
                console.log("Comprovante de matrícula anexado:", compMatricula.name);
            } else {
                console.log("Nenhum comprovante de matrícula anexado");
            }

            var mensagemElement = document.getElementById("mensagem-cadastro");
            mensagemElement.style.display = "none";

            $.ajax({
                url: '../../backend/alunos/api_cadastro.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Verifica se a resposta já é um objeto
                    var res;
                    if (typeof response === 'string') {
                        try {
                            res = JSON.parse(response);
                        } catch (e) {
                            // Se não puder analisar como JSON, exibe uma mensagem genérica
                            console.error("Erro ao processar resposta:", response);
                            mensagemElement.innerHTML = "Erro ao processar o cadastro. Por favor, tente novamente.";
                            mensagemElement.className = "alert alert-danger";
                            mensagemElement.style.display = "block";
                            return;
                        }
                    } else {
                        res = response;
                    }
                    
                    if (res && res.status === 'success') {
                        mensagemElement.innerHTML = res.message;
                        mensagemElement.className = "alert alert-success";
                        mensagemElement.style.display = "block";

                        // Limpar formulário após 2 segundos e recarregar a página
                        setTimeout(function() {
                            fecharModalCadastro();
                            location.reload();
                        }, 2000);
                    } else {
                        mensagemElement.innerHTML = "Erro ao cadastrar: " + (res && res.message ? res.message : "Erro desconhecido");
                        mensagemElement.className = "alert alert-danger";
                        mensagemElement.style.display = "block";
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro AJAX:", xhr.responseText);
                    try {
                        // Tenta analisar a resposta de erro como JSON
                        var errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse && errorResponse.message) {
                            mensagemElement.innerHTML = "Erro ao processar o cadastro: " + errorResponse.message;
                        } else {
                            mensagemElement.innerHTML = "Erro ao processar o cadastro: " + error;
                        }
                    } catch (e) {
                        // Se não puder analisar como JSON, exibe uma mensagem genérica
                        console.error("Erro ao processar resposta:", xhr.responseText);
                        mensagemElement.innerHTML = "Erro ao processar o cadastro. Por favor, tente novamente.";
                    }
                    mensagemElement.className = "alert alert-danger";
                    mensagemElement.style.display = "block";
                }
            });
        });

        // Função para editar aluno
        function editarAluno(cpf, nome, matricula, numero_tel, status, id_fiscal) {
            // Preenche o formulário de edição
            document.getElementById("edit_cpf").value = cpf;
            document.getElementById("edit_nome_completo").value = nome;
            document.getElementById("edit_matricula").value = matricula;
            document.getElementById("edit_numero_tel").value = numero_tel;
            document.getElementById("edit_status").value = status;

            // Se o id_fiscal for válido, seleciona o motorista
            if (id_fiscal) {
                document.getElementById("edit_id_fiscal").value = id_fiscal;
            }

            // Abre o modal
            document.getElementById("modalEditar").style.display = "block";
        }

        function fecharModalEditar() {
            document.getElementById("modalEditar").style.display = "none";
        }

        // Atualizar cidade ao selecionar faculdade
        $(document).ready(function() {
            $("#id_faculdade").change(function() {
                var faculdadeId = $(this).val();
                if (faculdadeId) {
                    // Encontra o elemento option da faculdade selecionada
                    var faculdadeOption = $(this).find("option:selected");

                    // Obtém a cidade dessa faculdade
                    var cidadeTexto = faculdadeOption.text().split(" - ")[1];

                    // Procura a opção da cidade no select de cidades que contém esse texto
                    var cidadeOption = $("#id_cidade option").filter(function() {
                        return $(this).text() === cidadeTexto;
                    });

                    // Se encontrou, seleciona essa cidade
                    if (cidadeOption.length) {
                        $("#id_cidade").val(cidadeOption.val());
                    }
                }
            });
        });

        // Envio do formulário de edição
        $(document).ready(function() {
            $("#formEditarAluno").submit(function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: '../../backend/alunos/processar_edicao_aluno.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === 'success') {
                            alert(res.message);
                            fecharModalEditar();
                            location.reload();
                        } else {
                            alert("Erro ao editar: " + res.message);
                        }
                    },
                    error: function() {
                        alert("Erro ao processar a edição!");
                    }
                });
            });
        });
    </script>
</body>

</html>