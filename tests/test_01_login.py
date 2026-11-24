
import os
import pytest

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


@pytest.mark.parametrize(
    "perfil, pagina",
    [
        ("DIRECAO", "home.php"),
        ("PROFESSOR", "homea.php"),
        ("MONITOR", "homea.php"),
    ]
)
def test_login_por_perfil(navegador, perfil, pagina):

    # Recupera as credenciais do arquivo .env
    email = os.getenv(f"{perfil}_EMAIL")
    senha = os.getenv(f"{perfil}_SENHA")

    # Ignora o teste caso as credenciais não estejam configuradas
    if not email or not senha:
        pytest.skip(f"Credenciais de {perfil} não configuradas.")

    # Localiza os campos
    campo_email = navegador.find_element(By.ID, "email")
    campo_senha = navegador.find_element(By.ID, "senha")

    # Preenche os campos
    campo_email.send_keys(email)
    campo_senha.send_keys(senha)

    # Clica no botão de login
    navegador.find_element(
        By.CSS_SELECTOR,
        'button[name="entrar"]'
    ).click()

    # Aguarda o redirecionamento
    WebDriverWait(navegador, 10).until(
        EC.url_contains(pagina)
    )

    # Confirma que chegou à página esperada
    assert pagina in navegador.current_url

    print(f"\n[PASSOU] Login realizado: {perfil}")
