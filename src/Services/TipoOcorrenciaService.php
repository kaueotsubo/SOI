<?php

class TipoOcorrenciaService {
    private PDO $conexao;
    private TipoOcorrenciaGateway $gateway;

    public function __construct(PDO $conexao) {
            $this->conexao = $conexao;
            $this->gateway = new TipoOcorrenciaGateway($conexao);
    }

    public function listarTiposOcorrencias(): array {
        return $this->gateway->listarTiposOcorrencias();
    }

    public function buscarTipoOcorrenciaPorId(int $id): ?array {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID inválido");
        }    
        return $this->gateway->buscarTipoOcorrenciaPorId($id);
    }

    public function cadastrarTipoOcorrencia(object $data): array {
        if (empty($data->nome)) {
            throw new InvalidArgumentException("Nome do tipo de ocorrência é obrigatório.");
        }

        if (empty($data->gravidade_inicial_id)) {
            throw new InvalidArgumentException("Gravidade inicial é obrigatória.");
        }

        $data->nome = trim($data->nome);
        
        if (mb_strlen($data->nome) < 3 || mb_strlen($data->nome) > 150) {
            throw new InvalidArgumentException("O nome deve ter entre 3 e 150 caracteres.");
        }

        if (!is_numeric($data->gravidade_inicial_id) || $data->gravidade_inicial_id <= 0) {
            throw new InvalidArgumentException("Gravidade inicial inválida.");
        }

        $data->gravidade_inicial_id = (int) $data->gravidade_inicial_id;

        if (!isset($data->ativo)) {
            $data->ativo = true;
        }

        $resultado = $this->gateway->cadastrarTipoOcorrencia($data);

        if (!$resultado) {
            throw new RuntimeException("Erro ao cadastrar tipo de ocorrência.");
        }

        return [
            'success' => true,
            'message' => 'Tipo de ocorrência cadastrado com sucesso.'
        ];
    }

    public function alterarTipoOcorrencia(object $data): array {
        if (empty($data->id)) {
            throw new InvalidArgumentException("ID do tipo de ocorrência é obrigatório.");
        }

        if (empty($data->nome)) {
            throw new InvalidArgumentException("Nome do tipo de ocorrência é obrigatório.");
        }

        if (empty($data->gravidade_inicial_id)) {
            throw new InvalidArgumentException("Gravidade inicial é obrigatória.");
        }

        $data->nome = trim($data->nome);

        if (mb_strlen($data->nome) < 3 || mb_strlen($data->nome) > 150) {
            throw new InvalidArgumentException("O nome deve ter entre 3 e 150 caracteres.");
        }

        if (!is_numeric($data->id) || $data->id <= 0) {
            throw new InvalidArgumentException("ID inválido.");
        }

        if (!is_numeric($data->gravidade_inicial_id) || $data->gravidade_inicial_id <= 0) {
            throw new InvalidArgumentException("Gravidade inicial inválida.");
        }

        $data->id = (int) $data->id;
        $data->gravidade_inicial_id = (int) $data->gravidade_inicial_id;

        $resultado = $this->gateway->alterarTipoOcorrencia($data);

        if (!$resultado) {
            throw new RuntimeException("Erro ao alterar tipo de ocorrência.");
        }

        return [
            'success' => true,
            'message' => 'Tipo de ocorrência alterado com sucesso.'
        ];
    }

    public function alterarStatusTipoOcorrencia(int $id, bool $ativo): array {
        if ($id <= 0) {
            throw new InvalidArgumentException("ID do tipo de ocorrência inválido.");
        }

        $resultado = $this->gateway
            ->alterarStatusTipoOcorrencia($id, $ativo);

        if (!$resultado) {
            throw new RuntimeException("Erro ao alterar status do tipo de ocorrência.");
        }

        return [
            'success' => true,
            'message' => $ativo
                ? 'Tipo de ocorrência ativado com sucesso.'
                : 'Tipo de ocorrência desativado com sucesso.'
        ];
    }
}