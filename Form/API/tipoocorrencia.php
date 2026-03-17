<?php
// api/tipoocorrencia.php
require_once '../classe/config.php';

header("Content-Type: application/json");

if (isset($_GET['idGravidade']) && !empty($_GET['idGravidade'])) {
    $idGravidade = (int) $_GET['idGravidade'];
    $stmt = $pdo->prepare("SELECT idTipoOcorrencia, nomeTipo, descricao FROM tipo_ocorrencia WHERE idGravidade = ?");
    $stmt->execute([$idGravidade]);
    $tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Sucesso!
    http_response_code(200); 
    echo json_encode($tipo);
} else {
    // O JS não mandou o ID corretamente
    http_response_code(400); 
    echo json_encode(["error" => "O ID da gravidade não foi fornecido."]);
}
?>