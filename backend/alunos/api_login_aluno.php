<?php
// Configurações de cabeçalho
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Conectar ao banco de dados
$host = "localhost";
$user = "root";
$password = "";
$database = "app_sistema";

$conn = new mysqli($host, $user, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "error" => "Falha na conexão com o banco de dados: " . $conn->connect_error
    ]));
}

// Verificar se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obter dados da requisição
    $data = json_decode(file_get_contents("php://input"), true);

    // Se os dados foram enviados via form-data em vez de JSON
    if (empty($data)) {
        $cpf = isset($_POST["cpf"]) ? $_POST["cpf"] : null;
        $senha = isset($_POST["senha"]) ? $_POST["senha"] : null;
    } else {
        $cpf = isset($data["cpf"]) ? $data["cpf"] : null;
        $senha = isset($data["senha"]) ? $data["senha"] : null;
    }

    // Validar dados
    if (empty($cpf) || empty($senha)) {
        echo json_encode([
            "success" => false,
            "error" => "CPF e senha são obrigatórios"
        ]);
        exit;
    }

    // Formatar CPF (remover pontos e traços)
    $cpf_formatado = preg_replace('/[^0-9]/', '', $cpf);

    // Primeiro, recupere o aluno pelo CPF (independente da senha)
    $stmt = $conn->prepare("SELECT * FROM alunos WHERE cpf = ? OR cpf = ?");
    $stmt->bind_param("ss", $cpf, $cpf_formatado);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $aluno = $result->fetch_assoc();

        // Agora verifique a senha
        // Se a senha está armazenada com hash (começa com $2y$)
        if (substr($aluno["senha"], 0, 4) === '$2y$') {
            // Verifique usando password_verify
            $senha_correta = password_verify($senha, $aluno["senha"]);
        } else {
            // Compare diretamente (não recomendado, mas como fallback)
            $senha_correta = ($senha === $aluno["senha"]);
        }

        if ($senha_correta) {
            // Login bem-sucedido
            unset($aluno["senha"]); // Remover senha do resultado

            echo json_encode([
                "success" => true,
                "message" => "Login realizado com sucesso",
                "aluno" => $aluno
            ]);
        } else {
            // Senha incorreta
            echo json_encode([
                "success" => false,
                "error" => "CPF ou senha incorretos"
            ]);
        }
    } else {
        // Aluno não encontrado com esse CPF
        echo json_encode([
            "success" => false,
            "error" => "CPF ou senha incorretos"
        ]);
    }

    $stmt->close();
} else {
    // Método não permitido
    echo json_encode([
        "success" => false,
        "error" => "Método não permitido. Use POST."
    ]);
}

$conn->close();
