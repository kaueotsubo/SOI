<?php
session_start();

if (!isset($_SESSION['idUsuario']) || $_SESSION['cargo'] !== 'DIRECAO') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

require_once __DIR__ . '/../../src/Services/UsuarioService.php';
require_once __DIR__ . '/../../src/Gateway/UsuarioGateway.php';
require_once __DIR__ . '/../../src/Config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cadastro.php');
    exit();
}

try {
    // Verifica o token CSRF
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    if (empty($tokenEnviado) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenEnviado)) {
        header('Location: ../home.php?erro=' . urlencode('Sessão expirada. Recarregue a página e tente novamente.'));
        exit();
    }

    $conexao = Conexao::getInstance();
    $usuarioService = new UsuarioService($conexao);

    $usuarioData = (object) [
        'nome' => trim($_POST['nome'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'senha' => $_POST['senha'] ?? '',
        'confirmaSenha' => $_POST['confirmaSenha'] ?? '',
        'cargo' => trim($_POST['cargo'] ?? ''),
    ];

    $usuarioService->cadastrarUsuario($usuarioData);

    header('Location: ../home.php?sucesso=1');
    exit();

} catch (InvalidArgumentException $e) {
    // Mensagens dos Services ("E-mail já em uso.", "Senhas não coincidem." etc.) são seguras para exibir
    header('Location: ../home.php?erro=' . urlencode($e->getMessage()));
    exit();

} catch (Throwable $e) {
    error_log($e);
    header('Location: ../home.php?erro=1');
    exit();
}
