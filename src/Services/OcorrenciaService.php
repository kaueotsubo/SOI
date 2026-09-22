<?php

    class OcorrenciaService {
        private PDO $conexao;
        private OcorrenciaGateway $gateway;

        // Construtor
        public function __construct(PDO $conexao) {
            $this->conexao = $conexao;
            $this->gateway = new OcorrenciaGateway($conexao);
        }

        // Listar todas as ocorrências
        public function listarOcorrencias(): array {
            return $this->gateway->listarOcorrencias();
        }

        // Buscar ocorrência por ID
        public function buscarOcorrenciaPorId(int $id): ?array {
            return $this->gateway->buscarOcorrenciaPorId($id);
        }

        // Buscar ocorrências por aluno
        public function buscarOcorrenciasPorAluno(int $alunoId): array {
            return $this->gateway->buscarOcorrenciasPorAluno($alunoId);
        }

        // Cadastrar ocorrência
        public function cadastrarOcorrencia(array $dados): array {
            if (
                empty($dados['aluno_id']) ||
                empty($dados['curso_id']) ||
                empty($dados['tipo_ocorrencia_id']) ||
                empty($dados['gravidade_id']) ||
                empty($dados['registrado_por_id']) ||
                empty($dados['relato']) ||
                empty($dados['data_ocorrencia']) ||
                empty($dados['ano_letivo'])
            ) {
                return ['success' => false, 'message' => 'Campos obrigatórios não preenchidos'];
            }

            else if (
                !is_numeric($dados['aluno_id']) ||
                !is_numeric($dados['curso_id']) ||
                !is_numeric($dados['tipo_ocorrencia_id']) ||
                !is_numeric($dados['gravidade_id']) ||
                !is_numeric($dados['registrado_por_id'])
            ) {
                return ['success' => false, 'message' => 'IDs inválidos'];
            }

            else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dados['data_ocorrencia'])) {
                return ['success' => false, 'message' => 'Data de ocorrência inválida'];
            }

            else if (!is_numeric($dados['ano_letivo']) || strlen((string)$dados['ano_letivo']) !== 4) {
                return ['success' => false, 'message' => 'Ano letivo inválido'];
            }

            $dados['aluno_id'] = (int)$dados['aluno_id'];
            $dados['curso_id'] = (int)$dados['curso_id'];
            $dados['tipo_ocorrencia_id'] = (int)$dados['tipo_ocorrencia_id'];
            $dados['gravidade_id'] = (int)$dados['gravidade_id'];
            $dados['registrado_por_id'] = (int)$dados['registrado_por_id'];
            $dados['ano_letivo'] = (int)$dados['ano_letivo'];
            $dados['relato'] = trim($dados['relato']);

            $result = $this->gateway->cadastrarOcorrencia($dados);

            if (!$result) {
                return ['success' => false, 'message' => 'Erro ao cadastrar ocorrência'];
            }

            return ['success' => true, 'message' => 'Ocorrência cadastrada com sucesso'];
        }

        // Alterar ocorrência
        public function alterarOcorrencia(int $id, array $dados): array {
            if (
                empty($dados['aluno_id']) ||
                empty($dados['curso_id']) ||
                empty($dados['tipo_ocorrencia_id']) ||
                empty($dados['gravidade_id']) ||
                empty($dados['relato']) ||
                empty($dados['data_ocorrencia']) ||
                empty($dados['ano_letivo'])
            ) {
                return ['success' => false, 'message' => 'Campos obrigatórios não preenchidos'];
            }

            else if ($id <= 0) {
                return ['success' => false, 'message' => 'ID da ocorrência inválido'];
            }

            else if (
                !is_numeric($dados['aluno_id']) ||
                !is_numeric($dados['curso_id']) ||
                !is_numeric($dados['tipo_ocorrencia_id']) ||
                !is_numeric($dados['gravidade_id'])
            ) {
                return ['success' => false, 'message' => 'IDs inválidos'];
            }

            else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dados['data_ocorrencia'])) {
                return ['success' => false, 'message' => 'Data de ocorrência inválida'];
            }

            else if (!is_numeric($dados['ano_letivo']) || strlen((string)$dados['ano_letivo']) !== 4) {
                return ['success' => false, 'message' => 'Ano letivo inválido'];
            }

            $dados['aluno_id'] = (int)$dados['aluno_id'];
            $dados['curso_id'] = (int)$dados['curso_id'];
            $dados['tipo_ocorrencia_id'] = (int)$dados['tipo_ocorrencia_id'];
            $dados['gravidade_id'] = (int)$dados['gravidade_id'];
            $dados['ano_letivo'] = (int)$dados['ano_letivo'];
            $dados['relato'] = trim($dados['relato']);

            $result = $this->gateway->alterarOcorrencia($id, $dados);

            if (!$result) {
                return ['success' => false, 'message' => 'Erro ao alterar ocorrência'];
            }

            return ['success' => true, 'message' => 'Ocorrência alterada com sucesso'];
        }

        // Cancelar ocorrência
        public function cancelarOcorrencia(int $id, int $canceladaPorId, string $motivo): array {
            if ($id <= 0) {
                return ['success' => false, 'message' => 'ID da ocorrência inválido'];
            }

            else if ($canceladaPorId <= 0) {
                return ['success' => false, 'message' => 'Usuário responsável pelo cancelamento inválido'];
            }

            else if (empty(trim($motivo))) {
                return ['success' => false, 'message' => 'Motivo de cancelamento não fornecido'];
            }

            $motivo = trim($motivo);

            $result = $this->gateway->cancelarOcorrencia(
                $id,
                $canceladaPorId,
                $motivo
            );

            if (!$result) {
                return ['success' => false, 'message' => 'Erro ao cancelar ocorrência'];
            }

            return ['success' => true, 'message' => 'Ocorrência cancelada com sucesso'];
        }
    }