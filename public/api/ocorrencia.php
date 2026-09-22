<?php

require_once __DIR__ . '/_guard.php';
$usuario = apiExigirLogin(); // qualquer usuário logado; regras por ação abaixo

require_once __DIR__ . '/../../src/Config/conexao.php';
require_once __DIR__ . '/../../src/Gateway/OcorrenciaGateway.php';
require_once __DIR__ . '/../../src/Services/OcorrenciaService.php';
require_once __DIR__ . '/../../src/Gateway/AlunoGateway.php';
require_once __DIR__ . '/../../src/Services/AlunoService.php';

try {
    $conexao = Conexao::getInstance();
    $ocorrenciaService = new OcorrenciaService($conexao);
    $method = $_SERVER['REQUEST_METHOD'];
    $ehDirecao = $usuario['cargo'] === 'DIRECAO';

    // ---------- GET ----------
    if ($method === 'GET') {

        // Histórico de um aluno: qualquer usuário logado (usado ao registrar e consultar alunos)
        if (isset($_GET['aluno_id'])) {
            echo json_encode($ocorrenciaService->buscarOcorrenciasPorAluno((int) $_GET['aluno_id']));
            exit();
        }

        // Listagem geral e detalhe por ID: somente Direção
        if (!$ehDirecao) {
            http_response_code(403);
            echo json_encode(['error' => 'Você não tem permissão para esta ação.']);
            exit();
        }

        if (isset($_GET['id'])) {
            $ocorrencia = $ocorrenciaService->buscarOcorrenciaPorId((int) $_GET['id']);
            if ($ocorrencia) {
                echo json_encode($ocorrencia);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Ocorrência não encontrada']);
            }
            exit();
        }

        echo json_encode($ocorrenciaService->listarOcorrencias());
        exit();
    }

    // ---------- POST ----------
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            exit();
        }

        // Quem registrou vem sempre da sessão, nunca do navegador
        $data['registrado_por_id'] = $usuario['id'];

        // O curso vem do cadastro do aluno
        $alunoService = new AlunoService($conexao);
        $aluno = $alunoService->buscarAlunoPorId((int) ($data['aluno_id'] ?? 0));
        if (!$aluno) {
            http_response_code(404);
            echo json_encode(['error' => 'Aluno não encontrado']);
            exit();
        }
        $data['curso_id'] = (int) $aluno['curso_id'];

        // Data real, no formato AAAA-MM-DD e não futura
        $dataTexto = (string) ($data['data_ocorrencia'] ?? '');
        $dataOcorrencia = DateTime::createFromFormat('!Y-m-d', $dataTexto);
        if (!$dataOcorrencia || $dataOcorrencia->format('Y-m-d') !== $dataTexto || $dataOcorrencia > new DateTime('today')) {
            http_response_code(400);
            echo json_encode(['error' => 'Data da ocorrência inválida.']);
            exit();
        }
        if (empty($data['ano_letivo'])) {
            $data['ano_letivo'] = (int) $dataOcorrencia->format('Y');
        }

        $resultado = $ocorrenciaService->cadastrarOcorrencia($data);

        if ($resultado['success']) {
            http_response_code(201);
            echo json_encode(['message' => $resultado['message']]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => $resultado['message']]);
        }
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
