<?php
require_once __DIR__ . '/includes/auth.php';
exigirLogin(); // Direção, professor e monitor

$tituloPagina = 'Alunos';
$paginaAtual = 'alunos';
$scriptsPagina = ['js/alunos.js'];
require __DIR__ . '/includes/header.php';
?>

<main class="soi-main container-xl" id="conteudo">
    <div class="soi-pagina-cabecalho">
        <div>
            <h1>Alunos</h1>
            <p class="text-secondary mb-0">Busque um aluno para ver o histórico ou registrar uma ocorrência.</p>
        </div>
    </div>

    <section class="soi-painel">
        <?php require __DIR__ . '/includes/busca-aluno.php'; ?>
    </section>
</main>

<div class="modal fade" id="modalHistorico" tabindex="-1" aria-labelledby="historicoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title fs-5" id="historicoTitulo">Histórico</h2>
                    <small class="text-secondary" id="historicoResumo"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="historicoCorpo"></div>
            <div class="modal-footer">
                <a class="btn btn-verde" id="historicoNova" href="nova-ocorrencia.php">Registrar ocorrência</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
