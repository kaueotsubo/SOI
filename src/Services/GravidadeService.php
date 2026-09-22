<?php

    class GravidadeService {
        private PDO $conexao;
        private GravidadeGateway $gateway;

        // Construtor
        public function __construct(PDO $conexao) {
            $this->conexao = $conexao;
            $this->gateway = new GravidadeGateway($conexao);
        }

        // Listar todas as gravidades
        public function listarGravidades(): array {
            return $this->gateway->listarGravidades();
        }

        // Buscar gravidade por ID
        public function buscarGravidadePorId(int $id): ?array {
            return $this->gateway->buscarGravidadePorId($id);
        }

    }