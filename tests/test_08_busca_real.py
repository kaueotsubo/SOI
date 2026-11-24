
import os
import pytest

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select, WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


def fazer_login_direcao(navegador):
    """Realiza login com a conta da Direção."""

    email = os.getenv("DIRECAO_EMAIL")
    senha = os.getenv("DIRECAO_SENHA")

    if not email or not senha:
        pytest.skip("Credenciais da Direção não configuradas.")

    navegador.find_element(By.ID, "email").send_keys(email)
    navegador.find_element(By.ID, "senha").send_keys(senha)

    navegador.find_element(
        By.CSS_SELECTOR, 'button[name="entrar"]'
    ).click()

    WebDriverWait(navegador, 10).until(
        EC.url_contains("home.php")
    )


def acessar_busca_alunos(navegador):
    """Abre a página e aguarda a inicialização da busca."""

    navegador.get(
        navegador.current_url.rsplit("/", 1)[0] + "/alunos.php"
    )

    # Registra erros JavaScript desde o início da página.
    navegador.execute_script("""
        window.__errosTeste = [];

        window.addEventListener("error", function(evento) {
            window.__errosTeste.push({
                mensagem: evento.message,
                arquivo: evento.filename,
                linha: evento.lineno
            });
        });

        window.addEventListener("unhandledrejection", function(evento) {
            window.__errosTeste.push({
                promessa: String(evento.reason)
            });
        });
    """)

    WebDriverWait(navegador, 10).until(
        EC.presence_of_element_located((By.ID, "buscaTermo"))
    )

    # Aguarda o alunos.js terminar de inicializar.
    WebDriverWait(navegador, 15).until(
        lambda driver: driver.execute_script(
            "return document.documentElement.dataset.buscaAlunosPronta === 'true'"
        )
    )


# ==================================================
# TESTE 1 - BUSCA POR NOME
# ==================================================

@pytest.mark.parametrize(
    "nome, quantidade_esperada",
    [
        ("João Victor Ribeiro", 1),
        ("Valentina Souza Nóbrega", 1),
        ("Arthur Oliveira Santos", 1),
        ("Pedro Henrique Araújo", 1),
        ("Vitória Almeida Santos", 1),
        ("Sofia Cardoso Lima", 1),
        ("Giovana Costa Melo", 2),
        ("João", 2),
    ]
)
def test_buscar_aluno_por_nome(navegador, nome, quantidade_esperada):
    fazer_login_direcao(navegador)
    acessar_busca_alunos(navegador)

    campo = navegador.find_element(By.ID, "buscaTermo")
    campo.send_keys(nome)

    try:
        WebDriverWait(navegador, 15).until(
            lambda driver: len(
                driver.find_elements(
                    By.CSS_SELECTOR,
                    "#buscaResultados .soi-aluno"
                )
            ) == quantidade_esperada
        )

    except Exception:
        print("\n[DIAGNÓSTICO DA BUSCA]")
        print("Nome pesquisado:", nome)

        print(
            "Conteúdo da interface:",
            repr(navegador.find_element(
                By.ID, "buscaResultados"
            ).text)
        )

        resposta = navegador.execute_async_script("""
            const nome = arguments[0];
            const finalizar = arguments[1];

            fetch(
                'api/aluno.php?nome=' + encodeURIComponent(nome),
                { credentials: 'same-origin' }
            )
            .then(async resposta => finalizar({
                status: resposta.status,
                corpo: await resposta.text()
            }))
            .catch(erro => finalizar({
                erro: erro.message
            }));
        """, nome)

        print("Resposta direta da API:", resposta)

        print("\n[DIAGNÓSTICO JAVASCRIPT]")

        estado = navegador.execute_script("""
            return {
                url: window.location.href,
                campo: document.querySelector("#buscaTermo")?.value,
                modo: document.querySelector("#buscaModo")?.value,
                resultadosExiste: !!document.querySelector("#buscaResultados"),
                buscaPronta: document.documentElement.dataset.buscaAlunosPronta,
                scripts: Array.from(document.scripts).map(s => s.src),
                erros: window.__errosTeste || []
            };
        """)

        print(estado)

        raise

    alunos = navegador.find_elements(
        By.CSS_SELECTOR,
        "#buscaResultados .soi-aluno"
    )

    assert len(alunos) == quantidade_esperada, (
        f"Esperado: {quantidade_esperada}, "
        f"Encontrado: {len(alunos)}"
    )

    print(
        f"[PASSOU] Busca por nome: {nome} "
        f"({len(alunos)} resultado(s))."
    )


# ==================================================
# TESTE 2 - BUSCA POR CPF
# ==================================================

def test_buscar_aluno_por_cpf(navegador):
    fazer_login_direcao(navegador)
    acessar_busca_alunos(navegador)

    Select(
        navegador.find_element(By.ID, "buscaModo")
    ).select_by_value("cpf")

    campo = navegador.find_element(By.ID, "buscaTermo")
    campo.send_keys("90000000053")

    navegador.find_element(
        By.CSS_SELECTOR,
        '#formBuscaAluno button[type="submit"]'
    ).click()

    WebDriverWait(navegador, 10).until(
        lambda driver: "João Victor Ribeiro" in
        driver.find_element(By.ID, "buscaResultados").text
    )

    resultados = navegador.find_element(
        By.ID, "buscaResultados"
    ).text

    assert "João Victor Ribeiro" in resultados

    print("\n[PASSOU] Aluno encontrado pela busca por CPF.")


# ==================================================
# TESTE 3 - ALUNO INEXISTENTE
# ==================================================

def test_buscar_aluno_inexistente(navegador):
    fazer_login_direcao(navegador)
    acessar_busca_alunos(navegador)

    campo = navegador.find_element(By.ID, "buscaTermo")
    campo.send_keys("Aluno Inexistente QA")

    WebDriverWait(navegador, 15).until(
        lambda driver: "Nenhum aluno encontrado" in
        driver.find_element(By.ID, "buscaResultados").text
    )

    resultados = navegador.find_element(
        By.ID, "buscaResultados"
    ).text

    assert "Nenhum aluno encontrado" in resultados

    print(
        "\n[PASSOU] Sistema informou corretamente "
        "que o aluno não existe."
    )



# ==================================================
# TESTE 4 - BUSCA POR MATRÍCULA
# ==================================================

def test_buscar_aluno_por_matricula(navegador):
    fazer_login_direcao(navegador)
    acessar_busca_alunos(navegador)

    Select(
        navegador.find_element(By.ID, "buscaModo")
    ).select_by_value("matricula")

    campo = navegador.find_element(By.ID, "buscaTermo")
    campo.send_keys("20251CRB00000000053")

    navegador.find_element(
        By.CSS_SELECTOR,
        '#formBuscaAluno button[type="submit"]'
    ).click()

    WebDriverWait(navegador, 10).until(
        lambda driver: "João Victor Ribeiro" in
        driver.find_element(By.ID, "buscaResultados").text
    )

    resultados = navegador.find_element(
        By.ID, "buscaResultados"
    ).text

    assert "João Victor Ribeiro" in resultados

    print(
        "\n[PASSOU] Aluno encontrado pela busca por matrícula."
    )
