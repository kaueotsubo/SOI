<?php
session_start();
// Credenciais do banco de dados
require_once '../classe/config.php';

try {

    $errorMessage = ""; // Variável para armazenar a mensagem de erro

    if (isset($_POST["entrar"])) {
        // Recebe os dados do formulário e faz a sanitização
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'];
        
            // Valida o email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
            } else {
                // Consulta preparada para evitar injeção de SQL
                $stmt = $pdo->prepare("SELECT idUsuario, email, senha, cargo FROM usuario WHERE email = :email LIMIT 1");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    // Compara a senha inserida com a senha no banco de dados usando password_verify
                    if (password_verify($senha, $row['senha'])) {
                        // Credenciais corretas, armazena as informações na sessão
                        $_SESSION['idUsuario'] = $row['idUsuario'];
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['cargo'] = $row['cargo']; // Certifique-se de que 'cargo' está sendo selecionado na consulta SQL

                        // Redireciona para a página de direção se o cargo for "direcao"
                        if (isset($_SESSION['cargo']) && $_SESSION['cargo'] === 'direcao') {
                            header("Location: ../home.php");
                            exit();   
                        }
                        // Redireciona para a página do assistente se o cargo for "assistente"
                        else if (isset($_SESSION['cargo']) && $_SESSION['cargo'] === 'assistente') {
                            header("Location: ../homea.php");
                            exit();   
                        }
                    } else {
                        // Senha incorreta
                        $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
                        header("Location: ../index.html");
                        exit();
                    }
                } else {
                    // Usuário não encontrado
                    $errorMessage = "Senha ou usuário incorretos. Por favor, tente novamente.";
                    header("Location: ../index.html");        
                }
            }    

    }
} catch (PDOException $e) {
    // Trata qualquer exceção relacionada ao banco de dados
    echo "Erro de conexão com o banco de dados: " . $e->getMessage();
}