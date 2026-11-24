
import os
import urllib.request
import urllib.error

import pytest


BASE_URL = os.getenv(
    "BASE_URL",
    "http://localhost:8000/index.php"
)

BASE_API = BASE_URL.rsplit("/", 1)[0] + "/api/"


APIS_PROTEGIDAS = [
    "aluno.php",
    "curso.php",
    "gravidade.php",
    "ocorrencia.php",
    "TipoOcorrenciaAPI.php",
]


@pytest.mark.parametrize("api", APIS_PROTEGIDAS)
def test_api_exige_autenticacao(api):

    url = BASE_API + api

    requisicao = urllib.request.Request(
        url,
        method="GET"
    )

    try:
        with urllib.request.urlopen(requisicao) as resposta:
            status = resposta.status

    except urllib.error.HTTPError as erro:
        status = erro.code

    assert status == 401, (
        f"A API {api} deveria retornar HTTP 401, "
        f"mas retornou HTTP {status}."
    )

    print(f"\n[PASSOU] {api} bloqueou acesso sem sessão: HTTP {status}")
