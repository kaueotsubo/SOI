<?php

    //Classe Usuario
    require_once __DIR__ .'/entidade.php';
    require_once __DIR__ .'/gateway/usuarioGateway.php';
    class Usuario extends entidade {
        private static $conn;

        //Método setConnection()
        public static function setConnection (PDO $conn) {
            self::$conn = $conn;
            usuarioGateway::setConnection($conn);
        }//Fim do método setConnection

        //Método find()
        public static function find($codUsuario) {
            $usuarioGateway = new usuarioGateway;
            return $usuarioGateway->find($codUsuario, 'Usuario');
        }//Método find()

        //Método all()
        public static function all ($filter = '') {
            $usuarioGateway = new usuarioGateway;
            return $usuarioGateway->all($filter, 'Usuario');
        }//Fim do método all()

        //Método delete()
        public function delete () {
            $usuarioGateway = new usuarioGateway;
            return $usuarioGateway->delete($this->idUsuario);
        }//Fim do método delete()

        //Método save()
        public function save () {
            $usuarioGateway = new usuarioGateway;
            return $usuarioGateway->save((object)$this->data);
        }//Fim do método save()

    }//Fim da classe Usuario
?>