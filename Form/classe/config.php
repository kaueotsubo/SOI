<?php
// config.php
$db_host = 'localhost';
$db_name = 'soi';
$db_user = 'root';
$db_pass = ''; // No InfinityFree, você mudará isso para as senhas deles

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Retorna erro 500 se o banco cair, sem vazar a senha na tela
    http_response_code(500);
    die(json_encode(["error" => "Erro interno de conexão com o banco de dados."]));
}
?>