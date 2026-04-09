<?php
session_start();

if (!isset($_SESSION['idUsuario']) || $_SESSION['cargo'] !== 'direcao') {
    http_response_code(403);
    echo json_encode(["success" => false, "error" => "Acesso negado. Apenas a direção pode realizar esta ação."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método inválido.");
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Erro de Segurança: Falha na validação do formulário (CSRF).");
}

require_once '../classe/config.php';

if (isset($_FILES['arquivo_csv']) && $_FILES['arquivo_csv']['error'] === UPLOAD_ERR_OK) {
    
    $arquivoTmp = $_FILES['arquivo_csv']['tmp_name'];
    
    $cadastrados = 0;
    $ignoradosCpf = 0;
    $ignoradosCurso = 0;

    try {
        $stmtCursos = $pdo->query("SELECT idCurso, nomeCurso FROM curso");
        $cursosBanco = [];
        
        while ($row = $stmtCursos->fetch(PDO::FETCH_ASSOC)) {
            $nomeNormalizado = mb_strtolower(trim($row['nomeCurso']), 'UTF-8');
            $cursosBanco[$nomeNormalizado] = $row['idCurso'];
        }

        // Prepara os statements do Aluno
        $stmtVerifica = $pdo->prepare("SELECT idAluno FROM aluno WHERE cpf = ?");
        $stmtInsert = $pdo->prepare("INSERT INTO aluno (matricula, nomeAluno, cpf, idCurso, status) VALUES (?, ?, ?, ?, 'ativo')");

        if (($handle = fopen($arquivoTmp, "r")) !== FALSE) {
            
            fgetcsv($handle, 1000, ","); // Pula o cabeçalho

            while (($dados = fgetcsv($handle, 1000, ",")) !== FALSE) {
                
                $matricula = trim($dados[0] ?? '');
                $nome = trim($dados[1] ?? '');
                $cpf = trim($dados[2] ?? '');
                $nomeCursoPlanilha = mb_strtolower(trim($dados[3] ?? ''), 'UTF-8'); 
                
                if (empty($nome) || empty($cpf) || empty($matricula) || empty($nomeCursoPlanilha)) {
                    continue; 
                }

                if (!isset($cursosBanco[$nomeCursoPlanilha])) {
                    // O curso que estava na planilha não existe no banco de dados! Pula esse aluno.
                    $ignoradosCurso++;
                    continue; 
                }
                
                // Se existe, pega o ID numérico dele!
                $idCursoCorreto = $cursosBanco[$nomeCursoPlanilha];

                // Verifica o CPF
                $stmtVerifica->execute([$cpf]);
                
                if ($stmtVerifica->rowCount() == 0) {
                    // Insere o aluno usando o ID do curso que o PHP descobriu sozinho
                    $stmtInsert->execute([$matricula, $nome, $cpf, $idCursoCorreto]);
                    $cadastrados++;
                } else {
                    $ignoradosCpf++;
                }
            }
            
            fclose($handle);
            
            // Relatório completo pra Direção
            echo "<script>
                    alert('Importação Concluída!\\n\\nCadastrados: $cadastrados\\nIgnorados (CPF já existe): $ignoradosCpf\\nIgnorados (Curso não encontrado no banco): $ignoradosCurso');
                    window.location.href = '../home.php';
                  </script>";
            exit();
            
        } else {
            throw new Exception("Não foi possível ler o arquivo.");
        }
    } catch (Exception $e) {
        echo "<script>
                alert('Erro ao processar o banco de dados: " . $e->getMessage() . "');
                window.history.back();
              </script>";
        exit();
    }

} else {
    echo "<script>
            alert('Por favor, selecione um arquivo válido.');
            window.history.back();
          </script>";
}
?>