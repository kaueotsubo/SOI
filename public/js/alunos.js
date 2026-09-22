(async () => {
    const S = window.SOI;
    const cat = await S.carregarCatalogos();
    const modal = new bootstrap.Modal('#modalHistorico');

    const irParaNova = (a) => {
        location.href = 'nova-ocorrencia.php' + (a.matricula ? '?matricula=' + encodeURIComponent(a.matricula) : '');
    };

    async function abrirHistorico(aluno) {
        if (!aluno.id) {
            S.erro('Não foi possível identificar o aluno para consultar o histórico.');
            return;
        }
        S.$('#historicoTitulo').textContent = aluno.nome || 'Histórico do aluno';
        S.$('#historicoResumo').textContent = '';
        S.$('#historicoNova').href = 'nova-ocorrencia.php' + (aluno.matricula ? '?matricula=' + encodeURIComponent(aluno.matricula) : '');
        const corpo = S.$('#historicoCorpo');
        corpo.innerHTML = S.carregando('Carregando histórico…');
        modal.show();

        try {
            const brutas = await S.api('ocorrencia.php', { params: { aluno_id: aluno.id } }).then(S.lista);
            const ocorrencias = brutas.map((o) => S.normalizarOcorrencia(o, cat)).sort(S.porDataDesc);
            S.$('#historicoResumo').textContent =
                ocorrencias.length === 1 ? '1 ocorrência registrada' : `${ocorrencias.length} ocorrências registradas`;
            corpo.innerHTML = ocorrencias.length
                ? `<ul class="soi-hist">${ocorrencias.map(S.itemHistorico).join('')}</ul>`
                : S.estadoVazio('Nenhuma ocorrência registrada', 'Este aluno ainda não tem ocorrências.');
        } catch (e) {
            corpo.innerHTML = S.estadoErro(e.message);
        }
    }

    S.iniciarBuscaAluno({
        cat,
        acoes: [
            { rotulo: 'Ver histórico', classe: 'btn-outline-secondary', aoClicar: abrirHistorico },
            { rotulo: 'Registrar ocorrência', classe: 'btn-verde', aoClicar: irParaNova },
        ],
    });
})();
