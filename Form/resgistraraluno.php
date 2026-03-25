<?php
session_start();

// Verifica sessão
if (!isset($_SESSION['idDirecao']) && !isset($_SESSION['idAssistente'])) {
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
    <link rel="icon" href="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png">
    <title>SOI - Sistema de Ocorrência do Integrado</title>
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <header class="logo-nome-fixo">
        <img src="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png" alt="Logo IFAC">
        <span>SOI - Sistema de Ocorrência do Integrado</span>
    </header>

    <div class="container container-form">
        <h2>Registrar Ocorrência</h2>
        <form action="API/ocorrencia.php" method="POST">
            
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" placeholder="Ex: 000.000.000-00" maxlength="14" required>
            </div>
            <div class="form-group">
                <label for="cursoid">Cursos:</label>
                <select name="cursoid" id="cursoid" required>
                    <option value="">Escolha um Curso</option>
                </select>
            </div>
            <div class="form-group">
                <label for="gravidadeid">Gravidades:</label>
                <select name="gravidadeid" id="gravidadeid" required>
                    <option value="">Escolha uma gravidade</option>
                </select>
            </div>
            <div class="form-group">
                <label for="ano">Ano:</label>
                <select name="ano" id="ano" required>
                    <option value="1">1°</option>
                    <option value="2">2°</option>
                    <option value="3">3°</option>
                </select>
            </div>
            <div class="form-group">
                <label for="tipoid">Tipo de Ocorrência:</label>
                <select name="tipoid" id="tipoid" required>
                    <option value="">Escolha o tipo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-verde">Registrar</button>
        </form>
    </div>

    <script src="js/api.js"></script>
</body>

</html>