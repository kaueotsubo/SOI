// js/lista.js - VERSÃO COM SWEETALERT2 E PROTEÇÃO CSRF

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', function() {
    fetch("api/lista.php") 
        .then(response => response.json())
        .then(data => {
            let tbody = document.querySelector("#tabelaOcorrencias tbody");
            let rowsHtml = ""; 

            data.forEach(o => {
                rowsHtml += `
                    <tr>
                        <td data-label="Data">${o.dataOcorrencia}</td>
                        <td data-label="Descrição">${o.descricao}</td>
                        <td data-label="Aluno">${o.nomeAluno}</td>
                        <td data-label="Curso">${o.nomeCurso}</td>
                        <td data-label="Gravidade">${o.nivel}</td>
                        <td data-label="Tipo">${o.nomeTipo}</td>
                        <td data-label="Ano">${o.ano}</td>
                        <td data-label="Ações">
                            <button class="btn btn-excluir" onclick="confirmarExclusao(${o.idOcorrencia})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
        })
        .catch(err => console.error("Erro ao carregar ocorrências:", err));
});


// Função para excluir uma única ocorrência
function confirmarExclusao(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Esta ação é permanente e não pode ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', 
        cancelButtonColor: '#3085d6', 
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            
            fetch(`api/excluir.php?id=${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-Token': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Excluído!',
                        'A ocorrência foi apagada com sucesso.',
                        'success'
                    ).then(() => {
                        location.reload(); 
                    });
                } else {
                    Swal.fire('Erro!', data.error || 'Falha ao excluir a ocorrência.', 'error');
                }
            })
            .catch(error => {
                console.error("Erro na requisição:", error);
                Swal.fire('Erro!', 'Ocorreu um erro ao tentar excluir a ocorrência.', 'error');
            });
        }
    });
}

// Função para excluir TODAS as ocorrências com dupla confirmação
function confirmarExcluirTudo() {
    Swal.fire({
        title: 'VOCÊ ESTÁ PRESTES A EXCLUIR TUDO!',
        text: "Tem certeza que deseja apagar TODAS as ocorrências?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            
            Swal.fire({
                title: 'PERIGO!',
                text: "Esta é sua última chance. Confirmar a exclusão de TODOS os registros permanentemente?",
                icon: 'error', 
                showCancelButton: true,
                confirmButtonColor: '#000', 
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'EXCLUIR TUDO DEFINITIVAMENTE',
                cancelButtonText: 'Me tire daqui!'
            }).then((secondResult) => {
                if (secondResult.isConfirmed) {
                    
                    fetch('api/excluir.php?tudo=true', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-Token': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Excluído!',
                                'Todas as ocorrências foram apagadas com sucesso.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', data.error || 'Falha ao excluir as ocorrências.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error("Erro na requisição:", error);
                        Swal.fire('Erro!', 'Ocorreu um erro ao tentar excluir as ocorrências.', 'error');
                    });
                }
            });
        }
    });
}