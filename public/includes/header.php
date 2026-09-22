<?php
// Espera as variáveis: $tituloPagina e $paginaAtual (definidas antes do require).
require_once __DIR__ . '/auth.php';

$cargo = $_SESSION['cargo'] ?? '';
$rotulosCargo = ['DIRECAO' => 'Direção', 'PROFESSOR' => 'Professor', 'MONITOR' => 'Monitor'];

if ($cargo === 'DIRECAO') {
    $menu = [
        'inicio'      => ['home.php', 'Início'],
        'ocorrencias' => ['ocorrencias.php', 'Ocorrências'],
        'nova'        => ['nova-ocorrencia.php', 'Nova ocorrência'],
        'alunos'      => ['alunos.php', 'Alunos'],
        'tipos'       => ['tipos-ocorrencia.php', 'Tipos de ocorrência'],
        'usuarios'    => ['cadastro.php', 'Cadastrar usuário'],
    ];
} else {
    $menu = [
        'inicio' => ['homea.php', 'Início'],
        'nova'   => ['nova-ocorrencia.php', 'Nova ocorrência'],
        'alunos' => ['alunos.php', 'Alunos'],
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($_SESSION['csrf_token']) ?>">
    <link rel="icon" href="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png">
    <title><?= e($tituloPagina ?? 'SOI') ?> - SOI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/soi.css" rel="stylesheet">
</head>

<body data-inicio="<?= e(paginaInicial()) ?>">
    <a class="visually-hidden-focusable soi-pular" href="#conteudo">Ir para o conteúdo</a>

    <nav class="navbar navbar-expand-lg soi-nav">
        <div class="container-xxl">
            <a class="navbar-brand soi-brand" href="<?= e(paginaInicial()) ?>">
                <img src="https://www.ifac.edu.br/o-ifac/comunicacao/copy2_of_logomarcas/logo_ifac_2.png" alt="Logo IFAC">
                <span>SOI<small>Sistema de Ocorrência do Integrado</small></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav me-auto mt-3 mt-lg-0 ms-lg-3">
                    <?php foreach ($menu as $chave => [$url, $rotulo]): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($paginaAtual ?? '') === $chave ? 'active' : '' ?>"
                                <?= ($paginaAtual ?? '') === $chave ? 'aria-current="page"' : '' ?>
                                href="<?= e($url) ?>"><?= e($rotulo) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="soi-usuario mt-3 mt-lg-0">
                    <div>
                        <div><?= e($_SESSION['nome'] ?? $_SESSION['email'] ?? '') ?></div>
                        <div class="cargo"><?= e($rotulosCargo[$cargo] ?? '') ?></div>
                    </div>
                    <form action="api/logout.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Sair</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
