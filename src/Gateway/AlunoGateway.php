<?php

class AlunoGateway{
    private PDO $conexao;

    public function __construct(PDO $conexao){
        $this->conexao = $conexao;
    }

    //buscar aluno por CPF
    public function buscarAlunoPorCpf(string $cpf): ?array {
        $sql = "SELECT * FROM aluno WHERE cpf = :cpf";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':cpf', $cpf, PDO::PARAM_STR);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aluno ?: null;
    }

    //buscar aluno por nome
    public function buscarAlunoPorNome(string $nome): array {
        $sql = "SELECT * FROM aluno WHERE nome LIKE :nome";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':nome' => '%' . $nome . '%']);
        $aluno = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $aluno ?: [];
    }

    //buscar aluno por matricula
    public function buscarAlunoPorMatricula(string $matricula): ?array {
        $sql = "SELECT * FROM aluno WHERE matricula = :matricula";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':matricula', $matricula, PDO::PARAM_STR);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aluno ?: null;
    }

    //buscar aluno por ID
    public function buscarAlunoPorId(int $id): ?array {
        $sql = "SELECT * FROM aluno WHERE id = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aluno ?: null;
    }

    //Cadastrar um novo alluno
    public function save (object $data):bool {
        if (empty($data->id)) { // Inserir
            $sql = "INSERT INTO aluno (nome, cpf, matricula, curso_id) VALUES (?, ?, ?, ?)";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                $data->nome, 
                $data->cpf, 
                $data->matricula,
                $data->curso_id
            ]);                                            
        }
        else { // Atualizar
            $sql = "UPDATE aluno SET nome = ?, cpf = ?, matricula = ?, curso_id = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                $data->nome, 
                $data->cpf, 
                $data->matricula, 
                $data->curso_id,
                $data->id
            ]);
        }
    }
}