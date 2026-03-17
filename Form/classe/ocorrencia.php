<?php

    //Classe ocorrencia
    require_once __DIR__ .'/entidade.php';
    require_once __DIR__ .'/Gateway/ocorrenciaGateway.php';
    class Ocorrencia extends entidade {
        private static $conn;

        //Método setConnection()
        public static function setConnection (PDO $conn) {
            self::$conn = $conn;
            OcorrenciaGateway::setConnection($conn);
        }//Fim do método setConnection

        //Método find()
        public static function find($codUsuario) {
            $OcorrenciaGateway = new OcorrenciaGateway;
            return $OcorrenciaGateway->find($codUsuario, 'ocorrencia');
        }//Método find()

        //Método all()
        public static function all ($filter = '') {
            $OcorrenciaGateway = new OcorrenciaGateway;
            return $OcorrenciaGateway->all($filter, 'ocorrencia');
        }//Fim do método all()

        //Método delete()
        public function delete () {
            $OcorrenciaGateway = new OcorrenciaGateway;
            return $OcorrenciaGateway->delete($this->idOcorrencia);
        }//Fim do método delete()

        //Método save()
        public function save () {
            $OcorrenciaGateway = new OcorrenciaGateway;
            return $OcorrenciaGateway->save((object)$this->data);
        }//Fim do método save()

    }//Fim da classe ocorrencia
?>