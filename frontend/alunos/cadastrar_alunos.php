<?php
session_start();
require '../../backend/sistemas/config.php';

$sql_faculdades = "SELECT f.id, f.nome, f.sigla, f.cidade FROM faculdades f";
$result_faculdades = $conn->query($sql_faculdades);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro do Estudante</title>
    <link rel="stylesheet" href="../css/cadastro_aluno.css">
</head>

<body>
    <div class="container">
        <h1>Cadastro do Estudante</h1>
        <div id="message" class="alert"></div>
        <form id="formCadAluno" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nome_completo">Nome Completo:</label>
                <input type="text" id="nome_completo" placeholder="Coloque seu nome completo" name="nome_completo" required>
            </div>

            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" keyboardType="'numeric" required>
            </div>

            <div class="form-group">
                <label for="matricula">Matrícula:</label>
                <input type="text" id="matricula" name="matricula" placeholder="Coloque sua matrícula" required>
            </div>

            <div class="form-group">
                <label for="numero_tel">Número de Telefone:</label>
                <input type="text" id="numero_tel" name="numero_tel" placeholder="(00) 00000-0000" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>

            <div class="form-group">
                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme sua senha" required>
            </div>

            <div class="form-group">
                <label for="id_faculdade">Faculdade/Universidade:</label>
                <select id="id_faculdade" name="id_faculdade" required>
                    <option value="">Selecione a faculdade</option>
                    <?php
                    if ($result_faculdades->num_rows > 0) {
                        while ($row = $result_faculdades->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nome']) . " (" . htmlspecialchars($row['sigla']) . ") - " . htmlspecialchars($row['cidade']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nenhuma faculdade encontrada</option>";
                    }
                    ?>
                </select>
            </div>

            <!--<div class="form-group">
                <label for="id_cidade">Cidade:</label>
                <select id="id_cidade" name="id_cidade" required>
                    <option value="">Selecione a cidade</option>
                    <option data-faculdades="1,2,3,4,5,6,7,8" value="1">Recife</option>
                    <option data-faculdades="15,16" value="2">Olinda</option>
                    <option data-faculdades="9,12,17,18,19" value="3">Caruaru</option>
                    <option data-faculdades="10,13,14" value="4">Petrolina</option>
                </select>
            </div>-->

            <div class="form-group">
                <label for="curso">Curso:</label>
                <input type="text" id="curso" name="curso" placeholder="Digite o nome do curso" required>
            </div>

            <div class="form-group">
                <label for="foto">Foto:</label>
                <input type="file" id="foto" name="foto" required>
            </div>
            <button type="submit" value="Cadastrar">CADASTRAR-SE</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $("#formCadAluno").submit(function(event) {
            event.preventDefault();

            // Verificar senhas antes de enviar
            const senha = document.getElementById('senha');
            const confirmarSenha = document.getElementById('confirmar_senha');

            if (senha.value !== confirmarSenha.value) {
                const messageElement = document.getElementById("message");
                messageElement.innerHTML = "As senhas não coincidem!";
                messageElement.className = "alert error";
                messageElement.style.display = "block";
                return false;
            }

            var formData = new FormData(this);

            // Como o campo id_cidade está comentado no HTML, mas é exigido pelo backend
            // Vamos adicionar um valor padrão para evitar erro
            if (!formData.has('id_cidade')) {
                formData.append('id_cidade', '1'); // Valor padrão
            }

            $.ajax({
                url: "../../backend/alunos/cadastrar_aluno.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function(response) {
                    const messageElement = document.getElementById("message");

                    if (response.status === "success") {
                        messageElement.innerHTML = response.message;
                        messageElement.className = "alert success";

                        // Limpar formulário após sucesso
                        document.getElementById("formCadAluno").reset();

                        // Rolar para o topo para mostrar a mensagem
                        window.scrollTo(0, 0);
                    } else {
                        messageElement.innerHTML = response.message;
                        messageElement.className = "alert error";
                    }

                    // Garantir que a mensagem seja visível
                    messageElement.style.display = "block";
                },
                error: function(xhr, status, error) {
                    const messageElement = document.getElementById("message");
                    messageElement.innerHTML = "Erro ao enviar o formulário. Por favor, tente novamente.";
                    messageElement.className = "alert error";
                    messageElement.style.display = "block";
                    console.error("Erro AJAX:", xhr.responseText);
                }
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            const cpfInput = document.getElementById('cpf');
            const telInput = document.getElementById('numero_tel');

            cpfInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);

                if (value.length >= 3) value = value.slice(0, 3) + '.' + value.slice(3);
                if (value.length >= 7) value = value.slice(0, 7) + '.' + value.slice(7);
                if (value.length >= 11) value = value.slice(0, 11) + '-' + value.slice(11);

                e.target.value = value;
            });

            telInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);

                if (value.length >= 2) value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
                if (value.length >= 9) value = value.slice(0, 10) + '-' + value.slice(10);

                e.target.value = value;
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const senha = document.getElementById('senha');
            const confirmarSenha = document.getElementById('confirmar_senha');

            form.addEventListener('submit', function(event) {
                if (senha.value !== confirmarSenha.value) {
                    alert('As senhas não coincidem!');
                    event.preventDefault();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const faculdadeSelect = document.getElementById('id_faculdade');
            const cidadeSelect = document.getElementById('id_cidade');

            faculdadeSelect.addEventListener('change', function() {
                const faculdadeValue = this.value;

                // Filtrar as cidades que correspondem à faculdade selecionada
                Array.from(cidadeSelect.options).forEach(option => {
                    const faculdadesRelacionadas = option.dataset.faculdades?.split(',') || [];
                    option.style.display = faculdadesRelacionadas.includes(faculdadeValue) ? 'block' : 'none';
                });

                cidadeSelect.value = ""; // Resetar a seleção da cidade
            });

            cidadeSelect.addEventListener('change', function() {
                const cidadeValue = this.value;

                // Mostrar apenas as faculdades relacionadas à cidade selecionada
                Array.from(faculdadeSelect.options).forEach(option => {
                    const cidadesRelacionadas = Array.from(cidadeSelect.options)
                        .filter(opt => opt.dataset.faculdades?.includes(option.value))
                        .map(opt => opt.dataset.faculdades.split(','))
                        .flat();

                    option.style.display = cidadesRelacionadas.includes(option.value) ? 'block' : 'none';
                });
            });
        });
    </script>
</body>

</html>