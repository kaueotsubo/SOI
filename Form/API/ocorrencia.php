<?php
session_start();

// Puxa as configurações (Conexão com o banco)
require_once '../classe/config.php';
// Puxa o Gateway
require_once '../classe/gateway/ocorrenciaGateway.php';

$errorMessage = ""; // Variável para mensagens de erro

try {

    // Configura gateway
    OcorrenciaGateway::setConnection($pdo);
    $gateway = new OcorrenciaGateway();

    // Verifica se o formulário foi submetido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Pega dados do formulário
        $cpf = $_POST['cpf'] ?? null;
        $cursoId = $_POST['cursoid'] ?? null;
        $gravidadeId = $_POST['gravidadeid'] ?? null;
        $tipoId = $_POST['tipoid'] ?? null;
        $ano = $_POST['ano'] ?? null;

        // Validação
        if (!$cpf || !$cursoId || !$gravidadeId || !$tipoId) {
            throw new Exception("Todos os campos são obrigatórios.");
        }

        // Busca idAluno pelo CPF
        $stmt = $pdo->prepare("SELECT idAluno FROM aluno WHERE cpf = ?");
        $stmt->execute([$cpf]);
        $aluno = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$aluno) {
            throw new Exception("Aluno não encontrado para o CPF informado.");
        }

        // Preenche objeto ocorrencia
        $ocorrencia = new stdClass();
        $ocorrencia->idAluno = $aluno->idAluno;
        $ocorrencia->idCurso = $cursoId;
        $ocorrencia->idGravidade = $gravidadeId;
        $ocorrencia->idTipoOcorrencia = $tipoId;
        $ocorrencia->ano = $ano;

        // Salva usando o gateway
        $gateway->save($ocorrencia);

        if (isset($_SESSION['idDirecao'])) {
        header("Location: ../home.php");
        exit();
        } elseif (isset($_SESSION['idAssistente'])) {
        header("Location: ../homea.php");
        exit();
    }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
