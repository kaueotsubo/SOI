<?php
require_once __DIR__ . '/includes/auth.php';
exigirLogin(['DIRECAO']); // só a direção cadastra usuários

$tituloPagina = 'Cadastrar usuário';
$paginaAtual = 'usuarios';
$scriptsPagina = ['js/script.js'];
require __DIR__ . '/includes/header.php';
?>

<main class="soi-main container-xl" id="conteudo">
    <div class="soi-pagina-cabecalho">
        <div>
            <h1>Cadastrar usuário</h1>
            <p class="text-secondary mb-0">Crie o acesso de um novo membro da direção, professor ou monitor.</p>
        </div>
    </div>

    <section class="soi-painel soi-form-estreito">
        <form id="registerForm" action="api/cadastrarUsuario.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="form-group mb-3">
                <label class="form-label" for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome completo"
                    autocomplete="off" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="nome@exemplo.com"
                    autocomplete="off" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="form-group col-md-6">
                    <label class="form-label" for="senha">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Crie uma senha"
                        autocomplete="new-password" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label" for="confirmaSenha">Confirmação de senha</label>
                    <input type="password" class="form-control" id="confirmaSenha" name="confirmaSenha"
                        placeholder="Repita a senha" autocomplete="new-password" required>
                </div>
            </div>
            <div class="form-group mb-4">
                <label class="form-label" for="cargo">Tipo de usuário</label>
                <select class="form-select" id="cargo" name="cargo" required>
                    <option value="" disabled selected>Selecione o tipo de usuário</option>
                    <option value="DIRECAO">Direção</option>
                    <option value="PROFESSOR">Professor</option>
                    <option value="MONITOR">Monitor</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-verde">Cadastrar usuário</button>
                <a href="home.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
