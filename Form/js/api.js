// carregar cursos no select
fetch("./api/cursos.php")
  .then(res => res.json())
  .then(cursos => {
    const select = document.getElementById("cursoid");
    cursos.forEach(curso => {
      const option = document.createElement("option");
      option.value = curso.idCurso;   // IMPORTANTE -> pega o ID
      option.textContent = curso.nomeCurso; // mostra o nome
      select.appendChild(option);
    });
  });

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

if (inputCpf) {
    inputCpf.addEventListener('input', function (e) {
        let valor = e.target.value;

        // 1. Remove tudo o que não for número (letras, espaços, etc)
        valor = valor.replace(/\D/g, "");

        // 2. Coloca o primeiro ponto (após 3 números)
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

        // 3. Coloca o segundo ponto (após mais 3 números)
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");

        // 4. Coloca o traço (antes dos últimos 2 números)
        valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

        // Atualiza o campo na tela com a formatação
        e.target.value = valor;
    });
}
