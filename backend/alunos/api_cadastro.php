<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root";
$password = "";
$database = "app_sistema";

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Falha na conexão com o banco de dados"]));
}

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se todos os campos obrigatórios estão preenchidos
    if (!isset($_POST["nome_completo"], $_POST["cpf"], $_POST["matricula"], $_POST["numero_tel"], $_POST["senha"], $_POST["id_faculdade"], $_POST["curso"], $_POST["id_cidade"])) {
        echo json_encode(["error" => "Todos os campos são obrigatórios"]);
        exit;
    }

    // Pegar os dados do formulário
    $nome_completo = $_POST["nome_completo"];
    $cpf = $_POST["cpf"];
    $matricula = $_POST["matricula"];
    $numero_tel = $_POST["numero_tel"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); // Criptografar a senha
    $id_faculdade = $_POST["id_faculdade"];
    $curso = $_POST["curso"];
    $id_cidade = $_POST["id_cidade"];

    // Lidar com o upload da foto
    $foto = null;
    if (isset($_FILES["foto"])) {
        $foto_nome = time() . "_" . basename($_FILES["foto"]["name"]);
        $foto_caminho = "uploads/" . $foto_nome;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $foto_caminho)) {
            $foto = $foto_nome;
        } else {
            echo json_encode(["error" => "Erro ao enviar a foto"]);
            exit;
        }
    }
    
    // Verifica se o id_faculdade existe na tabela faculdades
    $sql_faculdade = "SELECT id FROM faculdades WHERE id = ?";
    $stmt_faculdade = $conn->prepare($sql_faculdade);
    $stmt_faculdade->bind_param("i", $id_faculdade);
    $stmt_faculdade->execute();
    $result_faculdade = $stmt_faculdade->get_result();

    if ($result_faculdade->num_rows === 0) {
        echo json_encode(["error" => "ID de faculdade inválido"]);
        exit;
    }

    // Inserir os dados no banco
    $sql = "INSERT INTO alunos (nome_completo, cpf, matricula, numero_tel, senha, id_faculdade, curso, id_cidade, foto) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $nome_completo, $cpf, $matricula, $numero_tel, $senha, $id_faculdade, $curso, $id_cidade, $foto);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Aluno cadastrado com sucesso!"]);
    } else {
        echo json_encode(["error" => "Erro ao cadastrar aluno"]);
    }

    $stmt->close();
}

    

$conn->close();
