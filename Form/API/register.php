<?php
require_once __DIR__ . "/../classe/direcao.php";
require_once __DIR__ . "/../classe/entidade.php";
require_once __DIR__ . "/../classe/gateway/direcaoGateway.php"; 
require_once __DIR__ . "/../classe/assistente.php";
require_once __DIR__ . "/../classe/gateway/assistenteGateway.php";

require_once '../classe/config.php';

$errorMessage = "";

try {
    direcaoGateway::setConnection($pdo);
    assistenteGateway::setConnection($pdo);

    $direcaoGateway = new direcaoGateway;
    $assistenteGateway = new assistenteGateway;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['username'];
        // Limpa o e-mail para evitar quebrar o banco (SQL Injection)
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); 
        $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
        $role  = $_POST['role']; 

        if ($role === 'direcao') {
            $data = new stdClass();
            $data->nomeDirecao = $nome;
            $data->emailDirecao = $email;
            $data->senhaDirecao = password_hash($senha, PASSWORD_BCRYPT);

            // Verifica existência
            $existente = $direcaoGateway->all("emailDirecao = '{$email}'");
            if (count($existente) > 0) {
                // Em vez de tela branca, avisa o usuário e volta
                echo "<script>alert('Este e-mail de Direção já está em uso.'); window.history.back();</script>";
                exit();
            } else {
                $direcaoGateway->save($data);
                echo "<script>alert('Registro concluído!'); window.location.href='../index.html';</script>";
                exit();
            }

        } elseif ($role === 'assistente') {
            $data = new stdClass();
            $data->nomeAssistente = $nome;
            $data->emailAssistente = $email;
            $data->senhaAssistente = password_hash($senha, PASSWORD_BCRYPT);

            $existente = $assistenteGateway->all("emailAssistente = '{$email}'");
            if (count($existente) > 0) {
                echo "<script>alert('Este e-mail de Assistente já está em uso.'); window.history.back();</script>";
                exit();
            } else {
                $assistenteGateway->save($data);
                echo "<script>alert('Registro concluído!'); window.location.href='../index.html';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Por favor, preencha todos os campos obrigatórios.'); window.history.back();</script>";
            exit();
        }
    }
} catch (Exception $e) {
    print "Erro: " . $e->getMessage();
}
?>