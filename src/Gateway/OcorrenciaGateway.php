<?php

class OcorrenciaGateway {

    private PDO $conexao;

    // Traz junto o nome/matrícula do aluno e o nome de quem registrou,
    // para as telas não precisarem de uma consulta extra por ocorrência.
    private const SELECT_BASE = "SELECT o.*,
                    a.nome AS aluno_nome,
                    a.matricula AS aluno_matricula,
                    u.nome AS registrado_por_nome
                FROM ocorrencia o
                LEFT JOIN aluno a ON a.id = o.aluno_id
                LEFT JOIN usuario u ON u.id = o.registrado_por_id";

    public function __construct(PDO $conexao) {
        $this->conexao = $conexao;
    }

    // Listar todas as ocorrências
    public function listarOcorrencias(): array {
        $sql = self::SELECT_BASE . " ORDER BY o.data_ocorrencia DESC, o.id DESC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar ocorrência por ID
    public function buscarOcorrenciaPorId(int $id): ?array {
        $sql = self::SELECT_BASE . " WHERE o.id = :id";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $ocorrencia = $stmt->fetch(PDO::FETCH_ASSOC);

        return $ocorrencia ?: null;
    }

    // Buscar ocorrências por aluno
    public function buscarOcorrenciasPorAluno(int $alunoId): array {
        $sql = self::SELECT_BASE . " WHERE o.aluno_id = :aluno_id ORDER BY o.data_ocorrencia DESC, o.id DESC";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':aluno_id', $alunoId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cadastrar ocorrência
    public function cadastrarOcorrencia(array $dados): bool {
        $sql = "INSERT INTO ocorrencia (
                    aluno_id,
                    curso_id,
                    tipo_ocorrencia_id,
                    gravidade_id,
                    registrado_por_id,
                    relato,
                    data_ocorrencia,
                    ano_letivo
                ) VALUES (
                    :aluno_id,
                    :curso_id,
                    :tipo_ocorrencia_id,
                    :gravidade_id,
                    :registrado_por_id,
                    :relato,
                    :data_ocorrencia,
                    :ano_letivo
                )";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':aluno_id' => $dados['aluno_id'],
            ':curso_id' => $dados['curso_id'],
            ':tipo_ocorrencia_id' => $dados['tipo_ocorrencia_id'],
            ':gravidade_id' => $dados['gravidade_id'],
            ':registrado_por_id' => $dados['registrado_por_id'],
            ':relato' => $dados['relato'],
            ':data_ocorrencia' => $dados['data_ocorrencia'],
            ':ano_letivo' => $dados['ano_letivo']
        ]);
    }

    // Alterar ocorrência
    public function alterarOcorrencia(int $id, array $dados): bool {
        $sql = "UPDATE ocorrencia SET
                    aluno_id = :aluno_id,
                    curso_id = :curso_id,
                    tipo_ocorrencia_id = :tipo_ocorrencia_id,
                    gravidade_id = :gravidade_id,
                    relato = :relato,
                    data_ocorrencia = :data_ocorrencia,
                    ano_letivo = :ano_letivo
                WHERE id = :id";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':aluno_id' => $dados['aluno_id'],
            ':curso_id' => $dados['curso_id'],
            ':tipo_ocorrencia_id' => $dados['tipo_ocorrencia_id'],
            ':gravidade_id' => $dados['gravidade_id'],
            ':relato' => $dados['relato'],
            ':data_ocorrencia' => $dados['data_ocorrencia'],
            ':ano_letivo' => $dados['ano_letivo'],
            ':id' => $id
        ]);
    }

    // Cancelar ocorrência
    public function cancelarOcorrencia(
        int $id,
        int $canceladaPorId,
        string $motivo
    ): bool {
        $sql = "UPDATE ocorrencia SET
                    status = 'CANCELADA',
                    cancelada_por_id = :cancelada_por_id,
                    cancelada_em = CURRENT_TIMESTAMP,
                    motivo_cancelamento = :motivo
                WHERE id = :id";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':cancelada_por_id' => $canceladaPorId,
            ':motivo' => $motivo,
            ':id' => $id
        ]);
    }
}
