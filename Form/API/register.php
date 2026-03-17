<?php

// Incluindo as classes necessárias
require_once __DIR__ . "/../classe/direcao.php";
require_once __DIR__ . "/../classe/entidade.php";
require_once __DIR__ . "/../classe/Gateway/direcaoGateway.php";
require_once __DIR__ . "/../classe/assistente.php";
require_once __DIR__ . "/../classe/Gateway/assistenteGateway.php";

// Definindo credenciais de banco de dados
require_once '../classe/config.php';
// A partir daqui, a variável $pdo já existe e está pronta para uso!

$errorMessage = ""; // Variável para armazenar a mensagem de erro

try {

    // Configurando a classe direcaoGateway com a conexão estabelecida
    direcaoGateway::setConnection($pdo);

    // Configurando a classe assistenteGateway com a conexão estabelecida
    assistenteGateway::setConnection($pdo);

    // Criando uma instância da classe direcaoGateway
    $direcaoGateway = new direcaoGateway;

    // Criando uma instância da classe assistenteGateway
    $assistenteGateway = new assistenteGateway;

    // Verificando se o formulário foi submetido
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['username'];
        $email = $_POST['email'];
        $senha = isset($_POST['senha']) ? $_POST['senha'] : '';;
        $role  = $_POST['role']; // pode ser "direcao" ou "assistente"

        // Validando os campos obrigatórios
        //se o papel for direção
        if ($role === 'direcao') {
            $data = new stdClass();
            $data->nomeDirecao = $nome;
            $data->emailDirecao = $email;
            $data->senhaDirecao = password_hash($senha, PASSWORD_BCRYPT);

            $existente = $direcaoGateway->all("emailDirecao = '{$email}'");
            if (count($existente) > 0) {
                $errorMessage = "Este e-mail já está em uso.";
            } else {
                $direcaoGateway->save($data);
                header('Location: ../index.html');
                exit();
            }
        }
        //Se o papel for assistente
        elseif ($role === 'assistente') {
            $data = new stdClass();
            $data->nomeAssistente = $nome;
            $data->emailAssistente = $email;
            $data->senhaAssistente = password_hash($senha, PASSWORD_BCRYPT);

            $existente = $assistenteGateway->all("emailAssistente = '{$email}'");
            if (count($existente) > 0) {
                $errorMessage = "Este e-mail já está em uso.";
            } else {
                $assistenteGateway->save($data);
                header('Location: ../index.html');
                exit();
            }
        } else {
            $errorMessage = "Por favor, preencha todos os campos obrigatórios.";
        }
    }
} catch (Exception $e) {
    // Capturando e exibindo erros
    print "Erro: " . $e->getMessage();
}
