<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestão - Página de Login">
    <title>Sistema de Gestão - Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">Login</h1>
        <p class="login-subtitle">
            Se você não tem um acesso<br>
            Você pode <strong><a href="http://localhost/sistema_dashboard/frontend/admin/cadastrar_admin.php">CRIAR AQUI!</a></strong>
        </p>

        <form method="post" id="formLogin">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Coloque seu email!" required><br>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" name="senha" placeholder="Coloque sua senha." required><br>
                
            </div>

            <div class="remember-forgot">
                <label>
                    <input type="checkbox" id="remember" name="remember"> Lembrar-me
                </label>
                <a href="http://localhost/sistema_dashboard/backend/admin/esqueceu_senha.php" class="forgot-password" value="Entrar">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="btn-login">LOGIN</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <script>
        $("#formLogin").submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "../../backend/sistemas/login.php", 
                type: "POST",
                data: $(this).serialize(), // Pega os dados do formulário
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                  window.location.href = "dashboard.php";
                  } else {
                        alert("Erro ao fazer login: " + response.message);
                    }
                },
            });
        });
    </script>
</body>
</html>