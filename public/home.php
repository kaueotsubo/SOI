<?php
require_once __DIR__ . '/includes/auth.php';
exigirLogin(['DIRECAO']);

$tituloPagina = 'Painel da direção';
$paginaAtual = 'inicio';
$scriptsPagina = ['js/home.js'];
require __DIR__ . '/includes/header.php';
?>

<main class="soi-main container-xxl" id="conteudo">
    <div class="soi-pagina-cabecalho">
        <div>
            <h1>Painel da direção</h1>
            <p class="text-secondary mb-0">Resumo das ocorrências registradas no sistema.</p>
        </div>
        <a href="nova-ocorrencia.php" class="btn btn-verde">Registrar ocorrência</a>
    </div>

    <section class="soi-metricas" aria-label="Resumo geral">
        <div class="soi-metrica">
            <span class="soi-metrica-valor" id="mTotal">–</span>
            <span class="soi-metrica-rotulo">ocorrências registradas</span>
        </div>
        <div class="soi-metrica">
            <span class="soi-metrica-valor" id="mRecentes">–</span>
            <span class="soi-metrica-rotulo">nos últimos 30 dias</span>
        </div>
        <div class="soi-metrica">
            <span class="soi-metrica-valor" id="mGraves">–</span>
            <span class="soi-metrica-rotulo">graves ou gravíssimas</span>
        </div>
        <div class="soi-metrica">
            <span class="soi-metrica-valor" id="mTipos">–</span>
            <span class="soi-metrica-rotulo">tipos de ocorrência ativos</span>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="soi-painel">
                <div class="soi-painel-topo">
                    <h2>Últimas ocorrências</h2>
                    <a href="ocorrencias.php">Ver todas</a>
                </div>
                <div id="ultimas"></div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="soi-painel">
                <h2>Ocorrências por gravidade</h2>
                <div id="porGravidade"></div>
            </section>
            <section class="soi-painel">
                <h2>Tipos mais frequentes</h2>
                <div id="porTipo"></div>
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
