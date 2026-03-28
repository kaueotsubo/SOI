<?php
session_start();

if (!isset($_SESSION['idDirecao'])) {
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
        <nav class="container-botoes" aria-label="Menu Principal">
            <a href="resgistraraluno.php" class="btn btn-verde">Registrar Ocorrência</a>
            <a href="lista.php" class="btn btn-verde">Ver lista de Ocorrência</a>
            <a href="importar_alunos.php" class="btn btn-verde">Importar Alunos (CSV)</a>
            <a href="status.html" class="btn btn-verde">Verificar status do aluno</a>
        </nav>
        <div style="margin-top: 35px; width: 100%; text-align: center; border-top: 1px solid #e0e0e0; padding-top: 25px;">
            <a href="API/logout.php" class="btn btn-vermelho">Sair</a>
        </div>
    </div>
</body>
</html>