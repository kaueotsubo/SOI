
import os
import pytest

from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


@pytest.mark.parametrize(
    "perfil",
    ["PROFESSOR", "MONITOR"]
)
def test_api_tipos_bloqueia_operacao_sem_permissao(
    navegador,
    perfil
):
    email = os.getenv(f"{perfil}_EMAIL")
    senha = os.getenv(f"{perfil}_SENHA")

    if not email or not senha:
        pytest.skip(
            f"Credenciais de {perfil} não configuradas."
        )

    # Realiza login
    navegador.find_element(By.ID, "email").send_keys(email)
    navegador.find_element(By.ID, "senha").send_keys(senha)

    navegador.find_element(
        By.CSS_SELECTOR,
        'button[name="entrar"]'
    ).click()

    WebDriverWait(navegador, 10).until(
        EC.url_contains("homea.php")
    )

    # Envia POST para a API usando a sessão autenticada
    resultado = navegador.execute_async_script("""
        const finalizar = arguments[0];

        fetch('api/TipoOcorrenciaAPI.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
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

    assert resultado["status"] == 403, (
        f"{perfil} deveria receber HTTP 403, "
        f"mas recebeu HTTP {resultado['status']}."
    )

    print(
        f"\n[PASSOU] {perfil} bloqueado na API: "
        f"HTTP {resultado['status']}"
    )
