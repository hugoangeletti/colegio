<?php
require_once 'seguridad.php';
require_once 'mesaEntrada/dataAccess/conection.php';
conectar();
require_once '/mesaEntrada/dataAccess/colegiadoLogic.php';
require_once '/mesaEntrada/dataAccess/tipoMovimientoLogic.php';
require_once '/mesaEntrada/dataAccess/mesaEntradaLogic.php';

$continua = TRUE;
if (isset($_GET['iME'])) {
    $idMesaEntrada = $_GET['iME'];
    $datofechaIngreso = obtenerFechaMesaEntrada($idMesaEntrada);
    if (!$datofechaIngreso) {
        $fechaIngreso = "";
    } else {
        $fechaIngreso = $datofechaIngreso->fetch_assoc();
    }
    $fechaIngresoInvertir = explode("-", $fechaIngreso['FechaIngreso']);
    $fecha = $fechaIngresoInvertir[2] . "/" . $fechaIngresoInvertir[1] . "/" . $fechaIngresoInvertir[0];
    $consultaDatoMesaEntrada = obtenerMovimientoPorId($idMesaEntrada);
} else {
    $continua = FALSE;
}

if (!$consultaDatoMesaEntrada || $continua) {
    die("Hubo un error en el sistema.");
} else {
    if ($consultaDatoMesaEntrada->num_rows == 0) {
        die("El sistema no registra movimiento en la mesa de entrada.");
        $continua = FALSE;
    } else {
        $datoMesaEntrada = $consultaDatoMesaEntrada->fetch_assoc();
        $consultaDatoColegiado = obtenerColegiadoPorId($datoMesaEntrada['IdColegiado']);

        if (!$consultaDatoColegiado) {
            die("Hubo un error en el sistema.");
            $continua = FALSE;
        } else {
            if ($consultaDatoColegiado->num_rows == 0) {
                die("El sistema no registra al colegiado que desea obtener.");
                $continua = FALSE;
            } else {
                $datoColegiado = $consultaDatoColegiado->fetch_assoc();
                
                $matricula = $datoColegiado['Matricula'];
                //Colsulto Estado Matricular
                if (!is_null($datoMesaEntrada['EstadoTesoreria'])) {
                    $estadoColegiado = $datoMesaEntrada['EstadoMatricular'];
                } else {
                    $estadoColegiado = $datoColegiado['Estado'];
                }
                $consultaEstadoMatricular = obtenerTipoMovimiento($estadoColegiado);

                if (!$consultaEstadoMatricular) {
                    echo "Hubo un error. Vuelva a intentar.";
                    $continua = FALSE;
                } else {
                    if ($consultaEstadoMatricular->num_rows == 0) {
                        echo "No corresponde a un estado matricular válido.";
                        $continua = FALSE;
                    } else {
                        $estadoMatricular = $consultaEstadoMatricular->fetch_assoc();
                        $matricularEstado = estadoColegiado($estadoMatricular['Estado']) . " (" . $estadoMatricular['DetalleCompleto'] . ")";
                        $continua = FALSE;
                    }
                }
            }
        }
    }
}
if ($continua) {
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <link href="../../public/css/style.css" rel="stylesheet" type="text/css" />
        </head>
        <body onload="window.print()">
            <div id='containerMovimiento'>
                <table class="encabezadoPlanilla">
                    <td>
                        <img src="../../public/images/logo_colmed1.gif" width="100" height="100" longdesc="Colegio de Medicos - Distrito I" /><br />
                    </td>
                    <td>    
                        <h4> Colegio de M&eacute;dicos Distrito I<br /></h4>
                        Calle 51 Nº 723. La Plata - Tel/Fax: 425-6311.<br />
                        E-Mail: <span class="subrayado">info@colmed1.org.ar</span><br />
                        Web: <span class="subrayado">www.colmed1.org.ar</span>
                    </td>
                    <td class="tdHDRMovimiento">
                        <h1>HOJA DE RUTA</h1><br />
                        <!--<h2>Nº <?php //echo trim(rellenarCeros($idMesaEntrada, 8))    ?></h2><br />-->
                        <h2>MESA ENTRADA Nº <?php echo trim(rellenarCeros($idMesaEntrada, 8)) ?></h2><br />
                        <h2>REUNIÓN MESA Nº </h2><div class="recuadroMovimiento"></div><!--<div class="recuadroMovimiento"><div class="NMesaIzq"></div><div class="NMesaDer"></div></div>--><br />
                        <h2>Fecha: <?php
    if (isset($fecha)) {
        echo $fecha;
    } else {
        echo date("d/m/Y");
    }
    ?></h2>
                        <br />
                    </td>
                </table>
    <?php
    require_once 'mostrarColegiadoImprimir.php';
    echo "<br>";
    ?>
                <table class="cuerpoHojaRutaMovimiento">
                    <th class="encabezadoTabla">Decisión de la Mesa Directiva</th>
                    <th class="encabezadoTabla">Firma</th>
                    <tr <?php
                        echo "class='cuerpoTablaMovimiento'";
    ?>>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
                <br/>
                    <div class="lineaBajoTablaMovimiento"></div>
                    <br/>
                <div class='finalMovimiento'>
                    <div class="lineaBajoTablaMovimiento"></div>
                    <p class="textoBajoTablaMovimiento">Realizó: <?php echo $datoMesaEntrada['Usuario']; //$_SESSION['idUsuario'] . " - " . $_SESSION['user'] ?></p>
                    <?php
                    // Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
                    date_default_timezone_set('America/Argentina/Buenos_Aires');
                    ?>
                    <p> 
                        Emitido el: <?php echo date("d/m/Y h:i:s a") ?>
                    </p>
                </div>
            </div>
        </body>
    </html>
    <?php
}
