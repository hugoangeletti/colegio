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
$consultaExpediente = obtenerInfoExpediente($idMesaEntrada);
$expediente = $consultaExpediente->fetch_assoc();

switch ($expediente['TipoEspecialidad']) {
    case "E":
        $titulo = "Nueva Especialidad";
        break;
    case "J":
        $titulo = "Especialista Jerarquizado";
        break;
    case "C":
        $titulo = "Especialista Cosultor";
        break;
    case "R":
        $titulo = "Recertificación";
        break;
    case "A":
        $titulo = "Nueva Calificación Agregada";
        break;
    case "X":
        $titulo = "Especialista Exceptuado Art. 8";
        break;
    case "O":
        $titulo = "Especialista de Otro Distrito";
        break;
    case "N":
        $titulo = "Expedido por Ministerio de Salud de la Nación";
        break;
}
$consultaColegiado = obtenerColegiadoPorId($expediente['IdColegiado']);
$colegiado = $consultaColegiado->fetch_assoc();
$okey = true;
$matricula = $colegiado['Matricula'];

$consultaEspecialidad = obtenerEspecialidadPorId($expediente['IdEspecialidad']);
$especialidad = $consultaEspecialidad->fetch_assoc();

$colegiadoEspecialista = obtenerEspecialidadesPorColegiado($expediente['IdColegiado']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../css/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body onload="window.print()">
        <div id="expediente">
            <table class="encabezadoExpediente">
                <td>
                    <img src="../images/logosh.gif" width="100" height="100" longdesc="Colegio de Medicos - Distrito I" /><br />
                    <h2> Colegio de M&eacute;dicos <br />
                        Provincia  de Buenos Aires <br />
                        Distrito I </h2><br />
                    <h4>Av. 51 Nº 723 - Tel. 425-6311 - LA PLATA</h4>
                </td>
                <td class="tdHDR">
                    <h1>Expediente Nº <?php echo rellenarCeros($expediente['NumeroExpediente'], 5) ?>/<?php echo $expediente['AnioExpediente'] ?></h1><br />
                    <h4>Nº <?php echo trim(rellenarCeros($idMesaEntrada, 8)) ?></h4><br /><br />
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
            <br/>
            <?php
            echo "<h3 style='text-align:center;'>Solicitud de " . $titulo . "</h3>";
            echo "<h1 style='text-align:center;'>" . utf8_encode($especialidad['Especialidad']) . "</h1>";
            ?>
            <br/><br/>
            <?php
            require 'mostrarColegiadoImprimir.php';

            $fechaMatriculacionInvertir = explode("-", $colegiado['FechaMatriculacion']);
            $fechaMatriculacion = $fechaMatriculacionInvertir[2] . "/" . $fechaMatriculacionInvertir[1] . "/" . $fechaMatriculacionInvertir[0];

            $fechaNacimientoInvertir = explode("-", $colegiado['FechaNacimiento']);
            $fechaNacimiento = $fechaNacimientoInvertir[2] . "/" . $fechaNacimientoInvertir[1] . "/" . $fechaNacimientoInvertir[0];
            ?>
            <table>
                <tr>
                    <td><b>Fecha de Matriculación:</b></td>
                    <td><?php echo $fechaMatriculacion ?></td>
                    <td>(Antigüedad <?php echo calcularEdad($colegiado['FechaMatriculacion']); ?> años)</td>
                </tr>
                <tr>
                    <td><b>Fecha de Nacimiento:</b></td>
                    <td><?php echo $fechaNacimiento ?></td>
                    <td>(Edad <?php echo calcularEdad($colegiado['FechaNacimiento']) ?> años)</td>
                </tr>
            </table>
            <br/>
            <table style="width: 100%">
                <?php
                if ($colegiadoEspecialista) {
                    if ($colegiadoEspecialista->num_rows != 0) {
                        ?>

                        <tr>
                            <td><h4>Especialidades del Colegiado</h4></td>
                            <td><h4>Fecha Especialista</h4></td>
                            <td><h4>Fecha de Caducidad</h4></td>
                        </tr>
                        <?php
                        while ($cE = $colegiadoEspecialista->fetch_assoc()) {
                            $fechaEspecialistaInvertir = explode("-", $cE['FechaEspecialista']);
                            $fechaEspecialista = $fechaEspecialistaInvertir[2] . "/" . $fechaEspecialistaInvertir[1] . "/" . $fechaEspecialistaInvertir[0];

                            if (!is_null($cE['FechaVencimiento'])) {
                                $fechaVencimientoInvertir = explode("-", $cE['FechaVencimiento']);
                                $fechaVencimiento = $fechaVencimientoInvertir[2] . "/" . $fechaVencimientoInvertir[1] . "/" . $fechaVencimientoInvertir[0];
                            } else {
                                $fechaVencimiento = "";
                            }
                            ?>
                            <tr>
                                <td><?php echo utf8_encode($cE['NombreEspecialidad']) ?></td>
                                <td><?php echo $fechaEspecialista ?></td>
                                <td><?php echo $fechaVencimiento ?></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </table>
            <fieldset style="height: 400px; width: 800px">
                <legend>Nota</legend>
                <p style="padding: 10px;font-size: 25px;font-weight: bold;">Dejo constancia que lo presentado ante este Colegio de Médicos tiene caracter de declaración jurada.</p>
                <p style="padding: 10px;margin-top: 250px;font-size: 25px;font-weight: bold;">Firma:</p>
            </fieldset>
            <div class="lineaBajoTabla"></div>
            <p class="textoBajoTabla">User: <?php echo $_SESSION['idUsuario'] . " - " . $_SESSION['user'] ?></p>
            <?php
// Establecer la zona horaria predeterminada a usar. Disponible desde PHP 5.1
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            ?>
            <p> 
                Emitido el: <?php echo date("d/m/Y h:i:s a") ?>
            </p>
        </div>
        <div class="saltopagina"></div>
        <h4>Solicitud de <?php echo $titulo; ?></h4>
        <h4><?php echo utf8_encode($especialidad['Especialidad']); ?></h4>
        <br/><br/>
        <?php
        require 'mostrarColegiado.php';

        $fechaMatriculacion = invertirFecha($colegiado['FechaMatriculacion']);

        $fechaNacimiento = invertirFecha($colegiado['FechaNacimiento']);
        ?>
        <table>
            <tr>
                <td><b>Fecha de Matriculación:</b></td>
                <td><?php echo $fechaMatriculacion ?></td>
                <td>(Antigüedad <?php echo calcularEdad($colegiado['FechaMatriculacion']); ?> años)</td>
            </tr>
            <tr>
                <td><b>Fecha de Nacimiento:</b></td>
                <td><?php echo $fechaNacimiento ?></td>
                <td>(Edad <?php echo calcularEdad($colegiado['FechaNacimiento']) ?> años)</td>
            </tr>
        </table>
        <br/>
        <table style="width: 100%">
            <?php
            $colegiadoEspecialista = obtenerEspecialidadesPorColegiado($expediente['IdColegiado']);
            if ($colegiadoEspecialista) {
                if ($colegiadoEspecialista->num_rows != 0) {
                    ?>

                    <tr>
                        <td><h4>Especialidades del Colegiado</h4></td>
                        <td><h4>Fecha Especialista</h4></td>
                        <td><h4>Fecha de Caducidad</h4></td>
                    </tr>
                    <?php
                    while ($cE = $colegiadoEspecialista->fetch_assoc()) {
                        $fechaEspecialistaInvertir = explode("-", $cE['FechaEspecialista']);
                        $fechaEspecialista = $fechaEspecialistaInvertir[2] . "/" . $fechaEspecialistaInvertir[1] . "/" . $fechaEspecialistaInvertir[0];

                        if (!is_null($cE['FechaVencimiento'])) {
                            $fechaVencimientoInvertir = explode("-", $cE['FechaVencimiento']);
                            $fechaVencimiento = $fechaVencimientoInvertir[2] . "/" . $fechaVencimientoInvertir[1] . "/" . $fechaVencimientoInvertir[0];
                        } else {
                            $fechaVencimiento = "";
                        }
                        ?>
                        <tr>
                            <td><?php echo utf8_encode($cE['NombreEspecialidad']) ?></td>
                            <td><?php echo $fechaEspecialista ?></td>
                            <td><?php echo $fechaVencimiento ?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
        </table>
    </body>
</html>