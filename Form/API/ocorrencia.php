<?php
session_start();

if (!isset($_SESSION['idUsuario'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "error" => "Acesso negado. Você precisa estar logado."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Erro de Segurança: Tentativa de ação não autorizada (CSRF).");
    }
}

require_once '../classe/config.php';
require_once '../classe/gateway/ocorrenciaGateway.php';

$errorMessage = "";

try {
    OcorrenciaGateway::setConnection($pdo);
    $gateway = new OcorrenciaGateway();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cpf = $_POST['cpf'] ?? null;
        $gravidadeId = $_POST['gravidadeid'] ?? null;
        $tipoId = $_POST['tipoid'] ?? null;
        $ano = $_POST['ano'] ?? null;

        if (!$cpf || !$gravidadeId || !$tipoId || !$ano) {
            throw new Exception("Todos os campos são obrigatórios.");
        }

        $stmt = $pdo->prepare("SELECT idAluno, idCurso FROM aluno WHERE cpf = ?");
        $stmt->execute([$cpf]);
        $aluno = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$aluno) {
            throw new Exception("Aluno não encontrado para o CPF informado.");
        }

        $ocorrencia = new stdClass();
        $ocorrencia->idAluno = $aluno->idAluno;
        $ocorrencia->idCurso = $aluno->idCurso;
        $ocorrencia->idGravidade = $gravidadeId;
        $ocorrencia->idTipoOcorrencia = $tipoId;
        $ocorrencia->ano = $ano;

        $gateway->save($ocorrencia);

        // Redireciona de volta
        if (isset($_SESSION['cargo']) && $_SESSION['cargo'] === 'direcao') {
            header("Location: ../home.php");
            exit();
        } elseif (isset($_SESSION['cargo']) && $_SESSION['cargo'] === 'assistente') {
            header("Location: ../homea.php");
            exit();
        } else {
            // Caso de segurança, se o cargo não for reconhecido, expulsa
            header("Location: ../index.php");
            exit();
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>