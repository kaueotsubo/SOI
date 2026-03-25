<?php
session_start();

if (!isset($_SESSION['idDirecao'])) {
    header("Location: index.html");
    exit();
}

// GERA O TOKEN CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    
    <link rel="icon" href="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png">
    <title>SOI - Sistema de Ocorrência do Integrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <header class="logo-nome-fixo">
        <img src="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png" alt="Logo IFAC">
        <span>SOI - Sistema de Ocorrência do Integrado</span>
    </header>

    <div class="container my-5 pt-5 container-para-impressao">
        <h1 class="text-center mb-4">Lista de Ocorrências</h1>
        
        <div class="table-responsive">
            <table id="tabelaOcorrencias" class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Aluno</th>
                        <th>Curso</th>
                        <th>Gravidade</th>
                        <th>Tipo</th>
                        <th>Ano</th>
                        <th style="width: 120px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="d-flex justify-content-between mt-4">
                <a href="home.php" class="btn btn-secondary">Voltar</a>

                <button onclick="window.print()" class="btn btn-info">
                    <i class="fas fa-print"></i> Imprimir / Gerar PDF
                </button>

                <button onclick="confirmarExcluirTudo()" class="btn btn-excluir-tudo">
                    <i class="fas fa-trash-alt"></i> Excluir Tudo
                </button>
            </div>
        </div>
    </div>
    <script src="js/lista.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>