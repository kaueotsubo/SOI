// script.js

document.getElementById('registerForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Impede o envio do formulário para testar

    const senha = document.getElementById('password').value;
    const confirmarSenha = document.getElementById('confpassword').value;

    if (senha !== confirmarSenha) {
        alert('As senhas não são iguais');
        document.getElementById('confpassword').classList.add('is-invalid');
        return;
    }

    alert('Cadastro válido!');
});