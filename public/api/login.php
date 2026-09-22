<?php
session_start();

require_once __DIR__ . '/../../src/Services/UsuarioService.php';
require_once __DIR__ . '/../../src/Gateway/UsuarioGateway.php';
require_once __DIR__ . '/../../src/Config/conexao.php';

try {
    // Só aceita o envio do formulário de login
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['entrar'])) {
        header('Location: ../index.php');
        exit();
    }

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // Verifica o token CSRF
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    if (empty($tokenEnviado) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenEnviado)) {
        header('Location: ../index.php?erro=csrf');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../index.php?erro=1');
        exit();
    }

    $conexao = Conexao::getInstance();
    $usuarioService = new UsuarioService($conexao);
    $usuario = $usuarioService->loginUsuario($email, $senha);

    if (!$usuario) {
        // Senha incorreta ou usuário inativo
        header('Location: ../index.php?erro=1');
        exit();
    }

    session_regenerate_id(true); // descarta o ID antigo da sessão
    $_SESSION['idUsuario'] = $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['cargo'] = $usuario['cargo'];

    if ($usuario['cargo'] === 'DIRECAO') {
        header('Location: ../home.php');
    } elseif ($usuario['cargo'] === 'PROFESSOR' || $usuario['cargo'] === 'MONITOR') {
        header('Location: ../homea.php');
    } else {
        // Cargo não reconhecido: encerra a sessão
        session_unset();
        session_destroy();
        header('Location: ../index.php?erro=1');
    }
    exit();

} catch (Throwable $e) {
    error_log($e);
    header('Location: ../index.php?erro=1');
    exit();
}
