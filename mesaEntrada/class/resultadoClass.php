<?php

    class resultado {
        
        public $error, $vacio, $exito;
        
        function __construct() {
            $this -> error = -1;
            $this -> vacio = 0;
            $this -> exito = 1;
        }
        
        public function getError() {
            $this -> error;
        }
        
        public function getVacio() {
            $this -> vacio;
        }
        
        public function getExito() {
            $this -> exito;
        }
        
        public function setError($datoError) {
            $this -> error = $datoError;
        }
        
        public function setVacio($datoVacio) {
            $this -> vacio = $datoVacio;
        }
        
        public function setExito($datoExito) {
            $this -> exito = $datoExito;
        }
        
        
    }
?>
