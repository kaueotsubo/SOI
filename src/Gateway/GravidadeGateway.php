<?php

    class GravidadeGateway {

        private PDO $conexao;

        public function __construct(PDO $conexao) {
            $this->conexao = $conexao;
        }

        // Listar todas as gravidades
        public function listarGravidades(): array {
            $sql = "SELECT * FROM gravidade";

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Buscar gravidade por ID
        public function buscarGravidadePorId(int $id): ?array {
            $sql = "SELECT * FROM gravidade WHERE id = :id";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $gravidade = $stmt->fetch(PDO::FETCH_ASSOC);

            return $gravidade ?: null;
        }
    }