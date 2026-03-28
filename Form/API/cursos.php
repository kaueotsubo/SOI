<?php
// api/cursos.php
require_once '../classe/config.php'; // Puxa a conexão do config.php

try {
    $stmt = $pdo->query("SELECT idCurso, nomeCurso FROM curso");
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    header("Content-Type: application/json");
    echo json_encode($cursos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao buscar cursos."]);
}
?>