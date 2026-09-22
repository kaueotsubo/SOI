<?php

    class UsuarioService{

        private UsuarioGateway $usuarioGateway;
        private PDO $conexao;

        public function __construct(PDO $conexao) {
            $this->usuarioGateway = new UsuarioGateway($conexao);
            $this->conexao = $conexao;
        }

        //Listar todos os usuarios
        public function listarUsuarios(): array {
            return $this->usuarioGateway->listarUsuarios();
        }

        //buscar usuario por id
        public function buscarUsuarioPorId(int $id): ?array {
            return $this->usuarioGateway->buscarUsuarioPorId($id);
        }

        //buscar usuario por email
        public function buscarUsuarioPorEmail(string $email): ?array {
            return $this->usuarioGateway->buscarUsuarioPorEmail($email);
        }

        //alterar status do usuario
        public function alterarStatusUsuario(int $id, bool $ativo): bool {
            return $this->usuarioGateway->alterarStatusUsuario($id, $ativo);
        }

        //alterar usuario
        public function alterarUsuario(object $data): array {
            $result = $this->usuarioGateway->alterarUsuario($data);
            return ['success' => $result];
        }

        //login do usuario
        public function loginUsuario(string $email, string $senha): ?array {
            $usuario = $this->usuarioGateway->buscarUsuarioParaLogin($email);
            if ($usuario && $usuario['ativo'] && password_verify($senha, $usuario['senha_hash'])) {
                return $usuario;
            }
            return null;
        }

        //Cadastro do usuario
        public function cadastrarUsuario(object $data): array {
            //validação de campos obrigatórios
            if (empty($data->nome) || empty($data->email) || empty($data->senha) || empty($data->confirmaSenha) || empty($data->cargo)) {
                throw new InvalidArgumentException("Todos os campos são obrigatórios.");
            }

            // Valida o formato do e-mail
            if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("E-mail inválido.");
            }
        
            // Verifica se o e-mail já está em uso
            $existente = $this->usuarioGateway->buscarUsuarioPorEmail($data->email);
            if ($existente) {
                throw new InvalidArgumentException("E-mail já em uso.");
            }
            
            // Continua com o cadastro do usuário
            $senhaigual = $data->senha === $data->confirmaSenha;
            if (!$senhaigual) {
                throw new InvalidArgumentException("Senhas não coincidem.");
            }

            //Verifica se o cargo é válido
            $cargos_permitidos = ['DIRECAO', 'PROFESSOR', 'MONITOR'];
            if (!in_array($data->cargo, $cargos_permitidos, true)) {
                throw new InvalidArgumentException("Cargo inválido.");
            }

            // Hash da senha antes de salvar
            $senhaHash = password_hash($data->senha, PASSWORD_DEFAULT);
            $data->senha_hash = $senhaHash;

            // Continua com o cadastro do usuário
            $result = $this->usuarioGateway->cadastrarUsuario($data);

            if(!$result) {
                throw new RuntimeException("Erro ao cadastrar usuário");
            }

            return ['success' => $result, 'message' => 'Usuário cadastrado com sucesso'];
            
        }
}
