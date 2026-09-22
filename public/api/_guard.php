<?php
/**
 * Proteção comum das APIs (arquivo de apoio, não é um endpoint).
 *
 *  - exige sessão ativa (401 se não houver);
 *  - restringe por cargo, quando informado (403);
 *  - nas requisições que alteram dados (POST/PUT/PATCH/DELETE) confere o token CSRF
 *    enviado pelo front no cabeçalho X-CSRF-Token (403).
 *
 * Retorna ['id' => int, 'cargo' => string] do usuário logado.
 */
function apiResponderErro(int $codigo, string $mensagem): void
{
    http_response_code($codigo);
    echo json_encode(['error' => $mensagem]);
    exit();
}

function apiExigirLogin(array $cargosPermitidos = []): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Content-Type: application/json; charset=utf-8');

    $id = $_SESSION['idUsuario'] ?? null;
    $cargo = $_SESSION['cargo'] ?? '';

    if ($id === null || $cargo === '') {
        apiResponderErro(401, 'Sessão expirada. Faça login novamente.');
    }

    if ($cargosPermitidos && !in_array($cargo, $cargosPermitidos, true)) {
        apiResponderErro(403, 'Você não tem permissão para esta ação.');
    }

    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (!in_array($metodo, ['GET', 'HEAD', 'OPTIONS'], true)) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            apiResponderErro(403, 'Token de segurança inválido. Recarregue a página e tente novamente.');
        }
    }

    // Libera o bloqueio da sessão: várias chamadas da mesma página rodam em paralelo
    session_write_close();

    return ['id' => (int) $id, 'cargo' => $cargo];
}
