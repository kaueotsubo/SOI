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
    <link href="css/soi.css" rel="stylesheet">
</head>

<body>
    <header class="soi-topo logo-nome-fixo">
        <div class="container-xxl">
            <div class="soi-brand">
                <img src="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png" alt="Logo IFAC">
                <span>SOI<small>Sistema de Ocorrência do Integrado</small></span>
            </div>
        </div>
    </header>

    <main class="soi-auth">
        <div class="container container-form soi-painel soi-auth-painel">
            <h1>Entrar</h1>
            <p class="soi-auth-ajuda">Use o e-mail e a senha cadastrados pela direção.</p>

            <form action="api/login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group mb-3">
                    <label class="form-label" for="email">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu e-mail"
                        autocomplete="username" autofocus required>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label" for="senha">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha"
                        autocomplete="current-password" required>
                </div>
                <button type="submit" name="entrar" class="btn btn-verde w-100">Entrar</button>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/index.js"></script>
</body>

</html>
