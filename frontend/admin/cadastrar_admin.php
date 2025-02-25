<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Admin</title>
    <link rel="stylesheet" href="../css/cadastro_admin.css">
    <script>
        function togglePassword(id, iconId) {
            const passwordInput = document.getElementById(id);
            const toggleIcon = document.getElementById(iconId);
            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';
            toggleIcon.textContent = isPassword ? '🙈' : '👁️';
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>Cadastre-se!</h1>
        <p class="subtitle">Se você já tem uma conta<br>Você pode <a href="http://localhost/sistema_dashboard/frontend/admin/login.php">LOGAR AQUI!</a></p>

        <div id="message"></div>
        <form method="post" id="formAdmin">
            <div class="form-group">
                <label>Usuário</label>
                <div class="input-container">
                    <input type="text" name="nome" placeholder="Coloque seu Usuário" required>
                </div>
                <span class="error" id="usernameError">Nome de usuário precisa ter pelo menos 4 caracteres</span>
            </div>

            <div class="form-group">
                <label>Email</label>
                <div class="input-container">
                    <input type="email" name="email" placeholder="Coloque seu Email" required>
                </div>
                <span class="error" id="emailError">Seu email está faltando algo! Confira-o!</span>
            </div>

            <div class="form-group">
                <label>Senha</label>
                <div class="input-container">
                    <input type="password" name="senha" id="password" placeholder="Coloque sua Senha" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', 'toggleIcon')">
                        <span id="toggleIcon">👁️</span>
                </div>
                <span class="error" id="passwordError">Sua senha precisa ter pelo menos 6 caracteres!</span>
            </div>

            <button type="submit" value="Cadastrar">CADASTRAR</button>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $("#formAdmin").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "../../backend/admin/cadastro_admin.php",
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