<?php
session_start();
// api/logout.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    // Verifica se o token CSRF é válido antes de destruir a sessão
    if (!empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $tokenEnviado)) {
        session_unset();       
        session_destroy();     
    }
}

// Redireciona para a página de login após o logout
header("Location: ../index.php");
exit();
?>