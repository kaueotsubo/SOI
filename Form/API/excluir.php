<?php
// api/excluir.php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['idDirecao'])) {
    echo json_encode(["success" => false, "error" => "Acesso negado. Apenas a direção pode excluir registros."]);
    exit();
}

$headers = apache_request_headers();
$tokenEnviado = isset($headers['X-CSRF-Token']) ? $headers['X-CSRF-Token'] : '';

if (empty($tokenEnviado) || !hash_equals($_SESSION['csrf_token'], $tokenEnviado)) {
    echo json_encode(["success" => false, "error" => "Ação bloqueada: Falha de segurança (CSRF)."]);
    exit();
}

require_once '../classe/config.php';

$id = $_GET['id'] ?? null;
$tudo = $_GET['tudo'] ?? null;
$response = ["success" => false];

try {
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM ocorrencia WHERE idOcorrencia = ?");
        $success = $stmt->execute([$id]);

        $response["success"] = $success;
        if (!$success) {
            $response["error"] = "Não foi possível excluir a ocorrência.";
        }
    } elseif ($tudo === "true") {
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

echo json_encode($response);
?>
