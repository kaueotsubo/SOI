<?php
session_start();
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
    <title>SOI - Sistema de Ocorrência do Integrado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <header class="logo-nome-fixo">
        <img src="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png" alt="Logo IFAC">
        <span>SOI - Sistema de Ocorrência do Integrado</span>
    </header>

    <div class="container container-form">
        <h2>Login</h2>
        <form action="api/login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="text" id="email" name="email" placeholder="Digite seu email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" name="entrar" class="btn btn-verde" style="margin-top: 15px;">Login</button>
        </form>
    </div>
    <script src="/script.js"></script>
</body>

</html>