<?php
// api/get_ocorrencias.php
session_start();

if (!isset($_SESSION['idUsuario']) || $_SESSION['cargo'] !== 'direcao') {
    http_response_code(403);
    echo json_encode(["success" => false, "error" => "Acesso negado. Apenas a direção pode realizar esta ação."]);
    exit();
}

require_once '../classe/config.php'; // Puxando a conexão segura!

$sql = "
    SELECT 
        o.idOcorrencia,
        o.dataOcorrencia,
        a.nomeAluno,
        c.nomeCurso,
        g.nivel,
        t.nomeTipo,
        o.ano,
        u.nome AS nomeUsuario
    FROM ocorrencia o
    JOIN aluno a ON o.idAluno = a.idAluno
    JOIN curso c ON o.idCurso = c.idCurso
    JOIN gravidade g ON o.idGravidade = g.idGravidade
    JOIN tipo_ocorrencia t ON o.idTipoOcorrencia = t.idTipoOcorrencia
    JOIN usuario u ON o.idUsuario = u.idUsuario
    ORDER BY o.dataOcorrencia DESC
";

try {
    $stmt = $pdo->query($sql);
    $ocorrencia = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header("Content-Type: application/json");
    echo json_encode($ocorrencia);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Erro no banco: " . $e->getMessage()]);
}
