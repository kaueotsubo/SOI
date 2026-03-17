<?php
session_start();
// Credenciais do banco de dados
require_once '../classe/config.php';
// A partir daqui, a variável $pdo já existe e está pronta para uso!

try {

    $errorMessage = ""; // Variável para armazenar a mensagem de erro

    if (isset($_POST["entrar"])) {
        // Recebe os dados do formulário e faz a sanitização
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'];
        $role  = $_POST['role']; // pode ser "direcao" ou "assistente"

        //Se o papel for direção
        if ($role === 'direcao') {
            // Valida o email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
            } else {
                // Consulta preparada para evitar injeção de SQL
                $stmt = $pdo->prepare("SELECT idDirecao, emailDirecao, senhaDirecao FROM direcao WHERE emailDirecao = :emailDirecao LIMIT 1");
                $stmt->bindParam(':emailDirecao', $email, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    // Compara a senha inserida com a senha no banco de dados usando password_verify
                    if (password_verify($senha, $row['senhaDirecao'])) {
                        // Credenciais corretas, armazena as informações na sessão
                        $_SESSION['idDirecao'] = $row['idDirecao'];
                        $_SESSION['emaildirecao'] = $row['emailDirecao'];

                        // Redireciona para a página autenticada
                        header("Location: ../home.php");
                        exit(); // Encerra a execução após o redirecionamento
                    } else {
                        // Senha incorreta
                        $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
                        header("Location: ../index.html");
                    }
                } else {
                    // Usuário não encontrado
                    $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
                    header("Location: ../index.html");        
                }
            }    
        }

        //Se o papel for assistente
        elseif ($role === "assistente") {
            // Valida o email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
            } else {
                // Consulta preparada para evitar injeção de SQL
                $stmt = $pdo->prepare("SELECT idAssistente, emailAssistente, senhaAssistente FROM assistente WHERE emailAssistente = :emailAssistente LIMIT 1");
                $stmt->bindParam(':emailAssistente', $email, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    // Compara a senha inserida com a senha no banco de dados usando password_verify
                    if (password_verify($senha, $row['senhaAssistente'])) {
                        // Credenciais corretas, armazena as informações na sessão
                        $_SESSION['idAssistente'] = $row['idAssistente'];
                        $_SESSION['emailassistente'] = $row['emailAssistente'];

                        // Redireciona para a página autenticada
                        header("Location: ../homea.php");
                        exit(); // Encerra a execução após o redirecionamento
                    } else {
                        // Senha incorreta
                        $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
                        header("Location: ../index.html");
                    }
                } else {
                    // Usuário não encontrado
                    $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
                    header("Location: ../index.html");        
                }
            }
        }
    }
} catch (PDOException $e) {
    // Trata qualquer exceção relacionada ao banco de dados
    echo "Erro de conexão com o banco de dados: " . $e->getMessage();
}