//Carregar Gravidade no select
fetch("./api/gravidade.php")
  .then(res => res.json())
  .then(gravidade => {
    const select = document.getElementById("gravidadeid");
    gravidade.forEach(gravidade => {
      const option = document.createElement("option");
      option.value = gravidade.idGravidade;   // IMPORTANTE -> pega o ID
      option.textContent = gravidade.nivel; // mostra o nome
      select.appendChild(option);
    });
  });

// carregar tipos de ocorrencia no select
// quando o usuário escolher uma gravidade, carrega os tipos relacionados
const gravidadeSelect = document.getElementById("gravidadeid");
const tipoSelect = document.getElementById("tipoid");

// quando o usuário escolher uma gravidade
gravidadeSelect.addEventListener("change", () => {
  const idGravidade = gravidadeSelect.value;

  // limpa as opções antigas
  tipoSelect.innerHTML = "<option value=''>Carregando...</option>";

  // busca os tipos filtrados
  fetch("./api/tipoocorrencia.php?idGravidade=" + idGravidade)
    .then(res => res.json())
    .then(tipos => {
      tipoSelect.innerHTML = "<option value=''>Selecione o tipo</option>"; // reset
      tipos.forEach(tipo => {
        const option = document.createElement("option");
        option.value = tipo.idTipoOcorrencia;
        option.textContent = tipo.nomeTipo;
        tipoSelect.appendChild(option);
      });
    });
});

// Máscara automática para o campo de CPF
const inputCpf = document.getElementById('cpf');
const feedbackAluno = document.getElementById('feedback-aluno');

if (inputCpf) {
    inputCpf.addEventListener('input', function (e) {
        let valor = e.target.value;

        valor = valor.replace(/\D/g, "");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

        // Atualiza o campo na tela com a formatação
        e.target.value = valor;

        if (valor.length === 14 && feedbackAluno) {
            // Se tem 14 caracteres, avisa que está buscando
            feedbackAluno.innerHTML = '<span style="color: #6c757d;">Procurando aluno...</span>';
            
            // Faz a requisição para a nossa nova API
            fetch(`./api/buscar_aluno.php?cpf=${valor}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Encontrou! Fica verde.
                        feedbackAluno.innerHTML = `<span style="color: #28a745;">✓ Aluno: ${data.nome} (${data.curso})</span>`;
                    } else {
                        // Não encontrou ou erro. Fica vermelho.
                        feedbackAluno.innerHTML = `<span style="color: #dc3545;">❌ ${data.error}</span>`;
                    }
                })
                .catch(error => {
                    console.error(error);
                    feedbackAluno.innerHTML = '<span style="color: #dc3545;">Erro ao conectar com o banco.</span>';
                });
        } else if (feedbackAluno) {
            // Se a pessoa apagar um número e ficar com menos de 14 caracteres, apaga a mensagem
            feedbackAluno.innerHTML = '';
        }
    });
}
