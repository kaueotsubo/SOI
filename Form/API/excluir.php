<?php
// api/excluir.php
require_once '../classe/config.php'; // Puxando a conexão segura!

// Pegando parâmetros da URL
$id = $_GET['id'] ?? null;
$tudo = $_GET['tudo'] ?? null;

$response = ["success" => false];

try {
    if ($id) {
        // Excluir apenas uma ocorrência
        $stmt = $pdo->prepare("DELETE FROM ocorrencia WHERE idOcorrencia = ?");
        $success = $stmt->execute([$id]);

        $response["success"] = $success;
        if (!$success) {
            $response["error"] = "Não foi possível excluir a ocorrência.";
        }
    } elseif ($tudo === "true") {
        // Excluir TODAS as ocorrências
        $stmt = $pdo->prepare("DELETE FROM ocorrencia");
        $success = $stmt->execute();

        $response["success"] = $success;
        if (!$success) {
            $response["error"] = "Não foi possível excluir todas as ocorrências.";
        }
    } else {
        $response["error"] = "Nenhum parâmetro válido fornecido.";
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

header("Content-Type: application/json");
echo json_encode($response);
