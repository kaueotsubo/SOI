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
        if (empty($data->idAluno) || empty($data->idCurso) || empty($data->idGravidade) || empty($data->idTipoOcorrencia)) {
            throw new Exception("Todos os campos obrigatórios devem existir no banco de dados.");
        }

        // Valida foreign keys
        $this->validateForeignKey('aluno', 'idAluno', $data->idAluno);
        $this->validateForeignKey('curso', 'idCurso', $data->idCurso);
        $this->validateForeignKey('gravidade', 'idGravidade', $data->idGravidade);
        $this->validateForeignKey('tipo_ocorrencia', 'idTipoOcorrencia', $data->idTipoOcorrencia);

        // Pega descrição do tipo de ocorrência automaticamente
        $stmt = self::$conn->prepare("SELECT descricao FROM tipo_ocorrencia WHERE idTipoOcorrencia = ?");
        $stmt->execute([$data->idTipoOcorrencia]);
        $tipo = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$tipo) {
            throw new Exception("Tipo de ocorrência inválido.");
        }
        $data->descricao = $tipo->descricao;

        if (empty($data->idOcorrencia)) {
            // Inserir
            $stmt = self::$conn->prepare("
                INSERT INTO ocorrencia (descricao, idAluno, idCurso, idGravidade, idTipoOcorrencia, ano)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $data->descricao,
                $data->idAluno,
                $data->idCurso,
                $data->idGravidade,
                $data->idTipoOcorrencia,
                $data->ano
            ]);
        } else {
            // Atualizar
            $stmt = self::$conn->prepare("
                UPDATE ocorrencia SET 
                    descricao = ?, 
                    idAluno = ?, 
                    idCurso = ?, 
                    idGravidade = ?, 
                    idTipoOcorrencia = ?
                    ano = ?
                WHERE idOcorrencia = ?
            ");
            return $stmt->execute([
                $data->descricao,
                $data->idAluno,
                $data->idCurso,
                $data->idGravidade,
                $data->idTipoOcorrencia,
                $data->ano,
                $data->idOcorrencia
            ]);
        }
    }

    // Retorna o último ID inserido
    public function getLastId() {
        $stmt = self::$conn->query("SELECT MAX(idOcorrencia) as max FROM ocorrencia");
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data ? $data->max : null;
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
