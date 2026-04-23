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
    <title>SOI - Sistema de Ocorrência do Integrado</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="logo-nome-fixo">
            <img src="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png" alt="Logo IFAC">
            <span>SOI - Sistema de Ocorrência do Integrado</span>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="col-md-6">
            <h2>Cadastro</h2>
            <form id="registerForm" action="api/register.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="username">Nome:</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Digite seu Nome"
                        required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Digite seu email"
                        required>
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" class="form-control" id="senha" name="senha"
                        placeholder="Digite sua senha" required>
                </div>
                <div class="form-group">
                    <label for="password">Confirmação de Senha:</label>
                    <input type="password" class="form-control" id="confpassword" name="confpassword"
                        placeholder="Digite sua senha novamente" required>
                </div>
                <div class="form-group">
                    <label for="role">Tipo de Usuário:</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="" disabled selected>Selecione o tipo de usuário</option>
                        <option value="direcao">Direção</option>
                        <option value="assistente">Assistente/Professor</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Cadastrar</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/script.js"></script>
</body>

</html>