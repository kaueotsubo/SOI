
import os
import pytest

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


@pytest.mark.parametrize(
    "perfil, pagina_protegida, pagina_destino",
    [
        ("PROFESSOR", "cadastro.php", "homea.php"),
        ("MONITOR", "cadastro.php", "homea.php"),
        ("PROFESSOR", "ocorrencias.php", "homea.php"),
        ("MONITOR", "ocorrencias.php", "homea.php"),
        ("PROFESSOR", "tipos-ocorrencia.php", "homea.php"),
        ("MONITOR", "tipos-ocorrencia.php", "homea.php"),
        ("PROFESSOR", "home.php", "homea.php"),
        ("MONITOR", "home.php", "homea.php"),
    ]
)
def test_bloqueio_paginas_direcao(
    navegador,
    perfil,
    pagina_protegida,
    pagina_destino
):
    email = os.getenv(f"{perfil}_EMAIL")
    senha = os.getenv(f"{perfil}_SENHA")

    if not email or not senha:
        pytest.skip(f"Credenciais de {perfil} não configuradas.")

    # Realiza login
    navegador.find_element(By.ID, "email").send_keys(email)
    navegador.find_element(By.ID, "senha").send_keys(senha)

    navegador.find_element(
        By.CSS_SELECTOR,
        'button[name="entrar"]'
    ).click()

    WebDriverWait(navegador, 10).until(
        EC.url_contains(pagina_destino)
    )

    # Tenta acessar diretamente a página protegida
    navegador.get(
        f"{navegador.current_url.rsplit('/', 1)[0]}/{pagina_protegida}"
    )

    # Aguarda o redirecionamento
    WebDriverWait(navegador, 10).until(
        EC.url_contains(pagina_destino)
    )

    assert pagina_destino in navegador.current_url

    print(
        f"\n[PASSOU] {perfil} bloqueado em {pagina_protegida}"
    )
