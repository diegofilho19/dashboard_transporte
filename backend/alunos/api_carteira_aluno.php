<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = "localhost";
$user = "root";
$password = "";
$database = "app_sistema";

$cpf = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST["cpf"] ?? "";
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $cpf = $_GET["cpf"] ?? "";
}

if (empty($cpf)) {
    echo json_encode(["error" => "CPF é obrigatório"]);
    exit;
}

try {
    $conn = new mysqli($host, $user, $password, $database);
    
    if ($conn->connect_error) {
        throw new Exception("Falha na conexão com o banco de dados");
    }

    $sql = "SELECT
                a.id,
                a.nome_completo AS nome_completo,
                a.cpf,
                a.matricula,
                a.numero_tel,
                f.nome AS faculdade,
                f.cidade AS cidade,
                af.id_fiscal,
                fc.nome AS motorista,
                fc.cnh,
                fc.numero AS numero_fiscal,
                fc.nome_carro AS carro,
                fc.placa,
                fc.destino,
                a.foto,
                a.turno,
                a.status AS ativo
            FROM alunos a
            LEFT JOIN faculdades f ON a.id_faculdade = f.id
            LEFT JOIN alunos_fiscais af ON a.id = af.id_aluno
            LEFT JOIN fiscais fc ON af.id_fiscal = fc.id
            WHERE a.cpf = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
        
        $aluno["ativo"] = trim(strtolower($aluno["ativo"])) === 'ativo';
        
        if (!empty($aluno["foto"])) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $fotoPath = "/sistema_dashboard/backend/alunos/uploads/fotoAluno/";
            
            // Verifica se o caminho já contém o diretório uploads/fotoAluno
            $fileName = basename($aluno["foto"]);
            $aluno["foto"] = $protocol . "://" . $host . $fotoPath . $fileName;
            
            error_log("URL da foto gerada: " . $aluno["foto"]);
        }
        
        echo json_encode($aluno);
    } else {
        echo json_encode(["error" => "Aluno não encontrado"]);
    }

    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Erro no servidor: " . $e->getMessage());
    echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
}