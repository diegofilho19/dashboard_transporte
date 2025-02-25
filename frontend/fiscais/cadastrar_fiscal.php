<!DOCTYPE html>
<html>
<head>
    <title>Cadastrar Fiscal</title>
    <link rel="stylesheet" href="../css/cadastrar_fiscal.css">
</head>
<body>
<div class="container">
        <h1>Cadastro do Fiscal</h1>
       
        <div id="message"></div>

        <form method="post" id="formFiscal">
            <label for="nome">Nome:</label><br>
            <input type="text" name="nome" id="nome" placeholder="Coloque o nome" required><br><br>

            <label for="cnh">CNH:</label><br>
            <input type="text" name="cnh" id="cnh" placeholder="Coloque a CNH" required><br><br>

            <label for="nome_carro">Carro:</label><br>
            <input type="text" name="nome_carro" id="nome_carro" placeholder="Modelo do Carro" required><br><br>

            <label for="placa">Placa:</label><br>
            <input type="text" name="placa" id="placa" placeholder="Placa do Carro" required><br><br>

            <label for="destino">Destino:</label><br>
            <input type="text" name="destino" id="destino" placeholder="Destino" required><br><br>

            <label for="numero">Número(Tel):</label><br>
            <input type="text" name="numero" id="numero" placeholder="Numero do Fiscal" required><br><br>

            <input type="submit" value="REGISTER">
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $("#formFiscal").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "../../backend/fiscais/cadastrar_fiscal.php",
                type: "POST",
                data: $(this).serialize(), // Pega os dados do formulário
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        document.getElementById("message").innerHTML = response.message;
                    } else {
                        document.getElementById("message").innerHTML = response.message;
                    }
                },
                error: function(error) {
                    console.log("Erro ao enviar o formulário.");
                    console.error(error);
                }
            });
        });

        const telefoneInput = document.getElementById('numero');

        telefoneInput.addEventListener('input', () => {
            let telefone = telefoneInput.value.replace(/\D/g, '');
            telefone = telefone.slice(0, 11);

            let formattedTelefone = '';
            if (telefone.length > 0) {
                formattedTelefone += '(' + telefone.slice(0, 2) + ') ';
                if (telefone.length > 2) {
                    formattedTelefone += telefone.slice(2, 7) + '-';
                    if (telefone.length > 7) {
                        formattedTelefone += telefone.slice(7, 11);
                    }
                }
            }

            telefoneInput.value = formattedTelefone;
        });
    </script>
</body>
</html>