<?php
// api/gravidade.php
require_once '../classe/config.php'; // Puxando a conexão segura!

$stmt = $pdo->query("SELECT idGravidade, nivel FROM gravidade");
$gravidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json");   
echo json_encode($gravidades);

?>