<?php

require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

//$codigoEspecial = array(3, 2, 27, 22, 12, 13, 24, 28, 17, 16, 15); //Arreglo con los códigos de cancelacion.

if (isset($_POST)) {
    $idColegiado = $_POST['idColegiado'];
    $tipoRemitente = "C";
    $idTipoMesaEntrada = "1";
    $observaciones = $_POST['observaciones'];
    $idTipoMovimiento = $_POST['tipoMovimiento'];
    $idMotivo = $_POST['motivo'];
    if($_POST['idPatologia']){
        $idPatologia = $_POST['idPatologia'];
    }else{
        $idPatologia = NULL;
    }
    $ColORem = "IdColegiado";
    if (isset($_POST['distrito'])) {
        $distrito = $_POST['distrito'];
    } else {
        $distrito = 0;
    }

    if (isset($_POST['tieneOS'])) {
        $tieneOS = $_POST['tieneOS'];
    } else {
        $tieneOS = null;
    }

    if (isset($_POST['obrasocialjubilado'])) {
        $obraSocial = $_POST['obrasocialjubilado'];
    } else {
        $obraSocial = NULL;
    }
    //Invierte la fecha del Post
    //$fechaDesde = $_POST['fechaDesde'];

    if ($idColegiado != "") {
        if ($idTipoMovimiento != "") {
            if (!validateDate($_POST['fechaDesde'], 'd-m-Y')) {
                $estadoAlta = -5;
            } elseif (!is_null($tieneOS) && ($tieneOS == 'S') && ((is_null($obraSocial))|| ($obraSocial == ""))) {
                $estadoAlta = 99;
            } else {
                $fechaDesdeInvertir = explode("-", $_POST['fechaDesde']);
                if ((1 > $fechaDesdeInvertir[0]) || ($fechaDesdeInvertir[0] > 31) || (1 > $fechaDesdeInvertir[1]) || ($fechaDesdeInvertir[1] > 12) || (1900 > $fechaDesdeInvertir[2]) || ($fechaDesdeInvertir[2] > 2500)) {
                    $estadoAlta = -10;
                } else {
                    $fechaDesde = $fechaDesdeInvertir[2] . "-" . $fechaDesdeInvertir[1] . "-" . $fechaDesdeInvertir[0];

                    $consultaYaHay = obtenerMovimientoPorIdPorIdColegiado($idColegiado, $idTipoMovimiento);

                    if (!$consultaYaHay) {
                        $estadoAlta = 89;
                    } else {
                        if ($consultaYaHay->num_rows == 0) {
                            $idMesaEntrada = realizarAltaMesaEntrada($idColegiado, $tipoRemitente, $ColORem, $idTipoMesaEntrada, $observaciones);

                            $estadoAlta = realizarAltaMovimiento($idMesaEntrada, $idTipoMovimiento, $fechaDesde, $idMotivo, $distrito, $obraSocial, $idPatologia);

                            if ($estadoAlta == 1) {
                                if ($idTipoMovimiento == 20) {
                                    $estadoAlta = realizarImpactoRehabilitacion($idColegiado, $fechaDesde, $idMesaEntrada);
                                    //$estadoAlta = realizarImpactoRehabilitacion($idColegiado, $fechaDesde);
                                } else {
                                    $estadoAlta = realizarImpactoMovimiento($idColegiado, $idTipoMovimiento, $fechaDesde, $distrito, $idMesaEntrada, $idPatologia);
                                    //$estadoAlta = realizarImpactoMovimiento($idColegiado, $idTipoMovimiento, $fechaDesde, $distrito);
                                    /* if(in_array($idTipoMovimiento, $codigoEspecial))
                                      { */
                                    switch ($idTipoMovimiento) {
                                        case 2:
                                            //A pedido de Marchetti(04/02/2015)
                                            //no se le borran las cuotas mientras tenga cancelación transitoria
                                            break;
                                        case 5:
                                            //Ingreso definitivo al distrito I
                                            $estadoAlta = realizarImpactoDeuda($idColegiado);
                                            break;
                                        case 7:
                                            //Fallecido
                                            $estadoAlta = realizarImpactoCancelacionFallecido($idColegiado);
                                            break;
                                        case 10:
                                            //Colegiado del distrito I, inscripto a otro distrito
                                            $estadoAlta = realizarImpactoDeuda($idColegiado);
                                            break;
                                        default :
                                            $estadoAlta = realizarImpactoCancelacion($idColegiado, $fechaDesde);
                                            break;
                                    }

                                    //}
                                }
                            }
                        } else {
                            $estadoAlta = 89;
                        }
                    }
                }
            }
        } else {
            $estadoAlta = -2;
        }
    } else {
        $estadoAlta = -3;
    }
    if ($estadoAlta == 1) {
        $text = "El movimiento se dio de alta correctamente.";
    } else {
        if ($estadoAlta == 89) {
            $text = "El trámite que desea realizar ya se efectuó el día de hoy.";
        } elseif ($estadoAlta == 99) {
            $text = "Eligió que tiene obra social o cobertura de salud. Se olvidó de cargarla.";
        } else {
            $text = "Hubo un error al dar de alta el movimiento. Intente nuevamente.";
        }
    }
}
$dev = array(
    "estado" => $estadoAlta,
    "texto" => $text,
    "importe" => 0
);

echo json_encode($dev);
?>
