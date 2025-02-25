<?php
session_start();
require '../../backend/sistemas/config.php';

// Obtém o CPF do aluno da requisição POST
$cpf = $_POST['cpf'] ?? null;

if ($cpf) {
    // Atualiza os dados do aluno
    $nome_completo = $_POST['nome_completo'];
    $matricula = $_POST['matricula'];
    $numero_tel = $_POST['numero_tel'];
    $id_fiscal = $_POST['id_fiscal']; // ID do motorista selecionado
    $status = $_POST['status']; // Status do aluno

    // Atualiza os dados do aluno incluindo o status
    $update_sql = "UPDATE alunos SET nome_completo=?, matricula=?, numero_tel=?, status=? WHERE cpf=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssss", $nome_completo, $matricula, $numero_tel, $status, $cpf);

    if ($update_stmt->execute()) {
        // Verifique se o aluno já está associado ao fiscal antes de inserir
        $check_sql = "SELECT * FROM alunos_fiscais WHERE id_aluno = (SELECT id FROM alunos WHERE cpf = ?)";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $cpf);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            // Adiciona a associação do motorista ao aluno
            $associar_sql = "INSERT INTO alunos_fiscais (id_aluno, id_fiscal) VALUES ((SELECT id FROM alunos WHERE cpf = ?), ?)";
            $associar_stmt = $conn->prepare($associar_sql);
            $associar_stmt->bind_param("ss", $cpf, $id_fiscal);
            $associar_stmt->execute();
        } else {
            // Atualiza a associação do motorista ao aluno
            $associar_sql = "UPDATE alunos_fiscais SET id_fiscal = ? WHERE id_aluno = (SELECT id FROM alunos WHERE cpf = ?)";
            $associar_stmt = $conn->prepare($associar_sql);
            $associar_stmt->bind_param("ss", $id_fiscal, $cpf);
            $associar_stmt->execute();
        }

        $_SESSION['message'] = "Aluno atualizado com sucesso!";
        echo json_encode([
            'status' => 'success',
            'message' => 'Aluno editado com sucesso!'
        ]);
        exit;
    } else {
        $_SESSION['error'] = "Erro ao atualizar aluno.";
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao editar aluno: ' . $conn->error
        ]);
        exit;
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'CPF não fornecido.'
    ]);
    exit;
}
?>