<?php // Formulário de busca de aluno (usado em nova-ocorrencia.php e alunos.php). ?>
<form id="formBuscaAluno" class="soi-busca" role="search">
    <div class="row g-2">
        <div class="col-sm-4">
            <label class="form-label" for="buscaModo">Buscar por</label>
            <select class="form-select" id="buscaModo">
                <option value="nome">Nome</option>
                <option value="matricula">Matrícula</option>
                <option value="cpf">CPF</option>
            </select>
        </div>
        <div class="col-sm-8">
            <label class="form-label" for="buscaTermo">Aluno</label>
            <div class="input-group">
                <input type="search" class="form-control" id="buscaTermo" autocomplete="off"
                    placeholder="Digite pelo menos 3 letras do nome">
                <button class="btn btn-verde" type="submit">Buscar</button>
            </div>
        </div>
    </div>
</form>
<div id="buscaResultados" class="mt-3" aria-live="polite"></div>
