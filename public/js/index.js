// Mostra os avisos que api/login.php manda por redirecionamento (?erro=1, ?erro=csrf).
(() => {
    const p = new URLSearchParams(location.search);
    if (!p.has('erro')) return;

    const codigo = p.get('erro');
    const mensagens = {
        csrf: 'Sua sessão expirou. Recarregue a página e tente novamente.',
        '1': 'E-mail ou senha incorretos. Por favor, tente novamente.',
    };

    Swal.fire({
        icon: 'error',
        title: 'Não foi possível entrar',
        text: mensagens[codigo] || 'Não foi possível entrar. Tente novamente.',
        confirmButtonColor: '#2b7a3d',
    });

    history.replaceState(null, '', location.pathname);
})();
