
def test_abertura_sistema(navegador):
    assert "SOI" in navegador.title or navegador.current_url
    print("\nSistema acessado:", navegador.current_url)
