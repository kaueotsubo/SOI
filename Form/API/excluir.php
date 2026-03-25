<?php
// api/excluir.php
session_start();
header("Content-Type: application/json");

// 1. PROTEÇÃO DE ACESSO: Verifica se é a DIREÇÃO (Apenas diretor pode excluir)
if (!isset($_SESSION['idDirecao'])) {
    echo json_encode(["success" => false, "error" => "Acesso negado. Apenas a direção pode excluir registros."]);
    exit();
}

// 2. VERIFICAÇÃO DO CARIMBO (Token CSRF via JavaScript)
$headers = apache_request_headers();
// O JavaScript mandou o token com o nome 'X-CSRF-Token'
$tokenEnviado = isset($headers['X-CSRF-Token']) ? $headers['X-CSRF-Token'] : '';

if (empty($tokenEnviado) || !hash_equals($_SESSION['csrf_token'], $tokenEnviado)) {
    echo json_encode(["success" => false, "error" => "Ação bloqueada: Falha de segurança (CSRF)."]);
    exit();
}

// 3. AGORA SIM, EXCLUI COM SEGURANÇA
require_once '../classe/config.php'; // Puxando a conexão segura!

$id = $_GET['id'] ?? null;
$tudo = $_GET['tudo'] ?? null;
$response = ["success" => false];

try {
    // Usando Prepared Statements (Muito bom, te protege contra SQL Injection!)
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