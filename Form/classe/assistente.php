<?php

    //Classe Assistente
    require_once __DIR__ .'/entidade.php';
    require_once __DIR__ .'/Gateway/assistenteGateway.php';
    class Assistente extends entidade {
        private static $conn;

        //Método setConnection()
        public static function setConnection (PDO $conn) {
            self::$conn = $conn;
            assistenteGateway::setConnection($conn);
        }//Fim do método setConnection

        //Método find()
        public static function find($codUsuario) {
            $assistenteGateway = new assistenteGateway;
            return $assistenteGateway->find($codUsuario, 'Assistente');
        }//Método find()

        //Método all()
        public static function all ($filter = '') {
            $assistenteGateway = new assistenteGateway;
            return $assistenteGateway->all($filter, 'Assistente');
        }//Fim do método all()

        //Método delete()
        public function delete () {
            $assistenteGateway = new assistenteGateway;
            return $assistenteGateway->delete($this->idAssistente);
        }//Fim do método delete()

        //Método save()
        public function save () {
            $assistenteGateway = new assistenteGateway;
            return $assistenteGateway->save((object)$this->data);
        }//Fim do método save()

    }//Fim da classe Assistente
?>