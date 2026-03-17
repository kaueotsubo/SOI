<?php

class AssistenteGateway {
    private static $conn;

    public static function setConnection (PDO $conn) {
        self::$conn = $conn; 
    }
    // A função find é simples, mas usa prepared statements para segurança
    public function find ($id, $class = 'stdClass') {
        // Usando prepare para segurança
        $stmt = self::$conn->prepare("SELECT * FROM assistente WHERE idAssistente = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject($class);
    }
    // A função all é um pouco mais complexa, pois permite um filtro dinâmico. Cuidado ao usar filtros dinâmicos, mas para esse projeto base atende bem.
    public function all ($filter, $class = 'stdClass') {
        $sql = "SELECT * FROM assistente";
        if ($filter) {
            $sql .= " WHERE $filter";
        }
        $result = self::$conn->query($sql);
        return $result->fetchAll(PDO::FETCH_CLASS, $class);
    }
    // A função delete é simples, mas também usa prepared statements para segurança
    public function delete ($id) {
        $stmt = self::$conn->prepare("DELETE FROM assistente WHERE idAssistente = ?");
        return $stmt->execute([$id]);
    }

    public function save ($data) {
        // Blindagem contra SQL Injection
        if (empty($data->idAssistente)) { // Inserir
            $stmt = self::$conn->prepare("INSERT INTO assistente (nomeAssistente, emailAssistente, senhaAssistente) VALUES (?, ?, ?)");
            return $stmt->execute([
                $data->nomeAssistente, 
                $data->emailAssistente, 
                $data->senhaAssistente
            ]);                
        }
        else { // Atualizar
            $stmt = self::$conn->prepare("UPDATE assistente SET nomeAssistente = ?, emailAssistente = ?, senhaAssistente = ? WHERE idAssistente = ?");
            return $stmt->execute([
                $data->nomeAssistente, 
                $data->emailAssistente, 
                $data->senhaAssistente, 
                $data->idAssistente
            ]);
        }
    }

    public function getLastId() {
        $sql = "SELECT max(idAssistente) as max FROM assistente";
        $result = self::$conn->query($sql);
        $data = $result->fetch(PDO::FETCH_OBJ);
        return $data->max;
    }
}
?>