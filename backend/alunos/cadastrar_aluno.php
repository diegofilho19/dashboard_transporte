<?php
header("Content-Type: application/json");
require '../sistemas/config.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //error_log(print_r($_POST, true));
    $nome_completo = $_POST['nome_completo'];
    $cpf = $_POST['cpf'];
    $matricula = $_POST['matricula'];
    $numero_tel = $_POST['numero_tel'];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    $id_faculdade = $_POST['id_faculdade'];
    $curso = $_POST['curso'];
    $id_cidade = $_POST['id_cidade'];

    // Upload da foto
    $foto = "";
    if (!empty($_FILES["foto"]["name"])) {
        $tipos_permitidos = array('image/jpeg', 'image/png', 'image/jpg');
        $tamanho_maximo = 5 * 500 * 500; // 5MB

        if (in_array($_FILES["foto"]["type"], $tipos_permitidos) && $_FILES["foto"]["size"] <= $tamanho_maximo) {
            $foto_nome = uniqid() . "_" . basename($_FILES["foto"]["name"]);
            $foto_caminho = "uploads/" . $foto_nome;

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
    // Verifica se já existe um aluno com o mesmo CPF
    $sql_check = "SELECT cpf FROM alunos WHERE cpf = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $cpf);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(array('status' => 'error', 'message' => 'Já existe um aluno com este CPF.'));
    } else {
        $sql = "INSERT INTO alunos (nome_completo, cpf, matricula, numero_tel, senha, id_faculdade, curso, id_cidade, foto) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssisss", $nome_completo, $cpf, $matricula, $numero_tel, $senha, $id_faculdade, $curso, $id_cidade, $foto);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Aluno cadastrado com sucesso!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erro ao cadastrar aluno: " . $stmt->error]);
        }
    }

    $stmt_check->close();
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
