
import os
import pytest

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
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


def acessar_registro_ocorrencia(navegador):
    """Abre a página e aguarda a inicialização do formulário."""

    navegador.get(
        navegador.current_url.rsplit("/", 1)[0]
        + "/nova-ocorrencia.php"
    )

    WebDriverWait(navegador, 15).until(
        lambda driver: driver.execute_script(
            "return document.querySelector('#tipo').options.length > 1"
            " && document.querySelector('#gravidade').options.length > 1"
        )
    )


def test_carregar_tipos_e_gravidades(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    tipos = Select(
        navegador.find_element(By.ID, "tipo")
    ).options

    gravidades = Select(
        navegador.find_element(By.ID, "gravidade")
    ).options

    assert len(tipos) == 63, (
        f"Esperados 62 tipos + opção inicial. Encontrados: {len(tipos)}"
    )

    assert len(gravidades) == 5, (
        f"Esperadas 4 gravidades + opção inicial. Encontradas: {len(gravidades)}"
    )

    print("\n[PASSOU] Tipos e gravidades carregados corretamente.")


@pytest.mark.parametrize(
    "tipo_id, gravidade_esperada",
    [
        ("1", "1"),
        ("14", "2"),
        ("22", "3"),
        ("38", "4"),
    ]
)
def test_sugestao_automatica_gravidade(
    navegador, tipo_id, gravidade_esperada
):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    tipo = Select(
        navegador.find_element(By.ID, "tipo")
    )

    tipo.select_by_value(tipo_id)

    gravidade = navegador.find_element(By.ID, "gravidade")

    WebDriverWait(navegador, 5).until(
        lambda driver: driver.find_element(
            By.ID, "gravidade"
        ).get_attribute("value") == gravidade_esperada
    )

    assert gravidade.get_attribute("value") == gravidade_esperada

    dica = navegador.find_element(By.ID, "dicaGravidade")

    assert dica.is_displayed()

    print(
        f"\n[PASSOU] Tipo {tipo_id} sugeriu "
        f"gravidade {gravidade_esperada}."
    )


def test_alterar_gravidade_manualmente(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    tipo = Select(
        navegador.find_element(By.ID, "tipo")
    )

    # Seleciona um tipo cuja gravidade inicial é Leve.
    tipo.select_by_value("1")

    dica = navegador.find_element(By.ID, "dicaGravidade")

    WebDriverWait(navegador, 5).until(
        lambda driver: dica.is_displayed()
    )

    # Altera manualmente a gravidade para Moderada.
    gravidade = Select(
        navegador.find_element(By.ID, "gravidade")
    )

    gravidade.select_by_value("2")

    WebDriverWait(navegador, 5).until(
        lambda driver: not dica.is_displayed()
    )

    assert gravidade.first_selected_option.get_attribute("value") == "2"
    assert not dica.is_displayed()

    print(
        "\n[PASSOU] Alteração manual da gravidade "
        "ocultou a dica automática."
    )


def test_selecionar_aluno_para_ocorrencia(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    # Pesquisa um aluno pelo nome.
    navegador.find_element(By.ID, "buscaTermo").send_keys(
        "João Victor Ribeiro"
    )

    navegador.find_element(
        By.CSS_SELECTOR, "#formBuscaAluno button[type='submit']"
    ).click()

    # Aguarda o resultado da pesquisa.
    WebDriverWait(navegador, 10).until(
        EC.visibility_of_element_located(
            (By.ID, "buscaResultados")
        )
    )

    WebDriverWait(navegador, 10).until(
        lambda driver: "João Victor Ribeiro"
        in driver.find_element(
            By.ID, "buscaResultados"
        ).text
    )

    # Seleciona o aluno.
    navegador.find_element(
        By.XPATH,
        "//div[@id='buscaResultados']//button[contains(., 'Selecionar')]"
    ).click()

    # Aguarda a exibição dos dados do aluno escolhido.
    WebDriverWait(navegador, 10).until(
        EC.visibility_of_element_located(
            (By.ID, "alunoEscolhido")
        )
    )

    nome = navegador.find_element(By.ID, "escolhidoNome").text

    assert "João Victor Ribeiro" in nome

    assert not navegador.find_element(
        By.ID, "blocoBusca"
    ).is_displayed()

    print(
        "\n[PASSOU] Aluno selecionado e exibido "
        "corretamente no formulário."
    )


def test_trocar_aluno_selecionado(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    # Pesquisa o primeiro aluno.
    navegador.find_element(By.ID, "buscaTermo").send_keys(
        "João Victor Ribeiro"
    )

    navegador.find_element(
        By.CSS_SELECTOR, "#formBuscaAluno button[type='submit']"
    ).click()

    WebDriverWait(navegador, 10).until(
        lambda driver: "João Victor Ribeiro"
        in driver.find_element(By.ID, "buscaResultados").text
    )

    # Seleciona o aluno.
    navegador.find_element(
        By.XPATH,
        "//div[@id='buscaResultados']//button[contains(., 'Selecionar')]"
    ).click()

    WebDriverWait(navegador, 10).until(
        EC.visibility_of_element_located((By.ID, "alunoEscolhido"))
    )

    # Clica em trocar aluno.
    navegador.find_element(By.ID, "trocarAluno").click()

    # Aguarda o retorno ao bloco de pesquisa.
    WebDriverWait(navegador, 10).until(
        EC.visibility_of_element_located((By.ID, "blocoBusca"))
    )

    assert not navegador.find_element(
        By.ID, "alunoEscolhido"
    ).is_displayed()

    assert navegador.find_element(
        By.ID, "buscaTermo"
    ).get_attribute("value") == ""

    print(
        "\n[PASSOU] Troca de aluno retornou à busca "
        "e limpou a pesquisa anterior."
    )



def test_contador_caracteres_relato(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    relato = navegador.find_element(By.ID, "relato")
    contador = navegador.find_element(By.ID, "contador")

    relato.send_keys("Aluno interrompeu a aula.")

    assert contador.text == "25/1000", (
        f"Contador incorreto: {contador.text}"
    )

    relato.clear()

    navegador.execute_script(
        "arguments[0].dispatchEvent(new Event('input', { bubbles: true }));",
        relato
    )

    assert contador.text == "0/1000", (
        f"Contador não foi reiniciado: {contador.text}"
    )

    print(
        "\n[PASSOU] Contador atualizado após digitação "
        "e limpeza do relato."
    )


def test_limite_maximo_relato(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    relato = navegador.find_element(By.ID, "relato")
    contador = navegador.find_element(By.ID, "contador")

    texto = "A" * 1100
    relato.send_keys(texto)

    valor = relato.get_attribute("value")

    assert len(valor) == 1000, (
        f"Esperados 1000 caracteres, encontrados: {len(valor)}"
    )

    assert contador.text == "1000/1000", (
        f"Contador incorreto: {contador.text}"
    )

    print(
        "\n[PASSOU] Campo de relato respeitou "
        "o limite máximo de 1000 caracteres."
    )


from datetime import date, timedelta


def test_data_ocorrencia_nao_pode_ser_futura(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    campo_data = navegador.find_element(By.ID, "data")

    data_futura = (
        date.today() + timedelta(days=1)
    ).isoformat()

    navegador.execute_script(
        """
        const campo = arguments[0];
        const data = arguments[1];

        campo.value = data;
        campo.dispatchEvent(new Event('input', { bubbles: true }));
        campo.dispatchEvent(new Event('change', { bubbles: true }));
        """,
        campo_data,
        data_futura
    )

    resultado = navegador.execute_script(
        """
        const campo = arguments[0];

        return {
            max: campo.max,
            value: campo.value,
            rangeOverflow: campo.validity.rangeOverflow,
            valid: campo.validity.valid
        };
        """,
        campo_data
    )

    print("\n[DIAGNÓSTICO DA DATA]")
    print(resultado)

    assert resultado["value"] == data_futura
    assert resultado["rangeOverflow"] is True
    assert resultado["valid"] is False

    print(
        "\n[PASSOU] Campo de data identificou "
        "corretamente uma data futura como inválida."
    )


def test_data_atual_e_permitida(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    campo_data = navegador.find_element(By.ID, "data")

    data_atual = date.today().isoformat()

    navegador.execute_script(
        """
        const campo = arguments[0];

        campo.value = arguments[1];
        campo.dispatchEvent(new Event('input', { bubbles: true }));
        campo.dispatchEvent(new Event('change', { bubbles: true }));
        """,
        campo_data,
        data_atual
    )

    resultado = navegador.execute_script(
        """
        const campo = arguments[0];

        return {
            value: campo.value,
            valid: campo.validity.valid,
            rangeOverflow: campo.validity.rangeOverflow
        };
        """,
        campo_data
    )

    assert resultado["value"] == data_atual
    assert resultado["valid"] is True
    assert resultado["rangeOverflow"] is False

    print("\n[PASSOU] Data atual aceita corretamente.")


def test_relato_exige_minimo_de_10_caracteres(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    relato = navegador.find_element(By.ID, "relato")

    relato.send_keys("Curto")

    resultado = navegador.execute_script(
        "return arguments[0].validity.tooShort;",
        relato
    )

    assert resultado is True, (
        "O campo aceitou um relato com menos de 10 caracteres."
    )

    relato.clear()

    navegador.execute_script(
        "arguments[0].dispatchEvent(new Event('input', { bubbles: true }));",
        relato
    )

    assert navegador.execute_script(
        "return arguments[0].validity.valueMissing;",
        relato
    ) is True

    print(
        "\n[PASSOU] Relato exige no mínimo 10 caracteres "
        "e não permite ficar vazio."
    )


def test_campos_obrigatorios_da_ocorrencia(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    campos = ["tipo", "gravidade", "data"]

    for campo_id in campos:
        campo = navegador.find_element(By.ID, campo_id)

        obrigatorio = campo.get_attribute("required")

        assert obrigatorio is not None, (
            f"O campo {campo_id} não está marcado como obrigatório."
        )

    print(
        "\n[PASSOU] Tipo, gravidade e data "
        "estão marcados como campos obrigatórios."
    )


def test_impedir_registro_sem_aluno(navegador):
    fazer_login_direcao(navegador)
    acessar_registro_ocorrencia(navegador)

    # Preenche os demais campos com dados válidos.
    Select(
        navegador.find_element(By.ID, "tipo")
    ).select_by_value("1")

    Select(
        navegador.find_element(By.ID, "gravidade")
    ).select_by_value("1")

    campo_data = navegador.find_element(By.ID, "data")

    navegador.execute_script(
        """
        arguments[0].value = arguments[1];
        arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
        """,
        campo_data,
        date.today().isoformat()
    )

    navegador.find_element(By.ID, "relato").send_keys(
        "Aluno interrompeu a explicação durante a aula."
    )

    # Tenta registrar sem selecionar um aluno.
    navegador.find_element(By.ID, "btnRegistrar").click()

    # Aguarda a mensagem de erro do sistema.
    alerta = WebDriverWait(navegador, 10).until(
        EC.visibility_of_element_located(
            (By.CSS_SELECTOR, ".swal2-popup")
        )
    )

    mensagem = alerta.text

    assert "aluno" in mensagem.lower(), (
        f"Mensagem inesperada: {mensagem}"
    )

    print(
        "\n[PASSOU] Sistema impediu o registro "
        "sem selecionar um aluno."
    )


@pytest.mark.parametrize(
    "cargo, email_env, senha_env, pagina",
    [
        ("Direção", "DIRECAO_EMAIL", "DIRECAO_SENHA", "home.php"),
        ("Professor", "PROFESSOR_EMAIL", "PROFESSOR_SENHA", "homea.php"),
        ("Monitor", "MONITOR_EMAIL", "MONITOR_SENHA", "homea.php"),
    ]
)

def test_registrar_ocorrencia_por_cargo(
    navegador, cargo, email_env, senha_env, pagina
):
    email = os.getenv(email_env)
    senha = os.getenv(senha_env)

    if not email or not senha:
        pytest.skip(f"Credenciais de {cargo} não configuradas.")

    # Login de acordo com o cargo.
    navegador.find_element(By.ID, "email").send_keys(email)
    navegador.find_element(By.ID, "senha").send_keys(senha)

    navegador.find_element(
        By.CSS_SELECTOR, 'button[name="entrar"]'
    ).click()

    # Aguarda o login e apresenta diagnóstico em caso de falha.
    try:
        WebDriverWait(navegador, 10).until(
            EC.url_contains(pagina)
        )
    except Exception:
        print(f"\n[ERRO DE LOGIN - {cargo}]")
        print("URL atual:", navegador.current_url)
        print(
            "Mensagem exibida:",
            navegador.find_element(By.TAG_NAME, "body").text
        )
        raise

    acessar_registro_ocorrencia(navegador)

    # Pesquisa e seleciona o aluno.
    navegador.find_element(
        By.ID, "buscaTermo"
    ).send_keys("João Victor Ribeiro")

    navegador.find_element(
        By.CSS_SELECTOR,
        "#formBuscaAluno button[type='submit']"
    ).click()

    WebDriverWait(navegador, 10).until(
        lambda driver: "João Victor Ribeiro"
        in driver.find_element(By.ID, "buscaResultados").text
    )

    navegador.find_element(
        By.XPATH,
        "//div[@id='buscaResultados']//button[contains(., 'Selecionar')]"
    ).click()

    WebDriverWait(navegador, 10).until(
        EC.visibility_of_element_located(
            (By.ID, "alunoEscolhido")
        )
    )

    # Preenche os dados da ocorrência.
    Select(
        navegador.find_element(By.ID, "tipo")
    ).select_by_value("1")

    Select(
        navegador.find_element(By.ID, "gravidade")
    ).select_by_value("1")

    campo_data = navegador.find_element(By.ID, "data")

    navegador.execute_script(
        """
        arguments[0].value = arguments[1];
        arguments[0].dispatchEvent(new Event('input', { bubbles: true }));
        arguments[0].dispatchEvent(new Event('change', { bubbles: true }));
        """,
        campo_data,
        date.today().isoformat()
    )

    navegador.find_element(
        By.ID, "relato"
    ).send_keys(
        f"Teste automatizado de registro pelo cargo {cargo}."
    )

    # Envia a ocorrência.
    navegador.find_element(By.ID, "btnRegistrar").click()

    # Aguarda a confirmação de sucesso.
    WebDriverWait(navegador, 15).until(
        EC.visibility_of_element_located(
            (By.CSS_SELECTOR, ".swal2-icon.swal2-success")
        )
    )

    print(
        f"\n[PASSOU] Ocorrência registrada com sucesso "
        f"pelo cargo {cargo}."
    )
