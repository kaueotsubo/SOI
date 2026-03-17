<?php
// api/cursos.php
require_once '../classe/config.php'; // Puxa a conexão do config.php

try {
    $stmt = $pdo->query("SELECT idCurso, nomeCurso FROM curso");
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200); // Ponto 2: Avisa o JS que deu tudo certo!
    header("Content-Type: application/json");
    echo json_encode($cursos);

} catch (PDOException $e) {
    http_response_code(500); // Avisa o JS que o banco falhou
    echo json_encode(["error" => "Erro ao buscar cursos."]);
}
?>