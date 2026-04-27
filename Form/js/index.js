document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('erro') === '1') {
        
        // Dispara o alerta bonitão
        Swal.fire({
            icon: 'error',
            title: 'Acesso Negado',
            text: 'E-mail ou senha incorretos. Tente novamente!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545',
            background: '#ffffff',
            color: '#333333'
        });

        // Limpa a URL removendo o ?erro=1, para o aviso não repetir se o usuário der F5
        window.history.replaceState(null, null, window.location.pathname);
    }
});