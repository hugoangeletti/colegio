<?php
require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

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
} else {
    $idMesaEntrada = obtenerNumeroHojaRuta();

    if (!$idMesaEntrada) {
        $idMesaEntrada = "";
    } else {
        $datoId = $idMesaEntrada->fetch_assoc();
        $idMesaEntrada = $datoId['IdMesaEntrada'];
    }
}
?>
<?php
$consultaDatoMesaEntrada = obtenerEntregaPorId($idMesaEntrada);

if (!$consultaDatoMesaEntrada) {
    die("Hubo un error en el sistema.");
} else {
    if ($consultaDatoMesaEntrada->num_rows == 0) {
        die("El sistema no registra movimiento en la mesa de entrada.");
    } else {
        $datoMesaEntrada = $consultaDatoMesaEntrada->fetch_assoc();
        if (is_null($datoMesaEntrada['IdColegiado'])) {
            die("El sistema no registra al colegiado que desea obtener.");
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../css/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body onload="window.print()">
        <div id='container'>
            <div id='titulo'>
                <h3>CONSTANCIA DE <?php echo pasarAMayuscula(utf8_encode($datoMesaEntrada['NombreTipoEntrega'])); ?></h3>
            </div>
            <br />
            <p class='textDerecha'>La Plata, <?php echo date("d") . " de " . obtenerNombreMes(date("Y-m-d")) . " de " . date("Y") ?></p>
            <br /><br />
            <p class='textCuerpo'>Firmo con la constancia de haber recibido <b><?php echo utf8_encode($datoMesaEntrada['Leyenda']); ?></b>.-</p>
            <br /><br /><br /><br />
            <p>Observación: _________________________________</p>
            <br /><br /><br /><br />
            <br /><br /><br /><br />
            <br /><br />
            <div class="firma">
                <p>Firma: _______________________________
                    <br /><br />
                    <?php
                    $consultaColegiado = obtenerColegiadoPorId($datoMesaEntrada['IdColegiado']);
                    if ($consultaColegiado) {
                        if ($consultaColegiado->num_rows != 0) {
                            $colegiado = $consultaColegiado->fetch_assoc();
                            ?>
                            <?php echo $colegiado['Apellido'] . " " . $colegiado['Nombres'] ?><br />
                            <b>MP:</b><?php echo $colegiado['Matricula'] ?>
                            <?php
                        }
                    }
                    ?>
                </p>
            </div>
            <div class='final'>
                <div class="lineaBajoTablaConsultorio"></div>
                <p class="textoBajoTablaConsultorio">Realizó: <?php echo $datoMesaEntrada['Usuario']; //$_SESSION['idUsuario'] . " - " . $_SESSION['user'] ?></p>
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