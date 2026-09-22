<?php
// Autenticação e utilidades compartilhadas pelas páginas internas.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const CARGOS_VALIDOS = ['DIRECAO', 'PROFESSOR', 'MONITOR'];

/**
 * Exige usuário logado e, opcionalmente, um dos cargos informados.
 * Quem não tem permissão volta para a sua própria página inicial.
 */
function exigirLogin(array $cargosPermitidos = []): void
{
    $cargo = $_SESSION['cargo'] ?? '';

    if (!isset($_SESSION['idUsuario'])) {
        header('Location: index.php');
        exit();
    }

    // Cargo desconhecido: encerra a sessão para evitar loop de redirecionamento
    if (!in_array($cargo, CARGOS_VALIDOS, true)) {
        session_unset();
        session_destroy();
        header('Location: index.php?erro=1');
        exit();
    }

    if ($cargosPermitidos && !in_array($cargo, $cargosPermitidos, true)) {
        header('Location: ' . paginaInicial());
        exit();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function paginaInicial(): string
{
    return ($_SESSION['cargo'] ?? '') === 'DIRECAO' ? 'home.php' : 'homea.php';
}

function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
