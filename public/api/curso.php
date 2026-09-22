<?php

require_once __DIR__ . '/_guard.php';
apiExigirLogin(); // qualquer usuário logado

require_once __DIR__ . '/../../src/Config/conexao.php';
require_once __DIR__ . '/../../src/Services/CursoService.php';
require_once __DIR__ . '/../../src/Gateway/CursoGateway.php';

try {
    $conexao = Conexao::getInstance();
    $cursoService = new CursoService($conexao);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id'])) {
            $curso = $cursoService->buscarCursoPorId((int) $_GET['id']);
            if ($curso) {
                echo json_encode($curso);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Curso não encontrado']);
            }
            exit();
        }

        echo json_encode($cursoService->listarCursos());
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
