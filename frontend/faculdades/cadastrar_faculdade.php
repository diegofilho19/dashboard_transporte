<!DOCTYPE html>
<html>
<head>
    <title>Cadastrar Fiscal</title>
    <link rel="stylesheet" href="../css/cadastro_faculdade.css">
</head>
<body>
<div class="container">
        <h1>Cadastro da Faculdade</h1>
       
        <div id="message"></div>

        <form method="post" id="formFaculdade">
            <label for="nome">Nome da Faculdade:</label><br>
            <input type="text" name="nome" id="nome" placeholder="Coloque o nome da Faculdade" required><br><br>

            <label for="cnh">Sigla:</label><br>
            <input type="text" name="sigla" id="sigla" placeholder="Sigla" required><br><br>

            <label for="nome_carro">Cidade:</label><br>
            <input type="text" name="cidade" id="cidade" placeholder="Cidade da Faculdade" required><br><br>

            <label for="tipo">Tipo:</label><br>
            <select name="tipo" id="tipo" required>
                <option value="">Selecione o tipo</option>
                <option value="Pública">Pública</option>
                <option value="Privada">Privada</option>
                <option value="Estadual">Estadual</option>
            </select><br><br>

            <input type="submit" value="REGISTRAR">
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $("#formFaculdade").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "../../backend/faculdades/cadastrar_faculdades.php",
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

    </script>
</body>
</html>