<?php
require_once __DIR__ . '/includes/auth.php';
exigirLogin(['PROFESSOR', 'MONITOR']);

$tituloPagina = 'Início';
$paginaAtual = 'inicio';
$scriptsPagina = [];
require __DIR__ . '/includes/header.php';
?>

<main class="soi-main container-xxl" id="conteudo">
    <div class="soi-pagina-cabecalho">
        <div>
            <h1>O que você quer fazer?</h1>
            <p class="text-secondary mb-0">Registre uma ocorrência ou consulte o histórico de um aluno.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <a class="soi-atalho" href="nova-ocorrencia.php">
                <h2>Registrar ocorrência</h2>
                <p>Escolha o aluno, o tipo e a gravidade e descreva o que aconteceu.</p>
            </a>
        </div>
        <div class="col-md-6">
            <a class="soi-atalho" href="alunos.php">
                <h2>Consultar aluno</h2>
                <p>Busque por nome, matrícula ou CPF e veja as ocorrências já registradas.</p>
            </a>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
