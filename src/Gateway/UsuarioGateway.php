<?php

    class UsuarioGateway{
    
        private PDO $conexao;

        public function __construct(PDO $conexao){
            $this->conexao = $conexao;
        }

        //Listar todos os usuarios
        public function listarUsuarios(): array {
            $sql = "SELECT id, nome, email, cargo, ativo, criado_em, atualizado_em FROM usuario";
            $stmt = $this->conexao->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //buscar usuario por id
        public function buscarUsuarioPorId(int $id): ?array {
            $sql = "SELECT id, nome, email, cargo, ativo, criado_em, atualizado_em FROM usuario WHERE id = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        }

        //buscar usuario por email
        public function buscarUsuarioPorEmail(string $email): ?array {
            $sql = "SELECT id, nome, email, cargo, ativo, criado_em, atualizado_em FROM usuario WHERE email = :email";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        }

        //buscar usuario por email para login
        public function buscarUsuarioParaLogin(string $email): ?array {
            $sql = "SELECT id, nome, email, senha_hash, cargo, ativo FROM usuario WHERE email = :email";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        }

        //alterar usuario
        public function alterarUsuario(object $data): bool {
            $sql = "UPDATE usuario SET nome = ?, email = ?, senha_hash = ?, cargo = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                $data->nome, 
                $data->email, 
                $data->senha_hash,
                $data->cargo,
                $data->id
            ]);
        }

        //alterar status do usuario
        public function alterarStatusUsuario(int $id, bool $ativo): bool {
            $sql = "UPDATE usuario SET ativo = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([$ativo, $id]);
        }

        //Cadastrar um novo usuario
        public function cadastrarUsuario(object $data): bool {
            $sql = "INSERT INTO usuario (nome, email, senha_hash, cargo) VALUES (?, ?, ?, ?)";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                $data->nome, 
                $data->email, 
                $data->senha_hash,
                $data->cargo
            ]);
        }

    }