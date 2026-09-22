(async () => {
    const S = window.SOI;

    const form = S.$('#formOcorrencia');
    const selTipo = S.$('#tipo');
    const selGravidade = S.$('#gravidade');
    const inputData = S.$('#data');
    const relato = S.$('#relato');
    const dica = S.$('#dicaGravidade');
    const botao = S.$('#btnRegistrar');
    let aluno = null;

    // Data local de hoje no formato AAAA-MM-DD (o backend só aceita esse formato)
    const hoje = () => {
        const d = new Date();
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        return d.toISOString().slice(0, 10);
    };

    function reiniciarCampos() {
        form.reset();
        form.classList.remove('was-validated');
        dica.hidden = true;
        inputData.max = hoje();
        inputData.value = hoje();
        S.$('#contador').textContent = '0/1000';
    }

    const cat = await S.carregarCatalogos();

    cat.tipos.filter(S.estaAtivo).forEach((t) => selTipo.add(new Option(S.nomeDe(t), t.id)));
    cat.gravidades.forEach((g) => selGravidade.add(new Option(S.nomeDe(g), g.id)));
    S.$('#avisoSemTipos').hidden = selTipo.options.length > 1;
    reiniciarCampos();

    // Cada tipo tem uma gravidade inicial: usamos como sugestão, e a pessoa pode trocar
    selTipo.addEventListener('change', () => {
        const tipo = cat.tiposPorId.get(String(selTipo.value));
        const sugerida = S.campo(tipo, ['gravidade_inicial_id']);
        if (sugerida && cat.gravidadesPorId.has(String(sugerida))) {
            selGravidade.value = String(sugerida);
            dica.hidden = false;
        }
    });
    selGravidade.addEventListener('change', () => { dica.hidden = true; });

    /* ---------- Aluno ---------- */

    function escolher(a) {
        aluno = a;
        S.$('#escolhidoAvatar').textContent = S.iniciais(a.nome);
        S.$('#escolhidoNome').textContent = a.nome || 'Sem nome';
        S.$('#escolhidoMeta').innerHTML = [
            a.matricula && `Matrícula ${S.esc(a.matricula)}`,
            a.curso && S.esc(a.curso),
        ].filter(Boolean).map((t) => `<span>${t}</span>`).join('');
        S.$('#alunoEscolhido').hidden = false;
        S.$('#blocoBusca').hidden = true;
        selTipo.focus();
    }

    function voltarParaBusca() {
        aluno = null;
        S.$('#alunoEscolhido').hidden = true;
        S.$('#blocoBusca').hidden = false;
        busca.limpar();
        busca.focar();
    }

    const busca = S.iniciarBuscaAluno({
        cat,
        acoes: [{ rotulo: 'Selecionar', classe: 'btn-verde', aoClicar: escolher }],
    });

    S.$('#trocarAluno').addEventListener('click', voltarParaBusca);

    // Vindo de "Registrar ocorrência" na página de alunos: ?matricula=...
    const matricula = new URLSearchParams(location.search).get('matricula');
    if (matricula) {
        try {
            const [encontrado] = await S.buscarAlunos('matricula', matricula);
            if (encontrado) escolher(S.normalizarAluno(encontrado, cat));
        } catch { /* segue para a busca manual */ }
    }

    /* ---------- Formulário ---------- */

    relato.addEventListener('input', () => {
        S.$('#contador').textContent = `${relato.value.length}/1000`;
    });

    inputData.addEventListener('input', () => {
        inputData.setCustomValidity(inputData.value && inputData.value > hoje() ? 'futuro' : '');
    });

    form.addEventListener('submit', async (ev) => {
        ev.preventDefault();
        form.classList.add('was-validated');

        if (!aluno) {
            S.erro('Busque e selecione o aluno antes de registrar.', 'Aluno não selecionado');
            return;
        }
        if (!aluno.id) {
            S.erro('Não foi possível identificar o aluno selecionado. Tente buscá-lo novamente.');
            return;
        }
        if (!form.checkValidity()) return;

        // curso_id e registrado_por_id NÃO são enviados: o servidor define os dois
        // (curso a partir do cadastro do aluno; autor a partir do usuário logado).
        const payload = {
            aluno_id: Number(aluno.id),
            tipo_ocorrencia_id: Number(selTipo.value),
            gravidade_id: Number(selGravidade.value),
            relato: relato.value.trim(),
            data_ocorrencia: inputData.value,
            ano_letivo: Number(inputData.value.slice(0, 4)),
        };

        botao.disabled = true;
        botao.textContent = 'Registrando…';
        try {
            await S.api('ocorrencia.php', { method: 'POST', body: payload });
            const r = await Swal.fire({
                icon: 'success',
                title: 'Ocorrência registrada',
                text: `A ocorrência de ${aluno.nome} foi registrada.`,
                showCancelButton: true,
                confirmButtonText: 'Registrar outra',
                cancelButtonText: 'Voltar ao início',
                confirmButtonColor: '#2b7a3d',
            });
            if (r.isConfirmed) {
                voltarParaBusca();
                reiniciarCampos();
            } else {
                location.href = document.body.dataset.inicio;
            }
        } catch (e) {
            S.erro(e.message);
        } finally {
            botao.disabled = false;
            botao.textContent = 'Registrar ocorrência';
        }
    });
})();
