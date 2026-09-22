<?php

require_once __DIR__ . '/_guard.php';
// Consultar: qualquer usuário logado. Criar, alterar e ativar/desativar: somente Direção.
apiExigirLogin(($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' ? [] : ['DIRECAO']);

require_once __DIR__ . '/../../src/Services/TipoOcorrenciaService.php';
require_once __DIR__ . '/../../src/Config/conexao.php';
require_once __DIR__ . '/../../src/Gateway/TipoOcorrenciaGateway.php';

try {
    $conexao = Conexao::getInstance();
    $tipoOcorrenciaService = new TipoOcorrenciaService($conexao);
    $method = $_SERVER['REQUEST_METHOD'];

    // ---------- GET ----------
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $tipoOcorrencia = $tipoOcorrenciaService->buscarTipoOcorrenciaPorId((int) $_GET['id']);
            if ($tipoOcorrencia) {
                echo json_encode($tipoOcorrencia);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Tipo de ocorrência não encontrado']);
            }
            exit();
        }

        echo json_encode($tipoOcorrenciaService->listarTiposOcorrencias());
        exit();
    }

    // ---------- POST ----------
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'));

        if (!is_object($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            exit();
        }

        $data->descricao = trim((string) ($data->descricao ?? ''));

        $resultado = $tipoOcorrenciaService->cadastrarTipoOcorrencia($data);

        http_response_code(201);
        echo json_encode(['success' => true, 'message' => $resultado['message']]);
        exit();
    }

    // ---------- PUT ----------
    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'));

        if (!is_object($data) || !isset($data->id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos ou ID ausente']);
            exit();
        }

        $data->descricao = trim((string) ($data->descricao ?? ''));

        // O UPDATE grava "ativo" junto: se não vier na requisição, preserva o valor atual
        if (!isset($data->ativo)) {
            $atual = $tipoOcorrenciaService->buscarTipoOcorrenciaPorId((int) $data->id);
            if (!$atual) {
                http_response_code(404);
                echo json_encode(['error' => 'Tipo de ocorrência não encontrado']);
                exit();
            }
            $data->ativo = filter_var($atual['ativo'], FILTER_VALIDATE_BOOLEAN);
        }
        $data->ativo = (bool) $data->ativo;

        $resultado = $tipoOcorrenciaService->alterarTipoOcorrencia($data);

        echo json_encode(['success' => true, 'message' => $resultado['message']]);
        exit();
    }

    // ---------- PATCH ----------
    if ($method === 'PATCH') {
        $data = json_decode(file_get_contents('php://input'));

        if (!is_object($data) || !isset($data->id) || !isset($data->ativo)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID ou status ausente']);
            exit();
        }

        $resultado = $tipoOcorrenciaService->alterarStatusTipoOcorrencia((int) $data->id, (bool) $data->ativo);

        echo json_encode(['success' => true, 'message' => $resultado['message']]);
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
