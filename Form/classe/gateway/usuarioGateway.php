<?php

class usuarioGateway {
    private static $conn;

    public static function setConnection (PDO $conn) {
        self::$conn = $conn; 
    }

    public function find ($id, $class = 'stdClass') {
        // Usando prepare para segurança na busca
        $stmt = self::$conn->prepare("SELECT * FROM usuario WHERE idUsuario = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject($class);
    }

    public function all ($filter, $class = 'stdClass') {
        $sql = "SELECT * FROM usuario";
        if ($filter) {
            $sql .= " WHERE $filter"; // Nota: Filtros dinâmicos exigem cuidado extra, mas para esse projeto base atende bem.
        }
        $result = self::$conn->query($sql);
        return $result->fetchAll(PDO::FETCH_CLASS, $class);
    }

    public function delete ($id) {
        $stmt = self::$conn->prepare("DELETE FROM usuario WHERE idUsuario = ?");
        return $stmt->execute([$id]);
    }

    public function save ($data) {
        // AQUI ESTÁ A MÁGICA DE SEGURANÇA (Prepared Statements)
        if (empty($data->idUsuario)) { // Inserir
            $stmt = self::$conn->prepare("INSERT INTO usuario (nome, email, senha, cargo) VALUES (?, ?, ?, ?)");
            return $stmt->execute([
                $data->nome, 
                $data->email, 
                $data->senha,
                $data->cargo
            ]);                
        }
        else { // Atualizar
            $stmt = self::$conn->prepare("UPDATE usuario SET nome = ?, email = ?, senha = ?, cargo = ? WHERE idUsuario = ?");
            return $stmt->execute([
                $data->nome, 
                $data->email, 
                $data->senha, 
                $data->cargo,
                $data->idUsuario
            ]);
        }
    }

    public function findByEmail($email) {
        $stmt = self::$conn->prepare("SELECT idUsuario FROM usuario WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna os dados se achar, ou false se não achar
    }

    public function getLastId() {
        $sql = "SELECT max(idUsuario) as max FROM usuario";
        $result = self::$conn->query($sql);
        $data = $result->fetch(PDO::FETCH_OBJ);
        return $data->max;
    }
}
?>