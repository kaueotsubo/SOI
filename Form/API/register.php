<?php
session_start();
require_once __DIR__ . "/../classe/usuario.php";
require_once __DIR__ . "/../classe/entidade.php";
require_once __DIR__ . "/../classe/gateway/usuarioGateway.php";

require_once '../classe/config.php';

//inicio funcao emitirAlerta()
function emitirAlerta($icone, $titulo, $texto, $redirecionarPara = null){
    $acao = $redirecionarPara ? "window.location.href = '$redirecionarPara';" : "window.history.back();";
    
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Aviso - SOI</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>body { background-color: #f8f9fa; }</style> </head>
    <body>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '$icone', // 'success', 'error', 'warning', etc.
                    title: '$titulo',
                    text: '$texto',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d6efd',
                    allowOutsideClick: false // Obriga a clicar no botão para sair
                }).then((result) => {
                    if (result.isConfirmed) {
                        $acao
                    }
                });
            });
        </script>
    </body>
    </html>
    ";
    exit();
}
//fim da funcao emitirAlerta()

$errorMessage = "";

try {
    usuarioGateway::setConnection($pdo);
    $usuarioGateway = new usuarioGateway;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se o token CSRF é válido
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        if (empty($tokenEnviado) || !hash_equals($_SESSION['csrf_token'], $tokenEnviado)) {
            emitirAlerta('warning', 'Sessão Expirada', 'Sua sessão expirou por inatividade. Por favor, tente novamente.');
            exit();
        }

        $nome = $_POST['username'];
        // Limpa o e-mail para evitar quebrar o banco (SQL Injection)
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); 
        $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
        $role  = $_POST['role'];
        $confirmarSenha = isset($_POST['confpassword']) ? $_POST['confpassword'] : '';

        if ($senha !== $confirmarSenha){
            emitirAlerta('error', 'Ops!', 'As senhas não coincidem. Tente novamente.');
            exit();
        }
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
                emitirAlerta('error', 'E-mail indisponível', 'Este e-mail de usuário já está em uso.');
                exit();
            } else {
                $usuarioGateway->save($data);
                emitirAlerta('success', 'Tudo certo!', 'Registro concluído com sucesso.', '../index.php');
                exit();
            }
        } else {
            emitirAlerta('warning', 'Campos Vazios', 'Por favor, preencha todos os campos corretamente.');
            exit();
        }


    }
} catch (Exception $e) {
    print "Erro: " . $e->getMessage();
}
?>