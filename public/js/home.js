(async () => {
    const S = window.SOI;
    S.avisoPorQuery();

    const ids = ['#ultimas', '#porGravidade', '#porTipo'];
    ids.forEach((id) => (S.$(id).innerHTML = S.carregando()));

    function barras(itens, classeFixa) {
        if (!itens.length) return S.estadoVazio('Sem dados ainda', 'As ocorrências registradas aparecerão aqui.');
        const maximo = Math.max(...itens.map((i) => i.total));
        return '<ul class="soi-barras">' + itens.map((i) => `
            <li>
                <div class="soi-barra-rotulo"><span>${S.esc(i.nome)}</span><strong>${i.total}</strong></div>
                <div class="soi-barra"><span class="soi-barra-fill ${classeFixa || S.classeGravidade(i.nome)}" style="--pct:${(i.total / maximo) * 100}%"></span></div>
            </li>`).join('') + '</ul>';
    }

    try {
        const [cat, brutas] = await Promise.all([
            S.carregarCatalogos(),
            S.api('ocorrencia.php').then(S.lista),
        ]);
        // Ocorrências canceladas não entram nos números do painel
        const ocorrencias = brutas.map((o) => S.normalizarOcorrencia(o, cat)).filter((o) => !o.cancelada).sort(S.porDataDesc);

        // Métricas
        const limite = new Date();
        limite.setDate(limite.getDate() - 30);
        S.$('#mTotal').textContent = ocorrencias.length;
        S.$('#mRecentes').textContent = ocorrencias.filter((o) => o.data && o.data >= limite).length;
        S.$('#mGraves').textContent = ocorrencias.filter((o) => {
            const c = S.classeGravidade(o.gravidade);
            return c === 'grav-grave' || c === 'grav-gravissima';
        }).length;
        S.$('#mTipos').textContent = cat.tipos.filter(S.estaAtivo).length;

        // Últimas ocorrências
        const ultimas = ocorrencias.slice(0, 6);
        S.$('#ultimas').innerHTML = ultimas.length
            ? '<ul class="soi-lista">' + ultimas.map((o) => `
                <li>
                    <div>
                        <strong>${S.esc(o.alunoNome || (o.alunoId ? 'Aluno #' + o.alunoId : 'Aluno não informado'))}</strong>
                        <span class="sub">${S.esc(o.tipo || 'Tipo não informado')} &nbsp;${S.esc(o.dataTexto)}</span>
                    </div>
                    ${S.badgeGravidade(o.gravidade)}
                </li>`).join('') + '</ul>'
            : S.estadoVazio('Nenhuma ocorrência registrada', 'Use "Registrar ocorrência" para criar a primeira.');

        // Por gravidade (na ordem cadastrada) e por tipo (top 5)
        const contar = (chave) => ocorrencias.reduce((m, o) => m.set(o[chave] || 'Não informado', (m.get(o[chave] || 'Não informado') || 0) + 1), new Map());

        const porGrav = contar('gravidade');
        const ordem = cat.gravidades.map((g) => S.nomeDe(g)).filter((n) => porGrav.has(n));
        porGrav.forEach((_, nome) => { if (!ordem.includes(nome)) ordem.push(nome); });
        S.$('#porGravidade').innerHTML = barras(ordem.map((nome) => ({ nome, total: porGrav.get(nome) })));

        const topTipos = [...contar('tipo')].map(([nome, total]) => ({ nome, total }))
            .sort((a, b) => b.total - a.total).slice(0, 5);
        S.$('#porTipo').innerHTML = barras(topTipos, 'grav-leve');
    } catch (e) {
        ids.forEach((id) => (S.$(id).innerHTML = S.estadoErro(e.message)));
    }
})();
