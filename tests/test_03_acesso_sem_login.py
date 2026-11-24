
import pytest

from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC


PAGINAS_PROTEGIDAS = [
    "home.php",
    "homea.php",
    "alunos.php",
    "cadastro.php",
    "nova-ocorrencia.php",
    "ocorrencias.php",
    "tipos-ocorrencia.php",
]


@pytest.mark.parametrize("pagina", PAGINAS_PROTEGIDAS)
def test_bloqueio_sem_login(navegador, pagina):

    # Acessa diretamente uma página interna
    url = (
        navegador.current_url.rsplit("/", 1)[0]
        + "/"
        + pagina
    )

    navegador.get(url)

    # Aguarda o redirecionamento para o login
    WebDriverWait(navegador, 10).until(
        EC.url_contains("index.php")
    )

    assert navegador.current_url.endswith("index.php")

    print(f"\n[PASSOU] Acesso sem login bloqueado: {pagina}")
