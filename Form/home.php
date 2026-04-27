<?php
session_start();

// Se não tiver logado OU se o cargo NÃO for direção, expulsa
if (!isset($_SESSION['idUsuario']) || $_SESSION['cargo'] !== 'direcao') {
    header("Location: index.php");
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
            <a href="registrarocorrencia.php" class="btn btn-verde">Registrar Ocorrência</a>
            <a href="lista.php" class="btn btn-verde">Ver lista de Ocorrência</a>
            <a href="importar_alunos.php" class="btn btn-verde">Importar Alunos (CSV)</a>
            <a href="status.html" class="btn btn-verde">Verificar status do aluno</a>
        </nav>
        <div class="d-flex justify-content-center mt-4 mb-5">
            <form action="api/logout.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit" class="btn btn-vermelho px-4 py-2">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </form>
        </div>
    </div>
</body>
</html>