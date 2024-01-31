<?php
require_once 'seguridad.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

$inicios = array(1, 26, 51, 76, 101, 126, 151, 176, 201, 226, 251, 276, 301, 326, 351);
$fines = array(25, 50, 75, 100, 125, 150, 175, 200, 225, 250, 275, 300, 325, 350);

if (isset($_GET['idM'])) {
    $idMesaEntrada = $_GET['idM'];
} else {
    $idMesaEntrada = -1;
}

if (isset($_GET['idR']) && ($_GET['idR'] != "")) {
    $idRemitente = $_GET['idR'];
} else {
    $idRemitente = -1;
}

$remitente = obtenerRemitentePorId($idRemitente);

if ($remitente) {
    if ($remitente->num_rows > 0) {
        $datoRemitente = $remitente->fetch_assoc();
        $nombreRemitente = $datoRemitente['Nombre'];
    }
}
$notasIncluyenMovimiento = obtenerMovimientosPorNota($idMesaEntrada);

if (!$notasIncluyenMovimiento) {
    die("Hubo un error en el sistema.");
} else {
    if ($notasIncluyenMovimiento->num_rows == 0) {
        die("No existe ninguna orden con esos parámetros.");
    } else {
        $i = 1;
        while ($tabla = $notasIncluyenMovimiento->fetch_assoc()) {
            $length = strlen($i);
            //mb_substr($i, $length-1, $length)
            if (in_array($i, $inicios)) {
                ?>
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                        <link href="../css/style.css" rel="stylesheet" type="text/css" />
                    </head>
                    <body onload="window.print();">
                        <table class="encabezadoPlanilla">
                            <td>
                                <img src="../images/logosh.gif" width="70" height="70" longdesc="Colegio de Medicos - Distrito I" /><br />
                            </td>
                            <td>
                                <h4> Colegio de M&eacute;dicos Distrito I<br /></h4>
                                Calle 51 Nº 723. La Plata - Tel/Fax: 425-6311.<br />
                                E-Mail: <span class="subrayado">info@colmed1.org.ar</span><br />
                                Web: <span class="subrayado">www.colmed1.org.ar</span>
                            </td>
                        </table>
                        <br />
                        <div id="titulo">
                            <h2>Movimientos Otros Distritos</h2>
                            <h3>Nota Nº <?php echo $idMesaEntrada ?><?php
                                if (isset($nombreRemitente)) {
                                    echo " | " . utf8_encode($nombreRemitente);
                                }
                                ?></h3>
                        </div>
                        <br />
                        <div class="tablaPlanillaMovimientoDistritos">
                        <table>
                            <tr>
                                <td><h4>Matrícula</h4></td>
                                <td><h4>Apellido y Nombre</h4></td>
                                <td><h4>Movimiento</h4></td>
                                <td><h4>Fecha Desde</h4></td>
                                <td><h4>Fecha Hasta</h4></td>
                                <td><h4>Distrito Cambio</h4></td>
                            </tr>
                            <?php
                        }
                        ?>


                        <tr class="textoChico"> 
                            <td><?php echo $tabla['Matricula'] ?></td>
                            <td style="width: 300px"><?php echo utf8_encode($tabla['Apellido']) . " " . utf8_encode($tabla['Nombres']); ?></td>
                            <td style="width: 300px"><?php echo utf8_encode($tabla['NombreMovimiento']) ?></td>
                            <td style="width: 80px"><?php echo invertirFecha($tabla['FechaDesde']) ?></td>
                            <td style="width: 80px"><?php
                                if (!is_null($tabla['FechaHasta'])) {
                                    echo invertirFecha($tabla['FechaHasta']);
                                }
                                ?></td>
                            <td><?php echo $tabla['DistritoCambio'] ?></td>
                        </tr>
                        <?php
                        ?>
                        <?php
                        //mb_substr($i, $length-1, $length)
                        if (in_array($i, $fines)) {
                            $yaFinaliza = true;
                            ?>
                        </table>
                        </div>
                        <br/>
                        <p class="textoBajoTablaPlanilla">User: <?php echo $_SESSION['idUsuario'] . " - " . $_SESSION['user'] ?></p>
                        <?php
                        // Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
                        date_default_timezone_set('America/Argentina/Buenos_Aires');
                        ?>
                        <p>Emitido el: <?php echo date("d/m/Y h:i:s a") ?></p>
                    </body>
                </html>
                <?php
            } else {
                $yaFinaliza = false;
            }
            $i++;
        }


        if (!$yaFinaliza) {
            ?>

            </table>
                </div>
            <br/>
            <p class="textoBajoTablaPlanilla">User: <?php echo $_SESSION['idUsuario'] . " - " . $_SESSION['user'] ?></p>
            <?php
            // Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            ?>
            <p>Emitido el: <?php echo date("d/m/Y h:i:s a") ?></p>
            </body>
            </html>
            <?php
        }
    }
}
?>