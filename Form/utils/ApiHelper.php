<?php

    //inicio da Class
    class ApiHelper{
        //captura o erro sem deixa amostra pro cliente
        public static function error(Exception $e, $context = "api"){
        //Salva o erro real e a linha onde ocorreu nos logs do servidor
        $logMessage = sprintf("[%s] Erro: %s | Arquivo: %s | Linha: %d", 
            $context, 
            $e->getMessage(), 
            $e->getFile(), 
            $e->getLine()
        );
        error_log($logMessage);

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);

        echo json_encode([
            "status" => "error",
            "message" => "Ocorreu um erro interno no servidor ao processar sua requisição."
        ]);

        exit;
        }   

        //Mesagem de sucesso
        public static function success($data, $statusCode = 200) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        
        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
        exit;
    }
    }