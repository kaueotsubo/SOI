<?php
require_once __DIR__ . '/includes/auth.php';
exigirLogin(); // Direção, professor e monitor

$tituloPagina = 'Registrar ocorrência';
$paginaAtual = 'nova';
$scriptsPagina = ['js/nova-ocorrencia.js'];
require __DIR__ . '/includes/header.php';
?>

<main class="soi-main container-xl" id="conteudo">
    <div class="soi-pagina-cabecalho">
        <div>
            <h1>Registrar ocorrência</h1>
            <p class="text-secondary mb-0">Primeiro escolha o aluno, depois preencha os detalhes.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <section class="soi-painel">
                <h2>Aluno</h2>

                <div id="alunoEscolhido" hidden>
                    <div class="soi-aluno soi-aluno-escolhido">
                        <div class="soi-avatar" id="escolhidoAvatar" aria-hidden="true"></div>
                        <div class="soi-aluno-dados">
                            <strong id="escolhidoNome"></strong>
                            <div class="soi-aluno-meta" id="escolhidoMeta"></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="trocarAluno">Trocar</button>
                    </div>
                </div>

                <div id="blocoBusca">
                    <?php require __DIR__ . '/includes/busca-aluno.php'; ?>
                </div>
            </section>
        </div>

        <div class="col-lg-7">
            <section class="soi-painel">
                <h2>Detalhes da ocorrência</h2>

                <div class="alert alert-warning" id="avisoSemTipos" hidden>
                    Não há tipos de ocorrência ativos. Peça à direção para cadastrar em "Tipos de ocorrência".
                </div>

                <form id="formOcorrencia" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="tipo">Tipo de ocorrência</label>
                            <select class="form-select" id="tipo" required>
                                <option value="" selected disabled>Selecione o tipo</option>
                            </select>
                            <div class="invalid-feedback">Selecione o tipo de ocorrência.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="gravidade">Gravidade</label>
                            <select class="form-select" id="gravidade" required>
                                <option value="" selected disabled>Selecione a gravidade</option>
                            </select>
                            <div class="invalid-feedback">Selecione a gravidade.</div>
                            <small class="text-secondary" id="dicaGravidade" hidden>Sugerida pelo tipo escolhido. Você pode alterar.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="data">Data do ocorrido</label>
                            <input type="date" class="form-control" id="data" required>
                            <div class="invalid-feedback">Informe uma data que não seja futura.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="relato">Relato</label>
                            <textarea class="form-control" id="relato" rows="6" minlength="10" maxlength="1000" required
                                placeholder="Descreva o fato com objetividade: o que aconteceu, onde e quem estava envolvido."></textarea>
                            <div class="d-flex justify-content-between">
                                <div class="invalid-feedback">Descreva a ocorrência com pelo menos 10 caracteres.</div>
                                <small class="text-secondary ms-auto" id="contador">0/1000</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-verde" id="btnRegistrar">Registrar ocorrência</button>
                        <a href="<?= e(paginaInicial()) ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
