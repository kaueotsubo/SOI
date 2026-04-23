document.getElementById('registerForm').addEventListener('submit', function (e) {
    // Busca os valores usando os IDs exatos que estão no HTML
    const senha = document.getElementById('senha').value;
    const confirmarSenha = document.getElementById('confpassword').value;

    // Se as senhas forem diferentes, bloqueia o envio e avisa o usuário
    if (senha !== confirmarSenha) {
        e.preventDefault(); // Impede o envio do formulário
        Swal.fire({
            icon: 'error',
            title: 'Ops!',
            text: 'As senhas não são iguais.',
            confirmButtonText: 'Tentar novamente',
            confirmButtonColor: '#0d6efd' // Cor azul do Bootstrap
        });
        document.getElementById('confpassword').classList.add('is-invalid');
        return;
    }
    // Se as senhas forem iguais, remove a classe de erro (caso exista)
});