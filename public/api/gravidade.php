<?php

require_once __DIR__ . '/_guard.php';
apiExigirLogin(); // qualquer usuário logado

require_once __DIR__ . '/../../src/Services/GravidadeService.php';
require_once __DIR__ . '/../../src/Config/conexao.php';
require_once __DIR__ . '/../../src/Gateway/GravidadeGateway.php';

try {
    $conexao = Conexao::getInstance();
    $gravidadeService = new GravidadeService($conexao);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $gravidade = $gravidadeService->buscarGravidadePorId((int) $_GET['id']);
            if ($gravidade) {
                echo json_encode($gravidade);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Gravidade não encontrada']);
            }
            exit();
        }

        echo json_encode($gravidadeService->listarGravidades());
        exit();
    }

    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);

} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);

} catch (Throwable $e) {
    error_log($e);
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
}
