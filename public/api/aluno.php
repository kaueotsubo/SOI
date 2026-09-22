<?php

require_once __DIR__ . '/_guard.php';
apiExigirLogin(); // qualquer usuário logado

require_once __DIR__ . '/../../src/Config/conexao.php';
require_once __DIR__ . '/../../src/Services/AlunoService.php';
require_once __DIR__ . '/../../src/Gateway/AlunoGateway.php';

try {
    $conexao = Conexao::getInstance();
    $alunoService = new AlunoService($conexao);

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        exit();
    }

    if (isset($_GET['cpf'])) {
        $aluno = $alunoService->buscarAlunoPorCpf((string) $_GET['cpf']);
    } elseif (isset($_GET['matricula'])) {
        $aluno = $alunoService->buscarAlunoPorMatricula((string) $_GET['matricula']);
    } elseif (isset($_GET['nome'])) {
        // Retorna a lista (pode ser vazia)
        echo json_encode($alunoService->buscarAlunoPorNome((string) $_GET['nome']));
        exit();
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Parâmetro de busca não fornecido']);
        exit();
    }

    if ($aluno) {
        echo json_encode($aluno);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Aluno não encontrado']);
    }

} catch (InvalidArgumentException $e) {
    // Erros de validação dos Services (CPF, matrícula, nome inválidos)
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);

} catch (Throwable $e) {
    error_log($e);
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
}
