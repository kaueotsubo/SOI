<?php
require_once __DIR__ . "/../classe/usuario.php";
require_once __DIR__ . "/../classe/entidade.php";
require_once __DIR__ . "/../classe/gateway/usuarioGateway.php";

require_once '../classe/config.php';

$errorMessage = "";

try {
    usuarioGateway::setConnection($pdo);
    $usuarioGateway = new usuarioGateway;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['username'];
        // Limpa o e-mail para evitar quebrar o banco (SQL Injection)
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); 
        $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
        $role  = $_POST['role']; 

        // Valida os campos obrigatórios
        if (!empty($nome) && !empty($email) && !empty($senha) && !empty($role)) {

            $data = new stdClass();
            $data->nome = $nome;
            $data->email = $email;
            $data->senha = password_hash($senha, PASSWORD_BCRYPT);
            $data->cargo = $role;

            // Verifica existência
            $existente = $usuarioGateway->findByEmail($email); 
              
            if ($existente) {
                echo "<script>alert('Este e-mail de Usuário já está em uso.'); window.history.back();</script>";
                exit();
            } else {
                $usuarioGateway->save($data);
                echo "<script>alert('Registro concluído!'); window.location.href='../index.html';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Por favor, preencha todos os campos.'); window.history.back();</script>";
            exit();
        }

    }
} catch (Exception $e) {
    print "Erro: " . $e->getMessage();
}
?>