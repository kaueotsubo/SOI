<?php

class TipoOcorrenciaGateway {
    private PDO $conexao;

    public function __construct(PDO $conexao) {
        $this->conexao = $conexao;
    }

    //listar tipos de ocorrencias
    public function listarTiposOcorrencias(): array {
        $sql = "SELECT * FROM tipo_ocorrencia";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //buscar tipo de ocorrencia por id
    public function buscarTipoOcorrenciaPorId(int $id): ?array {
        $sql = "SELECT * FROM tipo_ocorrencia WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $tipoOcorrencia = $stmt->fetch(PDO::FETCH_ASSOC);
        return $tipoOcorrencia ?: null;
    }

    //cadastrar tipo de ocorrencia
    public function cadastrarTipoOcorrencia(object $data): bool {
        $sql = "INSERT INTO tipo_ocorrencia (nome, descricao, gravidade_inicial_id ,ativo) VALUES (:nome, :descricao, :gravidade_inicial_id, :ativo)";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':nome', $data->nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $data->descricao, PDO::PARAM_STR);
        $stmt->bindParam(':gravidade_inicial_id', $data->gravidade_inicial_id, PDO::PARAM_INT);
        $stmt->bindParam(':ativo', $data->ativo, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    //aletrar tipo de ocorrencia
    public function alterarTipoOcorrencia(object $data): bool {
        $sql = "UPDATE tipo_ocorrencia SET nome = :nome, descricao = :descricao, gravidade_inicial_id = :gravidade_inicial_id, ativo = :ativo WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':nome', $data->nome, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $data->descricao, PDO::PARAM_STR);
        $stmt->bindParam(':gravidade_inicial_id', $data->gravidade_inicial_id, PDO::PARAM_INT);
        $stmt->bindParam(':ativo', $data->ativo, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $data->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    //alterar status do tipo de ocorrencia
    public function alterarStatusTipoOcorrencia(int $id, bool $ativo): bool {
        $sql = "UPDATE tipo_ocorrencia SET ativo = :ativo WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':ativo', $ativo, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}