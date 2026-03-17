<?php
// api/get_ocorrencias.php
require_once '../classe/config.php'; // Puxando a conexão segura!

$sql = "
    SELECT 
        o.idOcorrencia,
        o.dataOcorrencia,
        o.descricao,
        a.nomeAluno,
        c.nomeCurso,
        g.nivel,
        t.nomeTipo,
        o.ano
    FROM ocorrencia o
    JOIN aluno a ON o.idAluno = a.idAluno
    JOIN curso c ON o.idCurso = c.idCurso
    JOIN gravidade g ON o.idGravidade = g.idGravidade
    JOIN tipo_ocorrencia t ON o.idTipoOcorrencia = t.idTipoOcorrencia
    ORDER BY o.dataOcorrencia DESC
";

$stmt = $pdo->query($sql);
$ocorrencia = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json");
echo json_encode($ocorrencia);
