<?php
require_once __DIR__ . '/includes/auth.php';
exigirLogin(['DIRECAO']);

$tituloPagina = 'Tipos de ocorrência';
$paginaAtual = 'tipos';
$scriptsPagina = ['js/tipos-ocorrencia.js'];
require __DIR__ . '/includes/header.php';
?>

<main class="soi-main container-xl" id="conteudo">
    <div class="soi-pagina-cabecalho">
        <div>
            <h1>Tipos de ocorrência</h1>
            <p class="text-secondary mb-0">Tipos desativados deixam de aparecer ao registrar novas ocorrências.</p>
        </div>
        <button type="button" class="btn btn-verde" id="btnNovo">Novo tipo</button>
    </div>

    <section class="soi-painel p-0" aria-live="polite">
        <div id="areaTabela"></div>
    </section>
</main>

<div class="modal fade" id="modalTipo" tabindex="-1" aria-labelledby="modalTipoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" id="formTipo" novalidate>
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="modalTipoTitulo">Novo tipo de ocorrência</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tipoId">
                <div class="mb-3">
                    <label class="form-label" for="tipoNome">Nome</label>
                    <input type="text" class="form-control" id="tipoNome" minlength="3" maxlength="150" required
                        placeholder="Ex.: Uso indevido de celular">
                    <div class="invalid-feedback">O nome deve ter entre 3 e 150 caracteres.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tipoGravidade">Gravidade inicial</label>
                    <select class="form-select" id="tipoGravidade" required>
                        <option value="" selected disabled>Selecione a gravidade</option>
                    </select>
                    <div class="form-text">Aparece como sugestão ao registrar uma ocorrência deste tipo.</div>
                    <div class="invalid-feedback">Selecione a gravidade inicial.</div>
                </div>
                <div>
                    <label class="form-label" for="tipoDescricao">Descrição <span class="text-secondary fw-normal">(opcional)</span></label>
                    <textarea class="form-control" id="tipoDescricao" rows="3" maxlength="255"
                        placeholder="Quando este tipo deve ser usado"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-verde" id="btnSalvarTipo">Salvar</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
