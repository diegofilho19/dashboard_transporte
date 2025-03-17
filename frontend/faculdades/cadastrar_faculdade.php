<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Faculdade</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #003366;
            flex: 1;
        }

        .container {
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 110px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center mb-4">Cadastro da Faculdade</h2>

        <div id="message"></div>

        <form method="post" id="formFaculdade">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome da Faculdade:</label>
                <input type="text" class="form-control" name="nome" id="nome" placeholder="Coloque o nome da Faculdade" required>
            </div>

            <div class="mb-3">
                <label for="sigla" class="form-label">Sigla:</label>
                <input type="text" class="form-control" name="sigla" id="sigla" placeholder="Sigla" required>
            </div>

            <div class="mb-3">
                <label for="cidade" class="form-label">Cidade:</label>
                <input type="text" class="form-control" name="cidade" id="cidade" placeholder="Cidade da Faculdade" required>
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo:</label>
                <select class="form-select" name="tipo" id="tipo" required>
                    <option value="">Selecione o tipo</option>
                    <option value="Pública">Pública</option>
                    <option value="Privada">Privada</option>
                    <option value="Estadual">Estadual</option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Registrar</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
                    let messageDiv = document.getElementById("message");
                    if (response.status === "success") {
                        messageDiv.innerHTML = `<div class="alert alert-success">${response.message}</div>`;
                    } else {
                        messageDiv.innerHTML = `<div class="alert alert-danger">${response.message}</div>`;
                    }
                },
                error: function(error) {
                    console.log("Erro ao enviar o formulário.");
                    console.error(error);
                    document.getElementById("message").innerHTML = `<div class="alert alert-danger">Erro ao cadastrar. Tente novamente.</div>`;
                }
            });
        });
    </script>
</body>

</html>
