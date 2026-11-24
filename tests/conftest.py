
import os
from pathlib import Path

import pytest
from dotenv import load_dotenv

from selenium import webdriver
from selenium.webdriver.chrome.options import Options


# Caminho da pasta principal do projeto
ROOT_DIR = Path(__file__).resolve().parents[1]

# Carrega as configurações do arquivo .env
load_dotenv(ROOT_DIR / ".env")

# Endereço inicial do sistema
BASE_URL = os.getenv(
    "BASE_URL",
    "http://localhost:8000/index.php"
)


@pytest.fixture
def navegador():
    options = Options()

    # Permite executar sem abrir a janela do Chrome
    if os.getenv("HEADLESS", "false").lower() == "true":
        options.add_argument("--headless=new")

    options.add_argument("--start-maximized")
    options.add_argument("--disable-notifications")

    # Inicializa o Chrome
    driver = webdriver.Chrome(options=options)

    driver.set_page_load_timeout(30)

    # Abre o sistema SOI
    driver.get(BASE_URL)

    yield driver

    # Fecha o navegador ao terminar o teste
    driver.quit()
