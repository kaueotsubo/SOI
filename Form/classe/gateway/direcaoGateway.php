<?php

class DirecaoGateway {
    private static $conn;

    public static function setConnection (PDO $conn) {
        self::$conn = $conn; 
    }

    public function find ($id, $class = 'stdClass') {
        // Usando prepare para segurança na busca
        $stmt = self::$conn->prepare("SELECT * FROM direcao WHERE idDirecao = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject($class);
    }

    public function all ($filter, $class = 'stdClass') {
        $sql = "SELECT * FROM direcao";
        if ($filter) {
            $sql .= " WHERE $filter"; // Nota: Filtros dinâmicos exigem cuidado extra, mas para esse projeto base atende bem.
        }
        $result = self::$conn->query($sql);
        return $result->fetchAll(PDO::FETCH_CLASS, $class);
    }

    public function delete ($id) {
        $stmt = self::$conn->prepare("DELETE FROM direcao WHERE idDirecao = ?");
        return $stmt->execute([$id]);
    }

    public function save ($data) {
        // AQUI ESTÁ A MÁGICA DE SEGURANÇA (Prepared Statements)
        if (empty($data->idDirecao)) { // Inserir
            $stmt = self::$conn->prepare("INSERT INTO direcao (nomeDirecao, emailDirecao, senhaDirecao) VALUES (?, ?, ?)");
            return $stmt->execute([
                $data->nomeDirecao, 
                $data->emailDirecao, 
                $data->senhaDirecao
            ]);                
        }
        else { // Atualizar
            $stmt = self::$conn->prepare("UPDATE direcao SET nomeDirecao = ?, emailDirecao = ?, senhaDirecao = ? WHERE idDirecao = ?");
            return $stmt->execute([
                $data->nomeDirecao, 
                $data->emailDirecao, 
                $data->senhaDirecao, 
                $data->idDirecao
            ]);
        }
    }

    public function getLastId() {
        $sql = "SELECT max(idDirecao) as max FROM direcao";
        $result = self::$conn->query($sql);
        $data = $result->fetch(PDO::FETCH_OBJ);
        return $data->max;
    }
}
?>