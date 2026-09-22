<?php

    class AlunoService{

        private PDO $conexao;
        private AlunoGateway $alunoGateway;

        public function __construct(PDO $conexao){
            $this->alunoGateway = new AlunoGateway($conexao);
            $this->conexao = $conexao;
        }

        //buscar aluno por CPF
        public function buscarAlunoPorCpf(string $cpf): ?array {
            if (empty($cpf)) {
                throw new InvalidArgumentException("O CPF não pode ser vazio.");
            }
            
            else if (!preg_match('/^\d{11}$/', $cpf)) {
                throw new InvalidArgumentException("O CPF deve conter exatamente 11 dígitos numéricos.");
            }

            else if (preg_match('/(\d)\1{10}/', $cpf)) {
                throw new InvalidArgumentException("O CPF não pode ter todos os dígitos iguais.");
            }

            return $this->alunoGateway->buscarAlunoPorCpf($cpf);
        }

        //buscar aluno por nome
        public function buscarAlunoPorNome(string $nome): array {
            $nome = trim($nome);

            if (empty($nome)) {
                throw new InvalidArgumentException("O nome não pode ser vazio.");
            }

            else if (strlen($nome) < 3) {
                throw new InvalidArgumentException("O nome deve ter pelo menos 3 caracteres.");
            }

            else if (strlen($nome) > 100) {
                throw new InvalidArgumentException("O nome não pode ter mais de 100 caracteres.");
            }

            else if (!preg_match('/^[\p{L}\s]+$/u', $nome)) {
                throw new InvalidArgumentException("O nome deve conter apenas letras e espaços.");
            }

            return $this->alunoGateway->buscarAlunoPorNome($nome);
        }

        //buscar aluno por matricula
        public function buscarAlunoPorMatricula(string $matricula): ?array {
            if (empty($matricula)) {
                throw new InvalidArgumentException("A matrícula não pode ser vazia.");
            }

            else if (!preg_match('/^\d{19}$/', $matricula)) {
                throw new InvalidArgumentException("A matrícula deve conter exatamente 19 dígitos numéricos.");
            }

            return $this->alunoGateway->buscarAlunoPorMatricula($matricula);
        }

        //buscar aluno por ID
        public function buscarAlunoPorId(int $id): ?array {
            if ($id <= 0) {
                throw new InvalidArgumentException("O ID do aluno é inválido.");
            }

            return $this->alunoGateway->buscarAlunoPorId($id);
        }

        //Cadastrar um novo aluno
        public function save(object $data): array {

            if (
                empty($data->nome) ||
                empty($data->cpf) ||
                empty($data->matricula) ||
                empty($data->curso_id)
            ) {
                throw new InvalidArgumentException("Todos os campos são obrigatórios.");
            }

            // Validar nome
            else if (!preg_match('/^[\p{L}\s]+$/u', $data->nome)) {
                throw new InvalidArgumentException("O nome deve conter apenas letras e espaços.");
            }

            else if (strlen($data->nome) < 3) {
                throw new InvalidArgumentException("O nome deve ter pelo menos 3 caracteres.");
            }

            else if (strlen($data->nome) > 100) {
                throw new InvalidArgumentException("O nome não pode ter mais de 100 caracteres.");
            }

            // Validar CPF
            else if (!preg_match('/^\d{11}$/', $data->cpf)) {
                throw new InvalidArgumentException("O CPF deve conter exatamente 11 dígitos numéricos.");
            }

            else if (preg_match('/(\d)\1{10}/', $data->cpf)) {
                throw new InvalidArgumentException("O CPF não pode ter todos os dígitos iguais.");
            }

            // Validar matrícula
            else if (!preg_match('/^\d{19}$/', $data->matricula)) {
                throw new InvalidArgumentException("A matrícula deve conter exatamente 19 dígitos numéricos.");
            }

            // Validar curso
            else if (!is_numeric($data->curso_id) || $data->curso_id <= 0) {
                throw new InvalidArgumentException("Curso inválido.");
            }

            // Verificar CPF duplicado
            else if ($this->alunoGateway->buscarAlunoPorCpf($data->cpf)) {
                throw new InvalidArgumentException("CPF já cadastrado.");
            }

            // Verificar matrícula duplicada
            else if ($this->alunoGateway->buscarAlunoPorMatricula($data->matricula)) {
                throw new InvalidArgumentException("Matrícula já cadastrada.");
            }

            $resultado = $this->alunoGateway->save($data);

            if (!$resultado) {
                throw new RuntimeException("Falha ao salvar o aluno.");
            }

            return [
                'success' => true,
                'message' => 'Aluno salvo com sucesso.'
            ];
        }
    }