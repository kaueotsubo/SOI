(async () => {
    const S = window.SOI;
    const ENDPOINT = 'TipoOcorrenciaAPI.php';

    const area = S.$('#areaTabela');
    const form = S.$('#formTipo');
    const modal = new bootstrap.Modal('#modalTipo');
    const selGravidade = S.$('#tipoGravidade');
    const botaoSalvar = S.$('#btnSalvarTipo');
    let tipos = [];
    let gravidades = [];

    const nomeGravidade = (id) => {
        const g = gravidades.find((x) => String(S.campo(x, ['id'])) === String(id));
        return g ? S.nomeDe(g) : '';
    };

    async function carregar() {
        area.innerHTML = S.carregando('Carregando tipos…');
        try {
            tipos = (await S.api(ENDPOINT).then(S.lista)).map((t) => ({
                id: S.campo(t, ['id']),
                nome: S.campo(t, ['nome']),
                descricao: S.campo(t, ['descricao']),
                gravidadeId: S.campo(t, ['gravidade_inicial_id']),
                ativo: S.estaAtivo(t),
            }));
            desenhar();
        } catch (e) {
            area.innerHTML = S.estadoErro(e.message);
        }
    }

    function desenhar() {
        if (!tipos.length) {
            area.innerHTML = S.estadoVazio('Nenhum tipo cadastrado', 'Use "Novo tipo" para criar o primeiro.');
            return;
        }
        area.innerHTML = `
            <div class="table-responsive">
                <table class="table soi-tabela">
                    <thead><tr><th>Nome</th><th>Gravidade inicial</th><th>Descrição</th><th>Ativo</th><th class="text-end"><span class="visually-hidden">Ações</span></th></tr></thead>
                    <tbody>${tipos.map((t) => `
                        <tr class="${t.ativo ? '' : 'soi-cancelada'}">
                            <td class="fw-semibold">${S.esc(t.nome)}</td>
                            <td>${S.badgeGravidade(nomeGravidade(t.gravidadeId))}</td>
                            <td class="soi-truncar">${S.esc(t.descricao) || '<span class="text-secondary">Sem descrição</span>'}</td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" data-status="${S.esc(t.id)}"
                                        ${t.ativo ? 'checked' : ''} aria-label="Tipo ${S.esc(t.nome)} ativo">
                                </div>
                            </td>
                            <td class="text-end"><button type="button" class="btn btn-sm btn-outline-secondary" data-editar="${S.esc(t.id)}">Editar</button></td>
                        </tr>`).join('')}
                    </tbody>
                </table>
            </div>`;
    }

    function abrirFormulario(tipo) {
        form.classList.remove('was-validated');
        S.$('#tipoId').value = tipo?.id ?? '';
        S.$('#tipoNome').value = tipo?.nome ?? '';
        S.$('#tipoDescricao').value = tipo?.descricao ?? '';
        selGravidade.value = tipo?.gravidadeId ? String(tipo.gravidadeId) : '';
        S.$('#modalTipoTitulo').textContent = tipo ? 'Editar tipo de ocorrência' : 'Novo tipo de ocorrência';
        modal.show();
    }

    S.$('#btnNovo').addEventListener('click', () => abrirFormulario(null));
    S.$('#modalTipo').addEventListener('shown.bs.modal', () => S.$('#tipoNome').focus());

    area.addEventListener('click', (ev) => {
        const b = ev.target.closest('[data-editar]');
        if (b) abrirFormulario(tipos.find((t) => String(t.id) === b.dataset.editar));
    });

    area.addEventListener('change', async (ev) => {
        const chave = ev.target.closest('[data-status]');
        if (!chave) return;
        const tipo = tipos.find((t) => String(t.id) === chave.dataset.status);
        const novo = chave.checked;

        if (!novo) {
            const r = await Swal.fire({
                icon: 'warning',
                title: 'Desativar este tipo?',
                text: `"${tipo.nome}" deixará de aparecer ao registrar novas ocorrências. As já registradas continuam como estão.`,
                showCancelButton: true,
                confirmButtonText: 'Desativar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#a61e2d',
            });
            if (!r.isConfirmed) { chave.checked = true; return; }
        }

        chave.disabled = true;
        try {
            const r = await S.api(ENDPOINT, { method: 'PATCH', body: { id: Number(tipo.id), ativo: novo } });
            tipo.ativo = novo;
            S.toast(r?.message || (novo ? 'Tipo ativado' : 'Tipo desativado'));
            desenhar();
        } catch (e) {
            chave.checked = !novo; // volta ao estado anterior
            chave.disabled = false;
            S.erro(e.message);
        }
    });

    form.addEventListener('submit', async (ev) => {
        ev.preventDefault();
        form.classList.add('was-validated');
        if (!form.checkValidity()) return;

        const id = S.$('#tipoId').value;
        const atual = id ? tipos.find((t) => String(t.id) === id) : null;
        const dados = {
            nome: S.$('#tipoNome').value.trim(),
            descricao: S.$('#tipoDescricao').value.trim(),
            gravidade_inicial_id: Number(selGravidade.value),
            // O PUT grava o campo "ativo" junto: sem enviá-lo, o tipo seria desativado ao editar
            ativo: atual ? atual.ativo : true,
        };

        botaoSalvar.disabled = true;
        botaoSalvar.textContent = 'Salvando…';
        try {
            const r = await S.api(ENDPOINT, id
                ? { method: 'PUT', body: { id: Number(id), ...dados } }
                : { method: 'POST', body: dados });
            modal.hide();
            S.toast(r?.message || (id ? 'Tipo atualizado' : 'Tipo criado'));
            carregar();
        } catch (e) {
            S.erro(e.message);
        } finally {
            botaoSalvar.disabled = false;
            botaoSalvar.textContent = 'Salvar';
        }
    });

    // Início
    gravidades = await S.api('gravidade.php').then(S.lista).catch(() => []);
    gravidades.forEach((g) => selGravidade.add(new Option(S.nomeDe(g), g.id)));
    if (!gravidades.length) {
        area.innerHTML = S.estadoErro('Não foi possível carregar as gravidades. Recarregue a página.');
        S.$('#btnNovo').disabled = true;
        return;
    }
    carregar();
})();
