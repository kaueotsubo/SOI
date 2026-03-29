<?php
session_start();

if (!isset($_SESSION['idUsuario']) || $_SESSION['cargo'] !== 'direcao') {
    header("Location: index.html");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png">
    <title>SOI - Importar Alunos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <header class="logo-nome-fixo">
        <img src="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png" alt="Logo IFAC">
        <span>SOI - Sistema de Ocorrência do Integrado</span>
    </header>

    <div class="container container-form mt-5 pt-5">
        <h2>Importar Planilha de Alunos (CSV)</h2>
        <p class="text-muted">Faça o upload de um arquivo .csv contendo as colunas nesta ordem: <b>Matrícula, Nome, CPF, Nome do Curso</b>.</p>
        
        <form action="API/processar_csv.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group mb-3">
                <label for="arquivo_csv">Arquivo CSV:</label>
                <input class="form-control" type="file" name="arquivo_csv" id="arquivo_csv" accept=".csv" required>
            </div>
            
            <button type="submit" class="btn btn-verde">Processar Planilha</button>
            <a href="home.php" class="btn btn-secondary" style="margin-left: 10px;">Voltar</a>
        </form>
    </div>
</body>
</html>