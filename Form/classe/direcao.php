<?php

    //Classe Direcao
    require_once __DIR__ .'/entidade.php';
    require_once __DIR__ .'/gateway/direcaoGateway.php';
    class Direcao extends entidade {
        private static $conn;

        //Método setConnection()
        public static function setConnection (PDO $conn) {
            self::$conn = $conn;
            direcaoGateway::setConnection($conn);
        }//Fim do método setConnection

        //Método find()
        public static function find($codUsuario) {
            $direcaoGateway = new direcaoGateway;
            return $direcaoGateway->find($codUsuario, 'Direcao');
        }//Método find()

        //Método all()
        public static function all ($filter = '') {
            $direcaoGateway = new direcaoGateway;
            return $direcaoGateway->all($filter, 'Direcao');
        }//Fim do método all()

        //Método delete()
        public function delete () {
            $direcaoGateway = new direcaoGateway;
            return $direcaoGateway->delete($this->idDirecao);
        }//Fim do método delete()

        //Método save()
        public function save () {
            $direcaoGateway = new direcaoGateway;
            return $direcaoGateway->save((object)$this->data);
        }//Fim do método save()

    }//Fim da classe Direcao
?>
