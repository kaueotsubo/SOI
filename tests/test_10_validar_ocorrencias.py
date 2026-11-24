
import os
import pytest

from selenium.webdriver.support.ui import Select
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


def fazer_login_direcao(navegador):
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


def test_direcao_acessa_listagem_ocorrencias(navegador):
    fazer_login_direcao(navegador)

    navegador.get(
        navegador.current_url.rsplit("/", 1)[0]
        + "/ocorrencias.php"
    )

    WebDriverWait(navegador, 10).until(
        EC.url_contains("ocorrencias.php")
    )

    assert "ocorrencias.php" in navegador.current_url

    print(
        "\n[PASSOU] Direção acessou a listagem de ocorrências."
    )



def test_direcao_consulta_registros_dos_tres_cargos(navegador):
    fazer_login_direcao(navegador)

    navegador.get(
        navegador.current_url.rsplit("/", 1)[0]
        + "/ocorrencias.php"
    )

    # Aguarda a listagem carregar.
    WebDriverWait(navegador, 15).until(
        lambda driver: driver.find_element(
            By.ID, "areaTabela"
        ).find_elements(By.CSS_SELECTOR, "tbody tr")
    )

    relatos = [
        "Teste automatizado de registro pelo cargo Direção.",
        "Teste automatizado de registro pelo cargo Professor.",
        "Teste automatizado de registro pelo cargo Monitor.",
    ]

    for relato in relatos:
        busca = navegador.find_element(By.ID, "fBusca")
        busca.clear()
        busca.send_keys(relato)

        # Aguarda o filtro encontrar o relato pesquisado.
        WebDriverWait(navegador, 10).until(
            lambda driver: relato.lower() in
            driver.find_element(
                By.ID, "areaTabela"
            ).text.lower()
        )

        # Localiza as linhas que correspondem ao relato.
        linhas = navegador.find_elements(
            By.CSS_SELECTOR,
            "#areaTabela tbody tr[data-id]"
        )

        correspondentes = [
            linha for linha in linhas
            if relato in linha.text
        ]

        assert correspondentes, (
            f"Nenhuma ocorrência encontrada para: {relato}"
        )

        # Seleciona a primeira ocorrência correspondente.
        linha = correspondentes[0]
        texto_linha = linha.text

        assert "João Victor Ribeiro" in texto_linha
        assert "Leve" in texto_linha
        assert relato in texto_linha

        # Abre os detalhes.
        linha.find_element(
            By.CSS_SELECTOR,
            "button[data-detalhe]"
        ).click()

        modal = WebDriverWait(navegador, 10).until(
            EC.visibility_of_element_located(
                (By.ID, "modalOcorrencia")
            )
        )

        detalhe = modal.find_element(
            By.ID, "detalheOcorrencia"
        )

        # Confere a descrição completa.
        descricao = detalhe.find_element(
            By.XPATH,
            ".//dt[normalize-space()='Descrição']/following-sibling::dd[1]"
        ).text

        # Confere quem registrou a ocorrência.
        responsavel = detalhe.find_element(
            By.XPATH,
            ".//dt[normalize-space()='Registrada por']/following-sibling::dd[1]"
        ).text

        assert descricao == relato
        assert responsavel.strip(), (
            f"Responsável não encontrado para: {relato}"
        )

        print(f"\n[PASSOU] Relato: {descricao}")
        print(f"[RESPONSÁVEL] {responsavel}")

        # Fecha o modal.
        modal.find_element(
            By.CSS_SELECTOR,
            "button[data-bs-dismiss='modal']"
        ).click()

        WebDriverWait(navegador, 10).until(
            EC.invisibility_of_element_located(
                (By.ID, "modalOcorrencia")
            )
        )

    print(
        "\n[PASSOU] As três ocorrências foram consultadas "
        "e seus detalhes conferidos."
    )


def test_filtrar_ocorrencias_por_gravidade(navegador):
    fazer_login_direcao(navegador)

    navegador.get(
        navegador.current_url.rsplit("/", 1)[0]
        + "/ocorrencias.php"
    )

    # Aguarda a tabela carregar.
    WebDriverWait(navegador, 15).until(
        lambda driver: driver.find_elements(
            By.CSS_SELECTOR,
            "#areaTabela tbody tr[data-id]"
        )
    )

    # Seleciona a gravidade Leve.
    filtro = Select(
        navegador.find_element(By.ID, "fGravidade")
    )

    filtro.select_by_visible_text("Leve")

    # Aguarda os resultados serem filtrados.
    WebDriverWait(navegador, 10).until(
        lambda driver: all(
            "Leve" in linha.text
            for linha in driver.find_elements(
                By.CSS_SELECTOR,
                "#areaTabela tbody tr[data-id]"
            )
        )
    )

    linhas = navegador.find_elements(
        By.CSS_SELECTOR,
        "#areaTabela tbody tr[data-id]"
    )

    assert linhas, "Nenhuma ocorrência com gravidade Leve encontrada."

    for linha in linhas:
        assert "Leve" in linha.text, (
            f"Foi encontrada uma ocorrência de outra gravidade: {linha.text}"
        )

    print(
        f"\n[PASSOU] Filtro por gravidade Leve."
        f"\n[RESULTADOS] {len(linhas)} ocorrência(s) encontrada(s)."
    )


def test_filtrar_ocorrencias_por_tipo(navegador):
    fazer_login_direcao(navegador)

    navegador.get(
        navegador.current_url.rsplit("/", 1)[0]
        + "/ocorrencias.php"
    )

    # Aguarda a tabela carregar.
    WebDriverWait(navegador, 15).until(
        lambda driver: driver.find_elements(
            By.CSS_SELECTOR,
            "#areaTabela tbody tr[data-id]"
        )
    )

    # Seleciona o tipo de ocorrência.
    filtro = Select(
        navegador.find_element(By.ID, "fTipo")
    )

    tipo = "Tumultuar ou dificultar a realização da aula"
    filtro.select_by_visible_text(tipo)

    # Aguarda a filtragem.
    WebDriverWait(navegador, 10).until(
        lambda driver: all(
            tipo in linha.text
            for linha in driver.find_elements(
                By.CSS_SELECTOR,
                "#areaTabela tbody tr[data-id]"
            )
        )
    )

    linhas = navegador.find_elements(
        By.CSS_SELECTOR,
        "#areaTabela tbody tr[data-id]"
    )

    assert linhas, (
        f"Nenhuma ocorrência encontrada para o tipo: {tipo}"
    )

    for linha in linhas:
        assert tipo in linha.text, (
            f"Tipo inesperado encontrado: {linha.text}"
        )

    print(
        f"\n[PASSOU] Filtro por tipo: {tipo}"
        f"\n[RESULTADOS] {len(linhas)} ocorrência(s) encontrada(s)."
    )


def test_filtrar_ocorrencias_ativas(navegador):
    fazer_login_direcao(navegador)

    navegador.get(
        navegador.current_url.rsplit("/", 1)[0]
        + "/ocorrencias.php"
    )

    # Aguarda a tabela carregar.
    WebDriverWait(navegador, 15).until(
        lambda driver: driver.find_elements(
            By.CSS_SELECTOR,
            "#areaTabela tbody tr[data-id]"
        )
    )

    # Seleciona somente ocorrências ativas.
    filtro = Select(
        navegador.find_element(By.ID, "fSituacao")
    )

    filtro.select_by_value("ativas")

    # Aguarda a filtragem.
    WebDriverWait(navegador, 10).until(
        lambda driver: all(
            "Cancelada" not in linha.text
            for linha in driver.find_elements(
                By.CSS_SELECTOR,
                "#areaTabela tbody tr[data-id]"
            )
        )
    )

    linhas = navegador.find_elements(
        By.CSS_SELECTOR,
        "#areaTabela tbody tr[data-id]"
    )

    assert linhas, "Nenhuma ocorrência ativa encontrada."

    for linha in linhas:
        assert "Cancelada" not in linha.text, (
            f"Ocorrência cancelada encontrada: {linha.text}"
        )

    print(
        f"\n[PASSOU] Filtro de ocorrências ativas."
        f"\n[RESULTADOS] {len(linhas)} ocorrência(s) encontrada(s)."
    )

def test_cancelamento_exige_permissao_direcao(navegador):
    fazer_login_direcao(navegador)

    # Obtém o token CSRF da sessão.
    token = navegador.execute_script("""
        return document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
    """)

    # Tenta cancelar uma ocorrência com ID inexistente.
    # A autorização deve ser verificada antes da existência do registro.
    resultado = navegador.execute_async_script("""
        const token = arguments[0];
        const concluir = arguments[arguments.length - 1];

        fetch('api/ocorrencia.php?id=999999', {
            method: 'PATCH',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-Token': token
            },
            body: JSON.stringify({
                motivo_cancelamento: 'Teste de autorização.'
            })
        })
        .then(async resposta => {
            concluir({
                status: resposta.status,
                dados: await resposta.json()
            });
        })
        .catch(erro => concluir({
            erro: erro.message
        }));
    """, token)

    assert "erro" not in resultado, resultado
    assert resultado["status"] == 404, resultado

    print(
        "\n[PASSOU] Direção foi autorizada a executar "
        "a operação PATCH. O ID inexistente retornou 404."
    )


@pytest.mark.parametrize(
    "perfil",
    ["PROFESSOR", "MONITOR"]
)
def test_api_ocorrencia_bloqueia_cancelamento_sem_permissao(
    navegador,
    perfil
):
    email = os.getenv(f"{perfil}_EMAIL")
    senha = os.getenv(f"{perfil}_SENHA")

    if not email or not senha:
        pytest.skip(
            f"Credenciais de {perfil} não configuradas."
        )

    # Realiza login.
    navegador.find_element(By.ID, "email").send_keys(email)
    navegador.find_element(By.ID, "senha").send_keys(senha)

    navegador.find_element(
        By.CSS_SELECTOR,
        'button[name="entrar"]'
    ).click()

    WebDriverWait(navegador, 10).until(
        EC.url_contains("homea.php")
    )

    # Obtém o token CSRF da sessão.
    token = navegador.execute_script("""
        return document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
    """)

    # Tenta cancelar uma ocorrência.
    resultado = navegador.execute_async_script("""
        const token = arguments[0];
        const finalizar = arguments[arguments.length - 1];

        fetch('api/ocorrencia.php?id=999999', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': token
            },
            body: JSON.stringify({
                motivo_cancelamento: 'Teste de permissão.'
            })
        })
        .then(resposta => {
            finalizar({
                status: resposta.status
            });
        })
        .catch(erro => {
            finalizar({
                erro: erro.message
            });
        });
    """, token)

    assert "erro" not in resultado, resultado

    assert resultado["status"] == 403, (
        f"{perfil} deveria receber HTTP 403, "
        f"mas recebeu HTTP {resultado['status']}."
    )

    print(
        f"\n[PASSOU] {perfil} não pode cancelar ocorrências: "
        f"HTTP {resultado['status']}."
    )
