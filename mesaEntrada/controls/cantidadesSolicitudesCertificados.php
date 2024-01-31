<?php
require_once 'seguridad.php';

require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

$fechaDesde = $_GET['fD'];
$fechaHasta = $_GET['fH'];

$consultaConsultorio = obtenerSolicitudCertificadosPorFechas($fechaDesde, $fechaHasta);
?>

<table id="tablaEstadisticas">
    <?php
    if ($consultaConsultorio->num_rows != 0) {
        while ($row = $consultaConsultorio->fetch_assoc()) {
            ?>
            <tr>
                <td class="izquierda"><h4><?php echo utf8_encode($row['Detalle']) ?></h4></td>
                <td><?php echo $row['cantidad'] ?></td>
            </tr>
        <?php
    }
} else {
    ?>
        <tr>
            <td class="mensajeWARNING">No hay movimientos realizados en este período de tiempo.</td>
        </tr>
        <?php
    }
    ?>
</table>