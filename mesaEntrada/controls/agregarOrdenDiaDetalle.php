<?php

require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/ordenDiaLogic.php';
require_once '../dataAccess/funciones.php';

if (isset($_POST)) {
    if (isset($_POST['iOrden'])) {
        $idOrdenDia = $_POST['iOrden'];
        if (isset($_POST['mesaEntrada1'])) {
            $asuntos = $_POST['mesaEntrada1'];
            foreach ($asuntos as $key => $asunto) {
                $estadoAlta = realizarAltaOrdenDiaDetalle($idOrdenDia, $asunto, 1);

                switch ($estadoAlta) {
                    case -1: die("Hubo un error al dar de alta el detalle");
                        break;
                }
            }
        }
        if (isset($_POST['mesaEntrada2'])) {
            $notas = $_POST['mesaEntrada2'];
            foreach ($notas as $key => $nota) {
                $estadoAlta = realizarAltaOrdenDiaDetalle($idOrdenDia, $nota, 2);

                switch ($estadoAlta) {
                    case -1: die("Hubo un error al dar de alta el detalle");
                        break;
                }
            }
        }
        if (isset($_POST['mesaEntradaD'])) {
            $descartados = $_POST['mesaEntradaD'];
            foreach ($descartados as $key => $descartado) {
                $estadoAlta = realizarAltaOrdenDiaDetalle($idOrdenDia, $descartado, 3);

                switch ($estadoAlta) {
                    case -1: die("Hubo un error al dar de alta el detalle");
                        break;
                }
            }
        }

        switch ($estadoAlta) {
            case -1: die("Hubo un error al dar de alta el detalle");
                break;
            case 1:
                echo "El detalle se dio de alta correctamente.";
                break;
        }
    } else {
        echo "Hubo un error en el sistema.";
    }
}
?>
