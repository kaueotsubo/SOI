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
      
        valor = valor.replace(/\D/g, "");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        e.target.value = valor;
    });
}
