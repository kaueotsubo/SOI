<?php

class OcorrenciaGateway {
    private static $conn;

    // Configura a conexão PDO
    public static function setConnection(PDO $conn) {
        self::$conn = $conn;
    }

    // Buscar uma ocorrência pelo ID
    public function find($id, $class = 'stdClass') {
        $stmt = self::$conn->prepare("SELECT * FROM ocorrencia WHERE idOcorrencia = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject($class);
    }

    // Buscar todas ocorrências com filtro opcional
    public function all($filter = '', $class = 'stdClass') {
        $sql = "SELECT * FROM ocorrencia";
        if ($filter) {
            $sql .= " WHERE $filter";
        }
        $stmt = self::$conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, $class);
    }

    // Deletar ocorrência pelo ID
    public function delete($id) {
        $stmt = self::$conn->prepare("DELETE FROM ocorrencia WHERE idOcorrencia = ?");
        return $stmt->execute([$id]);
    }

    // Salvar ocorrência (INSERT ou UPDATE)
    public function save($data) {
        // Validação básica
        if (empty($data->idAluno) || empty($data->idCurso) || empty($data->idGravidade) || empty($data->idTipoOcorrencia) || empty($data->idUsuario)) {
            throw new Exception("Todos os campos obrigatórios (incluindo o usuário responsável) devem existir.");
        }

        // Valida foreign keys (Se a sua função validateForeignKey já existir no Gateway, ótimo!)
        $this->validateForeignKey('aluno', 'idAluno', $data->idAluno);
        $this->validateForeignKey('curso', 'idCurso', $data->idCurso);
        $this->validateForeignKey('gravidade', 'idGravidade', $data->idGravidade);
        $this->validateForeignKey('tipo_ocorrencia', 'idTipoOcorrencia', $data->idTipoOcorrencia);
        $this->validateForeignKey('usuario', 'idUsuario', $data->idUsuario); // Valida se o usuário existe

        if (empty($data->idOcorrencia)) {
            // INSERIR
            $stmt = self::$conn->prepare("
                INSERT INTO ocorrencia (idAluno, idCurso, idGravidade, idTipoOcorrencia, idUsuario, ano)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data->idAluno,
                $data->idCurso,
                $data->idGravidade,
                $data->idTipoOcorrencia,
                $data->idUsuario, // NOVO CAMPO
                $data->ano
            ]);
        } else {
            // ATUALIZAR
            $stmt = self::$conn->prepare("
                UPDATE ocorrencia SET 
                    idAluno = ?, 
                    idCurso = ?, 
                    idGravidade = ?, 
                    idTipoOcorrencia = ?,
                    idUsuario = ?,
                    ano = ?
                WHERE idOcorrencia = ?
            ");
            return $stmt->execute([
                $data->idAluno,         // Corrigido: Removido o $data->descricao que estava sobrando aqui
                $data->idCurso,
                $data->idGravidade,
                $data->idTipoOcorrencia,
                $data->idUsuario,       // NOVO CAMPO
                $data->ano,
                $data->idOcorrencia
            ]);
        }
    }

    // Retorna o último ID inserido
    public function getLastId() {
        return self::$conn->lastInsertId();
    }

    // Valida se um ID existe na tabela (foreign key)
    private function validateForeignKey($table, $column, $value) {
        $stmt = self::$conn->prepare("SELECT 1 FROM $table WHERE $column = ?");
        $stmt->execute([$value]);
        if ($stmt->rowCount() === 0) {
            throw new Exception("Valor inválido para $column: $value");
        }
    }
}

?>
