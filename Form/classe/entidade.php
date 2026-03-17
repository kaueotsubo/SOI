<?php

    //Superclasse entidade
    class entidade {
        //Atributos;
        protected $data;
        private static $conn;

        //Método __set()
        function __set($prop, $value) {
            $this->data[$prop] = $value;
        }//Fim do método __set()

        //Método __get()
        function __get($prop) {
            return $this->data[$prop];
        }//Fim do método __get()

    }//Fim da classe entidade

?>