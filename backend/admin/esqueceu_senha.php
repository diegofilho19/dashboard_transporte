<?php
require_once __DIR__ . '/../sistemas/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha_atual = $_POST['senha'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    $erros = array();

    // Consulta o usuário no banco
    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Verifica se o usuário existe
    if (!$usuario) {
        $erros[] = "Email não encontrado";
    } else {
        // Verifica se a senha atual está correta
        if (password_verify($senha_atual, $usuario['senha'])) {
            // Senha correta, verifica a nova senha
            if ($nova_senha !== $confirmar_senha) {
                $erros[] = "As novas senhas não conferem";
            } elseif (strlen($nova_senha) < 8) {
                $erros[] = "A nova senha deve ter pelo menos 8 caracteres";
            } else {
                // Gera o hash da nova senha
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                
                // Atualiza a senha no banco
                $sql = "UPDATE admins SET senha = ? WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $senha_hash, $email);
                
                if ($stmt->execute()) {
                    echo "<script>
                    alert('Senha alterada com sucesso!');
                    setTimeout(function() {
                        window.location.href = 'http://localhost/sistema_dashboard/frontend/admin/login.php';
                    }, 2000);
                </script>";
                exit();
                } else {
                    $erros[] = "Erro ao atualizar senha";
                }
            }
        } else {
            $erros[] = "Senha atual incorreta";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="../../frontend/css/esqueceu.css">
</head>
<body>
    <div class="container">
        <h2>Redefinir Senha</h2>
        
        <?php
        if (!empty($erros)) {
            foreach ($erros as $erro) {
                echo "<div class='alert alert-danger'>$erro</div>";
            }
        }
        ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="email_admin">Email do Administrador:</label>
        <input type="email" name="email" id="email_admin" required>
    </div>

    <div class="form-group">
        <label for="senha_atual">Senha Atual:</label>
        <div class="password-container" style="position: relative;">
            <input type="password" name="senha" id="senha_atual" required>
            <span id="toggle-senha-atual" 
                  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                  onclick="togglePassword('senha_atual', 'toggle-senha-atual')">
                👁️
            </span>
        </div>
    </div>

    <div class="form-group">
        <label for="nova_senha">Nova Senha:</label>
        <div class="password-container" style="position: relative;">
            <input type="password" name="nova_senha" id="nova_senha" required minlength="6">
            <span id="toggle-nova-senha" 
                  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                  onclick="togglePassword('nova_senha', 'toggle-nova-senha')">
                👁️
            </span>
        </div>
    </div>

    <div class="form-group">
        <label for="confirmar_senha">Confirmar Nova Senha:</label>
        <div class="password-container" style="position: relative;">
            <input type="password" name="confirmar_senha" id="confirmar_senha" required minlength="6">
            <span id="toggle-confirmar-senha" 
                  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"
                  onclick="togglePassword('confirmar_senha', 'toggle-confirmar-senha')">
                👁️
            </span>
        </div>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar Senha</button>
        <a href="http://localhost/sistema_dashboard/frontend/admin/login.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>
</div>

      <script>
      function togglePassword(id, iconId) {
          const passwordInput = document.getElementById(id);
          const toggleIcon = document.getElementById(iconId);
          const isPassword = passwordInput.type === 'password';
        
          passwordInput.type = isPassword ? 'text' : 'password';
          toggleIcon.textContent = isPassword ? '🙈' : '👁️';
      }

      document.querySelector('form').addEventListener('submit', function(e) {
          const nova_senha = document.getElementById('nova_senha').value;
          const confirmar_senha = document.getElementById('confirmar_senha').value;
        
          if (nova_senha !== confirmar_senha) {
              e.preventDefault();
              alert('As senhas não conferem!');
          }
      });
      </script>
  </body>
  </html>