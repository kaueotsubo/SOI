(async () => {
    const S = window.SOI;
    const POR_PAGINA = 15;

    const el = {
        busca: S.$('#fBusca'), gravidade: S.$('#fGravidade'), tipo: S.$('#fTipo'), situacao: S.$('#fSituacao'),
        de: S.$('#fDe'), ate: S.$('#fAte'), limpar: S.$('#fLimpar'),
        area: S.$('#areaTabela'), rodape: S.$('#rodape'), contagem: S.$('#contagem'), paginacao: S.$('#paginacao'),
        detalhe: S.$('#detalheOcorrencia'),
    };
    const modal = new bootstrap.Modal('#modalOcorrencia');

    let cat, todas = [], filtradas = [], pagina = 1;

    el.area.innerHTML = S.carregando('Carregando ocorrências…');

    try {
        const [catalogos, brutas] = await Promise.all([S.carregarCatalogos(), S.api('ocorrencia.php').then(S.lista)]);
        cat = catalogos;
        todas = brutas.map((o) => S.normalizarOcorrencia(o, cat)).sort(S.porDataDesc);
    } catch (e) {
        el.area.innerHTML = S.estadoErro(e.message);
        return;
    }

    // Opções dos filtros
    cat.gravidades.forEach((g) => el.gravidade.add(new Option(S.nomeDe(g), S.nomeDe(g))));
    cat.tipos.forEach((t) => el.tipo.add(new Option(S.nomeDe(t), S.nomeDe(t))));

    function filtrar() {
        const q = S.norm(el.busca.value);
        const de = el.de.value ? new Date(el.de.value + 'T00:00:00') : null;
        const ate = el.ate.value ? new Date(el.ate.value + 'T23:59:59') : null;

        filtradas = todas.filter((o) => {
            if (el.gravidade.value && S.norm(o.gravidade) !== S.norm(el.gravidade.value)) return false;
            if (el.tipo.value && S.norm(o.tipo) !== S.norm(el.tipo.value)) return false;
            if (el.situacao.value === 'ativas' && o.cancelada) return false;
            if (el.situacao.value === 'canceladas' && !o.cancelada) return false;
            if (de && (!o.data || o.data < de)) return false;
            if (ate && (!o.data || o.data > ate)) return false;
            if (q && !S.norm([o.alunoNome, o.alunoMatricula, o.tipo, o.descricao].join(' ')).includes(q)) return false;
            return true;
        });
        pagina = 1;
        desenhar();
    }

    function desenhar() {
        if (!filtradas.length) {
            el.rodape.hidden = true;
            el.area.innerHTML = todas.length
                ? S.estadoVazio('Nenhuma ocorrência com esses filtros', 'Ajuste ou limpe os filtros para ver mais resultados.')
                : S.estadoVazio('Nenhuma ocorrência registrada', 'As ocorrências criadas aparecerão aqui.');
            return;
        }

        const totalPaginas = Math.ceil(filtradas.length / POR_PAGINA);
        const inicio = (pagina - 1) * POR_PAGINA;
        const visiveis = filtradas.slice(inicio, inicio + POR_PAGINA);

        el.area.innerHTML = `
            <div class="table-responsive">
                <table class="table soi-tabela">
                    <thead><tr><th>Data</th><th>Aluno</th><th>Tipo</th><th>Gravidade</th><th>Descrição</th><th><span class="visually-hidden">Ações</span></th></tr></thead>
                    <tbody>${visiveis.map((o) => `
                        <tr data-id="${S.esc(o.id)}" class="${o.cancelada ? 'soi-cancelada' : ''}">
                            <td class="text-nowrap">${S.esc(o.dataTexto)}</td>
                            <td>${S.esc(o.alunoNome || (o.alunoId ? 'Aluno #' + o.alunoId : '–'))}</td>
                            <td>${S.esc(o.tipo || '–')}</td>
                            <td>${S.badgeGravidade(o.gravidade)} ${o.cancelada ? S.badgeCancelada() : ''}</td>
                            <td class="soi-truncar">${S.esc(o.descricao)}</td>
                            <td class="text-end"><button type="button" class="btn btn-sm btn-outline-secondary" data-detalhe="${S.esc(o.id)}">Detalhes</button></td>
                        </tr>`).join('')}
                    </tbody>
                </table>
            </div>`;

        el.rodape.hidden = false;
        el.contagem.textContent = `Mostrando ${inicio + 1} a ${inicio + visiveis.length} de ${filtradas.length}`;

        const item = (rotulo, alvo, desativado, ativo) =>
            `<li class="page-item ${desativado ? 'disabled' : ''} ${ativo ? 'active' : ''}"><button type="button" class="page-link" data-pagina="${alvo}" ${ativo ? 'aria-current="page"' : ''}>${rotulo}</button></li>`;
        let html = item('Anterior', pagina - 1, pagina === 1);
        const janela = [...new Set([1, pagina - 1, pagina, pagina + 1, totalPaginas])].filter((n) => n >= 1 && n <= totalPaginas).sort((a, b) => a - b);
        janela.forEach((n, i) => {
            if (i > 0 && n - janela[i - 1] > 1) html += '<li class="page-item disabled"><span class="page-link">…</span></li>';
            html += item(n, n, false, n === pagina);
        });
        html += item('Próxima', pagina + 1, pagina === totalPaginas);
        el.paginacao.innerHTML = html;
    }

    async function abrirDetalhe(id) {
        let o = todas.find((x) => String(x.id) === String(id));
        if (!o) return;
        mostrar(o);
        modal.show();
        // Tenta buscar o registro completo (a listagem pode vir resumida)
        try {
            const completo = S.normalizarOcorrencia(await S.api('ocorrencia.php', { params: { id } }), cat);
            const preenchidos = Object.fromEntries(Object.entries(completo).filter(([, v]) => v !== '' && v != null));
            mostrar({ ...o, ...preenchidos });
        } catch { /* mantém os dados da listagem */ }
    }

    function mostrar(o) {
        const linhas = [
            ['Aluno', o.alunoNome || (o.alunoId ? 'Aluno #' + o.alunoId : '')],
            ['Matrícula', o.alunoMatricula],
            ['Data', o.dataTexto],
            ['Tipo', o.tipo],
            ['Descrição', o.descricao],
            ['Registrada por', o.registradoPor],
            ['Situação', o.cancelada ? 'Cancelada' : ''],
            ['Motivo do cancelamento', o.motivoCancelamento],
        ].filter(([, v]) => v);
        el.detalhe.innerHTML = `<dl class="soi-detalhe">
            ${linhas.map(([r, v]) => `<dt>${S.esc(r)}</dt><dd>${S.esc(v)}</dd>`).join('')}
            <dt>Gravidade</dt><dd>${S.badgeGravidade(o.gravidade)}</dd>
        </dl>`;
    }

    // Eventos
    [el.gravidade, el.tipo, el.situacao, el.de, el.ate].forEach((c) => c.addEventListener('change', filtrar));
    el.busca.addEventListener('input', S.debounce(filtrar, 250));
    el.limpar.addEventListener('click', () => {
        S.$('#filtros').reset();
        filtrar();
    });
    S.$('#filtros').addEventListener('submit', (ev) => ev.preventDefault());

    el.area.addEventListener('click', (ev) => {
        const linha = ev.target.closest('tr[data-id]');
        if (linha) abrirDetalhe(linha.dataset.id);
    });
    el.paginacao.addEventListener('click', (ev) => {
        const b = ev.target.closest('[data-pagina]');
        if (!b || b.closest('.disabled')) return;
        pagina = +b.dataset.pagina;
        desenhar();
        el.area.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    filtrar();
})();
