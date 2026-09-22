<?php
//padrão de projeto MVC, a classe de conexão com o banco de dados é 
// A classe utiliza o padrão Singleton para garantir
// que apenas uma instância de PDO seja criada e reutilizada
// durante a execução da aplicação.

// Conexao.php
class Conexao{
    // Atributo estático para armazenar a instância do PDO
    private static ?PDO $instance = null;

    //vazio para bloquear a instanciação direta da classe
    private function __construct()
    {
    }

    //vazio para bloquear a clonagem da instância
    private function __clone()
    {
    }


    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            $config = require_once __DIR__ . '/database_local.php';

            try {
                //DSN = Data source name 
                $dsn = "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'] . ";charset=utf8mb4";

                self::$instance = new PDO($dsn, $config['username'], $config['password']);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                http_response_code(500);
                die("Erro na conexão com o banco de dados.");
            }
        }

        return self::$instance;
    }
}