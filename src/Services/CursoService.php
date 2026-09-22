<?php

    class CursoService {
        private CursoGateway $cursoGateway;
        private PDO $conexao;

        public function __construct(PDO $conexao) {
            $this->conexao = $conexao;
            $this->cursoGateway = new CursoGateway($conexao);
        }

        //Lista todos os cursos disponíveis no banco de dados
        public function listarCursos(): array {
            return $this->cursoGateway->listarCursos();
        }

        //Busca um curso específico pelo ID
        public function buscarCursoPorId(int $id): ?array {
            return $this->cursoGateway->buscarCursoPorId($id);
        }
    }