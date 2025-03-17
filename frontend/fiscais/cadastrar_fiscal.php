<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fiscal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #003366;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Cadastro do Fiscal</h2>

    <div id="message" class="alert d-none"></div>

    <form method="post" id="formFiscal">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" class="form-control" name="nome" id="nome" placeholder="Coloque o nome" required>
        </div>

        <div class="mb-3">
            <label for="cnh" class="form-label">CNH:</label>
            <input type="text" class="form-control" name="cnh" id="cnh" placeholder="Coloque a CNH" required>
        </div>

        <div class="mb-3">
            <label for="nome_carro" class="form-label">Carro:</label>
            <input type="text" class="form-control" name="nome_carro" id="nome_carro" placeholder="Modelo do Carro" required>
        </div>

        <div class="mb-3">
            <label for="placa" class="form-label">Placa:</label>
            <input type="text" class="form-control" name="placa" id="placa" placeholder="Placa do Carro" required>
        </div>

        <div class="mb-3">
            <label for="destino" class="form-label">Destino:</label>
            <input type="text" class="form-control" name="destino" id="destino" placeholder="Destino" required>
        </div>

        <div class="mb-3">
            <label for="numero" class="form-label">Número (Tel):</label>
            <input type="text" class="form-control" name="numero" id="numero" placeholder="Número do Fiscal" required>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Registrar</button>
            <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancelar</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS e jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
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

    // Máscara de telefone
    $("#numero").on("input", function() {
        let telefone = $(this).val().replace(/\D/g, '').slice(0, 11);
        let formattedTelefone = telefone.length > 2 ? `(${telefone.slice(0, 2)}) ` : telefone;
        formattedTelefone += telefone.length > 2 ? telefone.slice(2, 7) + '-' : '';
        formattedTelefone += telefone.length > 7 ? telefone.slice(7, 11) : '';
        $(this).val(formattedTelefone);
    });
</script>

</body>
</html>