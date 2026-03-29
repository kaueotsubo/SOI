<?php
// api/buscar_aluno.php
session_start();
header("Content-Type: application/json");

// Segurança básica
if (!isset($_SESSION['idUsuario'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "error" => "Acesso negado. Você precisa estar logado."]);
    exit();
}

require_once '../classe/config.php';

$cpf = $_GET['cpf'] ?? '';

if (empty($cpf) || strlen($cpf) !== 14) {
    echo json_encode(["success" => false, "error" => "CPF inválido."]);
    exit();
}

try {
    // Procura o aluno e junta com a tabela curso
    $sql = "SELECT a.nomeAluno, c.nomeCurso 
            FROM aluno a 
            JOIN curso c ON a.idCurso = c.idCurso 
            WHERE a.cpf = ? AND a.status = 'ativo'";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cpf]);
    
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aluno) {
        echo json_encode([
            "success" => true, 
            "nome" => $aluno['nomeAluno'], 
            "curso" => $aluno['nomeCurso']
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Aluno não encontrado."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => "Erro no servidor."]);
}
?>