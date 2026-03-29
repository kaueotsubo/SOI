<?php
session_start();

if (!isset($_SESSION['idUsuario']) || $_SESSION['cargo'] !== 'assistente') {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png">
    <title>SOI - Sistema de Ocorrência do Integrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <header class="logo-nome-fixo">
        <img src="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png" alt="Logo IFAC">
        <span>SOI - Sistema de Ocorrência do Integrado</span>
    </header>

    <div class="container container-menu">
        <nav class="container-botoes" aria-label="Menu do Assistente">
            <a href="resgistraraluno.php" class="btn btn-verde">Registrar Ocorrência</a>
            <a href="API/logout.php" class="btn btn-vermelho">Sair</a>
        </nav>
    </div>
</body>
</html>