<?php
// Certifique-se de que não há espaços ou caracteres antes da tag PHP de abertura
header("Content-Type: application/json");
// Suprimir qualquer saída de erro para garantir apenas JSON na resposta
error_reporting(0);

require '../sistemas/config.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //error_log(print_r($_POST, true));
    $nome_completo = $_POST['nome_completo'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $matricula = $_POST['matricula'] ?? '';
    $numero_tel = $_POST['numero_tel'] ?? '';
    $senha = isset($_POST["senha"]) ? password_hash($_POST["senha"], PASSWORD_DEFAULT) : '';
    $id_faculdade = $_POST['id_faculdade'] ?? '';
    $curso = $_POST['curso'] ?? '';
    $id_cidade = $_POST['id_cidade'] ?? '';
    $turno = $_POST['turno'] ?? '';
    
    // Validar dados obrigatórios
    if (empty($nome_completo) || empty($cpf) || empty($matricula) || empty($senha) || empty($id_faculdade)) {
        echo json_encode(array('status' => 'error', 'message' => 'Todos os campos obrigatórios devem ser preenchidos.'));
        exit;
    }

    // Upload da foto
    $foto = "";
    if (!empty($_FILES["foto"]["name"])) {
        $tipos_permitidos = array('image/jpeg', 'image/png', 'image/jpg');
        $tamanho_maximo = 5 * 500 * 500; // 5MB

        if (in_array($_FILES["foto"]["type"], $tipos_permitidos) && $_FILES["foto"]["size"] <= $tamanho_maximo) {
            $foto_nome = uniqid() . "_" . basename($_FILES["foto"]["name"]);
            $foto_caminho = "uploads/fotoAluno/" . $foto_nome;
            
            // Verificar se o diretório existe, se não, criar
            if (!is_dir("uploads/fotoAluno/")) {
                mkdir("uploads/fotoAluno/", 0755, true);
            }

            if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $foto_caminho)) {
                $response = array('status' => 'error', 'message' => 'Erro ao fazer upload da foto.');
                echo json_encode($response);
                exit; // Encerra a execução
            }

            $foto = $foto_caminho;
        } else {
            $response = array('status' => 'error', 'message' => 'Arquivo de foto inválido.');
            echo json_encode($response);
            exit;
        }
    }

    //Upload dos comprovantes de matricula
    $compMatricula = "";
    if (!empty($_FILES["compMatricula"]["name"])) {
        $tipos_permitidos = array('image/jpeg', 'image/png', 'image/jpg');
        $tamanho_maximo = 10 * 1024 * 1024; // 10MB

        if (in_array($_FILES["compMatricula"]["type"], $tipos_permitidos) && $_FILES["compMatricula"]["size"] <= $tamanho_maximo) {
            $compMatricula_nome = uniqid() . "_" . basename($_FILES["compMatricula"]["name"]);
            $compMatricula_caminho = "uploads/comprovantes/" . $compMatricula_nome;
            
            // Verificar se o diretório existe, se não, criar
            if (!is_dir("uploads/comprovantes/")) {
                mkdir("uploads/comprovantes/", 0755, true);
            }

            if (!move_uploaded_file($_FILES["compMatricula"]["tmp_name"], $compMatricula_caminho)) {
                echo json_encode(array('status' => 'error', 'message' => 'Erro ao fazer upload do comprovante de matrícula.'));
                exit; // Encerra a execução
            }

            $compMatricula = $compMatricula_caminho;
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Arquivo de comprovante inválido.'));
            exit;
        }
    }
    
    // Verifica se já existe um aluno com o mesmo CPF
    $sql_check = "SELECT cpf FROM alunos WHERE cpf = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $cpf);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Já existe um aluno com este CPF.'));
    } else {
        $sql = "INSERT INTO alunos (nome_completo, cpf, matricula, numero_tel, senha, id_faculdade, curso, id_cidade, foto, turno, compMatricula) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssisssss", $nome_completo, $cpf, $matricula, $numero_tel, $senha, $id_faculdade, $curso, $id_cidade, $foto, $turno, $compMatricula);

        if ($stmt->execute()) {
            echo json_encode(array('status' => 'success', 'message' => 'Aluno cadastrado com sucesso!'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Erro ao cadastrar aluno: ' . $stmt->error));
        }
    }

    $stmt_check->close();
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
} else {
    // Se não for método POST, retorna erro
    echo json_encode(array('status' => 'error', 'message' => 'Método não permitido.'));
}