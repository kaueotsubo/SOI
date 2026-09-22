<?php
require_once __DIR__ . '/includes/auth.php';
exigirLogin(['DIRECAO']);

$tituloPagina = 'Ocorrências';
$paginaAtual = 'ocorrencias';
$scriptsPagina = ['js/ocorrencias.js'];
require __DIR__ . '/includes/header.php';
?>

<main class="soi-main container-xxl" id="conteudo">
    <div class="soi-pagina-cabecalho">
        <div>
            <h1>Ocorrências</h1>
            <p class="text-secondary mb-0">Consulte e filtre todas as ocorrências registradas.</p>
        </div>
        <a href="nova-ocorrencia.php" class="btn btn-verde">Registrar ocorrência</a>
    </div>

    <section class="soi-painel mb-4" aria-label="Filtros">
        <form id="filtros" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="fBusca">Buscar</label>
                <input type="search" class="form-control" id="fBusca" placeholder="Aluno, matrícula, tipo ou relato">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label" for="fGravidade">Gravidade</label>
                <select class="form-select" id="fGravidade"><option value="">Todas</option></select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label" for="fTipo">Tipo</label>
                <select class="form-select" id="fTipo"><option value="">Todos</option></select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label" for="fSituacao">Situação</label>
                <select class="form-select" id="fSituacao">
                    <option value="ativas" selected>Somente ativas</option>
                    <option value="canceladas">Somente canceladas</option>
                    <option value="">Todas</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label" for="fDe">De</label>
                <input type="date" class="form-control" id="fDe">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label" for="fAte">Até</label>
                <input type="date" class="form-control" id="fAte">
            </div>
            <div class="col-12 col-md-auto">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="fLimpar">Limpar filtros</button>
            </div>
        </form>
    </section>

    <section class="soi-painel p-0" aria-live="polite">
        <div id="areaTabela"></div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-top" id="rodape" hidden>
            <small class="text-secondary" id="contagem"></small>
            <nav aria-label="Paginação"><ul class="pagination pagination-sm mb-0" id="paginacao"></ul></nav>
        </div>
    </section>
</main>

<div class="modal fade" id="modalOcorrencia" tabindex="-1" aria-labelledby="modalOcorrenciaTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalOcorrenciaTitulo">Detalhes da ocorrência</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="detalheOcorrencia"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
