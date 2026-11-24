
import os
import pytest

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select, WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


@pytest.mark.parametrize(
    "modo, valor, mensagem",
    [
        (
            "nome",
            "Jo",
            "Digite pelo menos 3 letras do nome."
        ),
        (
            "nome",
            "João123",
            "Use apenas letras e espaços no nome."
        ),
        (
            "matricula",
            "12345",
            "A matrícula deve ter exatamente 19 dígitos numéricos."
        ),
        (
            "cpf",
            "123",
            "O CPF deve ter 11 dígitos."
        ),
        (
            "cpf",
            "11111111111",
            "O CPF não pode ter todos os dígitos iguais."
        ),
    ]
)
def test_validacao_busca_aluno(navegador, modo, valor, mensagem):

    email = os.getenv("DIRECAO_EMAIL")
    senha = os.getenv("DIRECAO_SENHA")

    if not email or not senha:
        pytest.skip("Credenciais da Direção não configuradas.")

    # Login
    navegador.find_element(By.ID, "email").send_keys(email)
    navegador.find_element(By.ID, "senha").send_keys(senha)
    navegador.find_element(
        By.CSS_SELECTOR,
        'button[name="entrar"]'
    ).click()

    WebDriverWait(navegador, 10).until(
        EC.url_contains("home.php")
    )

    # Acessa a página de alunos
    url_alunos = (
        navegador.current_url.rsplit("/", 1)[0]
        + "/alunos.php"
    )
    navegador.get(url_alunos)

    WebDriverWait(navegador, 10).until(
        EC.presence_of_element_located(
            (By.ID, "formBuscaAluno")
        )
    )

    # Seleciona o modo de busca
    campo_modo = Select(
        navegador.find_element(By.ID, "buscaModo")
    )
    campo_modo.select_by_value(modo)

    # Preenche o termo
    campo_termo = navegador.find_element(
        By.ID,
        "buscaTermo"
    )
    campo_termo.send_keys(valor)

    # Envia o formulário
    navegador.find_element(
        By.CSS_SELECTOR,
        '#formBuscaAluno button[type="submit"]'
    ).click()

    # Aguarda a mensagem de validação
    WebDriverWait(navegador, 5).until(
        lambda driver: mensagem in driver.find_element(
            By.ID,
            "buscaResultados"
        ).text
    )

    resultado = navegador.find_element(
        By.ID,
        "buscaResultados"
    ).text

    assert mensagem in resultado

    print(
        f"\n[PASSOU] Validação {modo}: "
        f"{mensagem}"
    )
