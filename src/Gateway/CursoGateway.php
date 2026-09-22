<?php

class CursoGateway {
    private PDO $conexao;

    public function __construct(PDO $conexao) {
        $this->conexao = $conexao;
    }

    //Lista todos os cursos disponíveis no banco de dados
    public function listarCursos(): array {
        $sql = "SELECT * FROM curso where ativo = TRUE";
        $stmt = $this->conexao->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Busca um curso específico pelo ID
    public function buscarCursoPorId(int $id): ?array {
        $sql = "SELECT * FROM curso WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $curso = $stmt->fetch(PDO::FETCH_ASSOC);
        return $curso ?: null;
    }
}

