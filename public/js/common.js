/* SOI - utilitários compartilhados entre as páginas internas */
(function () {
    'use strict';

    const COR_CONFIRMAR = '#2b7a3d';
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const $ = (seletor, raiz = document) => raiz.querySelector(seletor);
    const $$ = (seletor, raiz = document) => Array.from(raiz.querySelectorAll(seletor));

    /* ---------- Texto ---------- */

    // Escapa HTML: use SEMPRE em dados vindos da API antes de montar innerHTML
    const esc = (valor) =>
        String(valor ?? '').replace(/[&<>"']/g, (c) =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    // Minúsculas e sem acento, para comparar e filtrar textos
    const norm = (valor) =>
        String(valor ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();

    // Primeiro valor preenchido entre várias chaves possíveis
    const campo = (obj, chaves, padrao = '') => {
        if (!obj) return padrao;
        for (const k of chaves) {
            const v = obj[k];
            if (v !== undefined && v !== null && v !== '') return v;
        }
        return padrao;
    };

    // Valor pode vir como texto ou como objeto { nome }
    const nomeDe = (v) => (v && typeof v === 'object' ? campo(v, ['nome', 'descricao', 'titulo']) : (v ?? ''));

    const iniciais = (nome) => {
        const partes = String(nome || '?').trim().split(/\s+/);
        const a = partes[0]?.[0] || '?';
        const b = partes.length > 1 ? partes[partes.length - 1][0] : '';
        return (a + b).toUpperCase();
    };

    const debounce = (fn, ms = 400) => {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    };

    const paraBool = (v) => {
        if (typeof v === 'string') return !/^(0|false|inativo|inativa|nao|não)$/i.test(v.trim());
        return v === undefined || v === null ? true : Boolean(Number(v) || v === true);
    };

    const estaAtivo = (item) => paraBool(campo(item, ['ativo', 'ativa', 'status'], true));

    /* ---------- Datas ---------- */

    function parseData(v) {
        if (!v) return null;
        if (v instanceof Date) return v;
        const s = String(v).trim();
        const soData = s.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (soData) return new Date(+soData[1], +soData[2] - 1, +soData[3]);
        const d = new Date(s.replace(' ', 'T'));
        return isNaN(d) ? null : d;
    }

    function formatarData(v) {
        const d = parseData(v);
        if (!d) return v ? String(v) : '';
        const temHora = /\d{1,2}:\d{2}/.test(String(v));
        return temHora
            ? d.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' })
            : d.toLocaleDateString('pt-BR');
    }

    /* ---------- API ---------- */

    class ApiError extends Error {
        constructor(mensagem, status) { super(mensagem); this.status = status; }
    }

    async function api(endpoint, { method = 'GET', params, body } = {}) {
        let url = 'api/' + endpoint;
        if (params) url += '?' + new URLSearchParams(params);

        const opcoes = { method, credentials: 'same-origin', headers: { Accept: 'application/json' } };
        if (body !== undefined) {
            opcoes.headers['Content-Type'] = 'application/json';
            opcoes.body = JSON.stringify(body);
        }
        // Enviado para o backend validar, se desejar (a API ainda não confere)
        if (method !== 'GET') opcoes.headers['X-CSRF-Token'] = CSRF;

        let resposta;
        try {
            resposta = await fetch(url, opcoes);
        } catch {
            throw new ApiError('Não foi possível conectar ao servidor. Verifique sua conexão.', 0);
        }

        if (resposta.status === 401) {
            location.href = 'index.php'; // sessão expirada
            throw new ApiError('Sessão expirada. Faça login novamente.', 401);
        }

        const texto = await resposta.text();
        let dados = null;
        try { dados = texto ? JSON.parse(texto) : null; }
        catch { throw new ApiError('O servidor enviou uma resposta inválida.', resposta.status); }

        if (!resposta.ok) throw new ApiError(dados?.error || 'Erro ao processar a requisição.', resposta.status);
        return dados;
    }

    const lista = (dados) => {
        if (Array.isArray(dados)) return dados;
        if (Array.isArray(dados?.data)) return dados.data;
        if (Array.isArray(dados?.items)) return dados.items;
        return [];
    };

    /* ---------- Catálogos (tipos, gravidades, cursos) ---------- */

    async function carregarCatalogos() {
        const buscar = (endpoint) => api(endpoint).then(lista).catch(() => []);
        const [tipos, gravidades, cursos] = await Promise.all([
            buscar('TipoOcorrenciaAPI.php'), buscar('gravidade.php'), buscar('curso.php'),
        ]);
        const porId = (arr) => new Map(arr.map((x) => [String(campo(x, ['id'])), x]));
        return {
            tipos, gravidades, cursos,
            tiposPorId: porId(tipos), gravidadesPorId: porId(gravidades), cursosPorId: porId(cursos),
        };
    }

    /* ---------- Normalização dos registros ---------- */
    // Os nomes de campo abaixo são tentativas comuns; se a sua API usar outros,
    // basta acrescentar a chave na lista correspondente.

    function normalizarOcorrencia(oc, cat) {
        const alunoObj = oc.aluno && typeof oc.aluno === 'object' ? oc.aluno : null;
        const tipoId = campo(oc, ['tipo_ocorrencia_id', 'tipoOcorrenciaId', 'id_tipo_ocorrencia', 'tipo_id']);
        const gravId = campo(oc, ['gravidade_id', 'gravidadeId', 'id_gravidade']);

        const tipo = nomeDe(campo(oc, ['tipo_ocorrencia_nome', 'tipo_nome', 'nome_tipo', 'tipoOcorrencia', 'tipo_ocorrencia', 'tipo']))
            || nomeDe(cat.tiposPorId.get(String(tipoId))) || '';
        const gravidade = nomeDe(campo(oc, ['gravidade_nome', 'nome_gravidade', 'gravidade']))
            || nomeDe(cat.gravidadesPorId.get(String(gravId))) || '';

        const dataBruta = campo(oc, ['data_ocorrencia', 'dataOcorrencia', 'data', 'data_hora', 'created_at', 'criado_em']);
        const alunoId = campo(oc, ['aluno_id', 'alunoId', 'id_aluno']) || campo(alunoObj, ['id']);

        return {
            id: campo(oc, ['id']),
            alunoId,
            alunoNome: nomeDe(campo(oc, ['aluno_nome', 'nome_aluno'])) || campo(alunoObj, ['nome']) || (typeof oc.aluno === 'string' ? oc.aluno : ''),
            alunoMatricula: campo(oc, ['aluno_matricula', 'matricula']) || campo(alunoObj, ['matricula']),
            tipo,
            gravidade,
            descricao: campo(oc, ['relato', 'descricao']),
            registradoPor: campo(oc, ['registrado_por_nome', 'usuario_nome']),
            cancelada: /cancel/i.test(String(campo(oc, ['status']))),
            motivoCancelamento: campo(oc, ['motivo_cancelamento']),
            dataBruta,
            data: parseData(dataBruta),
            dataTexto: formatarData(dataBruta),
        };
    }

    function normalizarAluno(a, cat) {
        const cursoId = campo(a, ['curso_id', 'cursoId', 'id_curso']);
        return {
            id: campo(a, ['id', 'idAluno', 'aluno_id']),
            nome: campo(a, ['nome', 'nomeAluno', 'nome_aluno']),
            matricula: campo(a, ['matricula']),
            curso: nomeDe(campo(a, ['curso_nome', 'nomeCurso', 'curso'])) || nomeDe(cat.cursosPorId.get(String(cursoId))) || '',
            cursoId,
        };
    }

    const porDataDesc = (a, b) => {
        const ta = a.data ? a.data.getTime() : 0;
        const tb = b.data ? b.data.getTime() : 0;
        return tb - ta || Number(b.id || 0) - Number(a.id || 0);
    };

    /* ---------- Componentes visuais ---------- */

    function classeGravidade(nome) {
        const n = norm(nome);
        if (!n) return 'grav-neutra';
        if (/leve|baix/.test(n)) return 'grav-leve';
        if (/gravissim|critic/.test(n)) return 'grav-gravissima';
        if (/grav|alta/.test(n)) return 'grav-grave';
        if (/moder|medi/.test(n)) return 'grav-moderada';
        return 'grav-neutra';
    }

    const badgeGravidade = (nome) =>
        `<span class="soi-badge ${classeGravidade(nome)}">${esc(nome || 'Sem gravidade')}</span>`;

    const carregando = (texto = 'Carregando…') =>
        `<div class="soi-vazio"><div class="spinner-border spinner-border-sm text-success" role="status"></div><span>${esc(texto)}</span></div>`;

    const estadoVazio = (titulo, texto = '') =>
        `<div class="soi-vazio"><strong>${esc(titulo)}</strong>${texto ? `<span>${esc(texto)}</span>` : ''}</div>`;

    const estadoErro = (mensagem) =>
        `<div class="soi-vazio soi-vazio-erro"><strong>Não foi possível carregar</strong><span>${esc(mensagem)}</span></div>`;

    const badgeCancelada = () => '<span class="soi-badge grav-neutra">Cancelada</span>';

    const itemHistorico = (o) => `
        <li class="soi-hist-item ${o.cancelada ? 'soi-cancelada' : ''}">
            <div class="soi-hist-topo">
                <strong>${esc(o.tipo || 'Tipo não informado')}</strong>
                <span class="d-flex gap-1">${o.cancelada ? badgeCancelada() : ''}${badgeGravidade(o.gravidade)}</span>
            </div>
            <time>${esc(o.dataTexto)}</time>
            <p>${esc(o.descricao)}</p>
            ${o.cancelada && o.motivoCancelamento ? `<p class="small mt-1">Motivo do cancelamento: ${esc(o.motivoCancelamento)}</p>` : ''}
        </li>`;

    /* ---------- Alertas ---------- */

    const toast = (mensagem, icone = 'success') =>
        Swal.fire({ toast: true, position: 'top-end', icon: icone, title: mensagem, showConfirmButton: false, timer: 3000, timerProgressBar: true });

    const erro = (mensagem, titulo = 'Não foi possível concluir') =>
        Swal.fire({ icon: 'error', title: titulo, text: mensagem, confirmButtonColor: COR_CONFIRMAR });

    // Mensagens dos redirecionamentos de api/cadastrarUsuario.php (home.php?sucesso=1 / ?erro=1)
    function avisoPorQuery() {
        const p = new URLSearchParams(location.search);
        if (p.has('sucesso')) {
            Swal.fire({ icon: 'success', title: 'Usuário cadastrado', text: 'O novo usuário já pode entrar no sistema.', confirmButtonColor: COR_CONFIRMAR });
        } else if (p.has('erro')) {
            const detalhe = (p.get('erro') || '').trim();
            const texto = detalhe && detalhe !== '1' ? detalhe.slice(0, 200) : 'Confira os dados informados e tente novamente.';
            Swal.fire({ icon: 'error', title: 'Cadastro não concluído', text: texto, confirmButtonColor: COR_CONFIRMAR });
        } else {
            return;
        }
        history.replaceState(null, '', location.pathname);
    }

    /* ---------- Busca de aluno (nome, matrícula ou CPF) ---------- */

    // Mesmas regras do AlunoService, para avisar o usuário antes de chamar a API.
    // Retorna o texto do problema ou '' quando está tudo certo.
    function validarBusca(modo, valor) {
        if (modo === 'nome') {
            if (valor.length < 3) return 'Digite pelo menos 3 letras do nome.';
            if (valor.length > 100) return 'O nome não pode ter mais de 100 caracteres.';
            if (!/^[\p{L}\s]+$/u.test(valor)) return 'Use apenas letras e espaços no nome.';
        } else if (modo === 'matricula') {
            if (!/^\d{19}$/.test(valor)) return 'A matrícula deve ter exatamente 19 dígitos numéricos.';
        } else if (modo === 'cpf') {
            const digitos = valor.replace(/\D/g, '');
            if (!/^\d{11}$/.test(digitos)) return 'O CPF deve ter 11 dígitos.';
            if (/^(\d)\1{10}$/.test(digitos)) return 'O CPF não pode ter todos os dígitos iguais.';
        }
        return '';
    }

    async function buscarAlunos(modo, termo) {
        const valor = termo.trim();
        if (!valor) return [];
        // Ajuste aqui se o CPF for salvo com máscara no banco
        const enviado = modo === 'cpf' ? valor.replace(/\D/g, '') : valor;
        try {
            const dados = await api('aluno.php', { params: { [modo]: enviado } });
            return Array.isArray(dados) ? dados : (dados ? [dados] : []);
        } catch (e) {
            if (e.status === 404) return [];
            throw e;
        }
    }

    /**
     * Liga o formulário de includes/busca-aluno.php.
     * acoes: [{ rotulo, classe, aoClicar(aluno) }] — botões exibidos em cada resultado.
     */
    function iniciarBuscaAluno({ cat, acoes = [] }) {
        const form = $('#formBuscaAluno');
        const modo = $('#buscaModo');
        const termo = $('#buscaTermo');
        const saida = $('#buscaResultados');
        const dicas = { nome: 'Digite pelo menos 3 letras do nome', matricula: 'Digite os 19 dígitos da matrícula', cpf: 'Digite os 11 dígitos do CPF' };
        const limites = { nome: 100, matricula: 19, cpf: 14 };
        let sequencia = 0;
        let alunos = [];

        function desenhar() {
            if (!alunos.length) {
                saida.innerHTML = estadoVazio('Nenhum aluno encontrado', 'Confira o que foi digitado ou busque por outro dado.');
                return;
            }
            saida.innerHTML = '<ul class="soi-lista-alunos">' + alunos.map((a, i) => `
                <li class="soi-aluno">
                    <div class="soi-avatar" aria-hidden="true">${esc(iniciais(a.nome))}</div>
                    <div class="soi-aluno-dados">
                        <strong>${esc(a.nome || 'Sem nome')}</strong>
                        <div class="soi-aluno-meta">
                            ${a.matricula ? `<span>Matrícula ${esc(a.matricula)}</span>` : ''}
                            ${a.curso ? `<span>${esc(a.curso)}</span>` : ''}
                        </div>
                    </div>
                    <div class="soi-aluno-acoes">
                        ${acoes.map((ac, j) => `<button type="button" class="btn btn-sm ${esc(ac.classe || 'btn-verde')}" data-aluno="${i}" data-acao="${j}">${esc(ac.rotulo)}</button>`).join('')}
                    </div>
                </li>`).join('') + '</ul>';
        }

        async function executar() {
            const valor = termo.value.trim();
            if (!valor) { saida.innerHTML = ''; return; }
            const problema = validarBusca(modo.value, valor);
            if (problema) {
                saida.innerHTML = estadoVazio('Confira o que foi digitado', problema);
                return;
            }
            const minha = ++sequencia;
            saida.innerHTML = carregando('Buscando alunos…');
            try {
                const brutos = await buscarAlunos(modo.value, valor);
                if (minha !== sequencia) return; // resposta antiga, já há outra busca em andamento
                alunos = brutos.map((a) => normalizarAluno(a, cat));
                desenhar();
            } catch (e) {
                if (minha === sequencia) saida.innerHTML = estadoErro(e.message);
            }
        }

        modo.addEventListener('change', () => {
            termo.placeholder = dicas[modo.value];
            termo.maxLength = limites[modo.value];
            termo.inputMode = modo.value === 'nome' ? 'text' : 'numeric';
            termo.focus();
        });
        form.addEventListener('submit', (ev) => { ev.preventDefault(); executar(); });
        termo.addEventListener('input', debounce(() => {
            if (modo.value === 'nome' && !validarBusca('nome', termo.value.trim())) executar();
        }));
        saida.addEventListener('click', (ev) => {
            const botao = ev.target.closest('[data-acao]');
            if (!botao) return;
            acoes[+botao.dataset.acao].aoClicar(alunos[+botao.dataset.aluno]);
        });

        return {
            limpar() { saida.innerHTML = ''; termo.value = ''; },
            focar() { termo.focus(); },
        };
    }

    window.SOI = {
        $, $$, esc, norm, campo, nomeDe, iniciais, debounce, estaAtivo, paraBool,
        parseData, formatarData,
        api, ApiError, lista, carregarCatalogos,
        normalizarOcorrencia, normalizarAluno, porDataDesc,
        classeGravidade, badgeGravidade, badgeCancelada, carregando, estadoVazio, estadoErro, itemHistorico,
        toast, erro, avisoPorQuery, validarBusca, buscarAlunos, iniciarBuscaAluno,
    };
})();
