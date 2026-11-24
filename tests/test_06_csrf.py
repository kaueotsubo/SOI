
import os
import pytest

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


@pytest.mark.parametrize(
    "cenario, token",
    [
        ("sem_token", ""),
        ("token_invalido", "token-falso-123"),
    ]
)
def test_protecao_csrf(navegador, cenario, token):

    email = os.getenv("DIRECAO_EMAIL")
    senha = os.getenv("DIRECAO_SENHA")

    if not email or not senha:
        pytest.skip("Credenciais da Direção não configuradas.")

    # Realiza login
    navegador.find_element(By.ID, "email").send_keys(email)
    navegador.find_element(By.ID, "senha").send_keys(senha)

    navegador.find_element(
        By.CSS_SELECTOR,
        'button[name="entrar"]'
    ).click()

    # Aguarda a conclusão do login
    WebDriverWait(navegador, 10).until(
        EC.url_contains("home.php")
    )

    # Confirma que estamos autenticados na página da Direção
    assert "home.php" in navegador.current_url, (
        f"Login não concluído. URL atual: {navegador.current_url}"
    )

    # Envia a requisição para a API
    resultado = navegador.execute_async_script("""
        const token = arguments[0];
        const finalizar = arguments[1];

        fetch('/api/TipoOcorrenciaAPI.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': token
            },
            body: JSON.stringify({})
        })
        .then(resposta => {
            finalizar({
                status: resposta.status,
                url: resposta.url
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
        f"Cenário {cenario}: esperado HTTP 403, "
        f"recebido HTTP {resultado['status']}. "
        f"URL: {resultado.get('url')}"
    )

    print(
        f"\n[PASSOU] CSRF {cenario}: "
        f"HTTP {resultado['status']}"
    )


def test_protecao_csrf_token_valido(navegador):
    import os

    from selenium.webdriver.common.by import By
    from selenium.webdriver.support.ui import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC

    email = os.getenv("DIRECAO_EMAIL")
    senha = os.getenv("DIRECAO_SENHA")

    if not email or not senha:
        pytest.skip("Credenciais da Direção não configuradas.")

    navegador.find_element(By.ID, "email").send_keys(email)
    navegador.find_element(By.ID, "senha").send_keys(senha)
    navegador.find_element(By.CSS_SELECTOR, 'button[name="entrar"]').click()

    WebDriverWait(navegador, 10).until(
        EC.url_contains("home.php")
    )

    resultado = navegador.execute_async_script("""
        const token = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;

        const finalizar = arguments[0];

        fetch('/api/TipoOcorrenciaAPI.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': token
            },
            body: JSON.stringify({})
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
    """)

    assert "erro" not in resultado, resultado

    assert resultado["status"] == 400, (
        f"Esperado HTTP 400 após passar pelo CSRF, "
        f"recebido HTTP {resultado['status']}"
    )

    print(
        "\n[PASSOU] CSRF token válido: "
        "HTTP 400 (token aceito, dados inválidos)"
    )
