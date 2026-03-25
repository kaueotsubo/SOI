<?php
session_start();

// 1. PROTEÇÃO DE ACESSO: Verifica se está logado (Pulseira VIP)
// Faltava isso no topo do seu arquivo original para barrar curiosos!
if (!isset($_SESSION['idDirecao']) && !isset($_SESSION['idAssistente'])) {
    die("Acesso negado. Você precisa estar logado.");
}

// 2. VERIFICAÇÃO DO CARIMBO (Token CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se o token não veio, ou se é diferente do que guardamos na sessão -> Bloqueia!
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Erro de Segurança: Tentativa de ação não autorizada (CSRF).");
    }
}

// Puxa as configurações (Conexão com o banco)
require_once '../classe/config.php';
// Puxa o Gateway
require_once '../classe/gateway/ocorrenciaGateway.php';

$errorMessage = "";

try {
    // Configura gateway
    OcorrenciaGateway::setConnection($pdo);
    $gateway = new OcorrenciaGateway();

    // Verifica se o formulário foi submetido (aqui o token já foi validado!)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cpf = $_POST['cpf'] ?? null;
        $cursoId = $_POST['cursoid'] ?? null;
        $gravidadeId = $_POST['gravidadeid'] ?? null;
        $tipoId = $_POST['tipoid'] ?? null;
        $ano = $_POST['ano'] ?? null;

        if (!$cpf || !$cursoId || !$gravidadeId || !$tipoId) {
            throw new Exception("Todos os campos são obrigatórios.");
        }

        // Restante do seu código continua idêntico...
        $stmt = $pdo->prepare("SELECT idAluno FROM aluno WHERE cpf = ?");
        $stmt->execute([$cpf]);
        $aluno = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$aluno) {
            throw new Exception("Aluno não encontrado para o CPF informado.");
        }

        $ocorrencia = new stdClass();
        $ocorrencia->idAluno = $aluno->idAluno;
        $ocorrencia->idCurso = $cursoId;
        $ocorrencia->idGravidade = $gravidadeId;
        $ocorrencia->idTipoOcorrencia = $tipoId;
        $ocorrencia->ano = $ano;

        $gateway->save($ocorrencia);

        // Redireciona de volta
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