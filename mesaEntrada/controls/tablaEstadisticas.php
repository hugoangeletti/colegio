<?php
require_once 'seguridad.php';

require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';


$cantidadMovimientos = obtenerMesaEntradaPorTipoPorFechas($fechaDesde, $fechaHasta, "1");
$cantidadEspecialidades = obtenerMesaEntradaPorTipoPorFechas($fechaDesde, $fechaHasta, "2");
$cantidadNotas = obtenerMesaEntradaPorTipoPorFechas($fechaDesde, $fechaHasta, "3");
$cantidadHabilitaciones = obtenerMesaEntradaPorTipoPorFechas($fechaDesde, $fechaHasta, "4");
$cantidadAutoprescripciones = obtenerMesaEntradaPorTipoPorFechas($fechaDesde, $fechaHasta, "7");
$cantidadNuevosMatriculados = obtenerNuevosMatriculadosPorFechas($fechaDesde, $fechaHasta);
$cantidadSolicitudCertificados = obtenerSolicitudCertificadosPorFechas($fechaDesde, $fechaHasta);
?>
<script type="text/javascript">
    $(function () {
        $(".verDetalle").click(function () {
            var href = $(this).attr("id");
            $.ajax({
                url: href,
                success: function (msg) {
                    $("#modalVerDetalle").html(msg);
                }
            });
            $("#modalVerDetalle").dialog({
                closeText: "cerrar",
                modal: true,
                minWidth: 680,
                minHeight: 100,
                width: 880,
                maxHeight: 450,
                maxWidth: 1000,
                resizable: true,
                title: "Ver Detalle"
            });
        });
    });

</script>
<table id="tablaEstadisticas">
    <tr>
        <td class="izquierda"><h4>Cantidad de Movimientos Matriculares</h4></td>
        <td><?php echo $cantidadMovimientos->num_rows ?></td>
        <td><a class="verDetalle" id="cantidadesMovimientos.php?fD=<?php echo $fechaDesde ?>&fH=<?php echo $fechaHasta ?>">VerDetalle</a></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Especialidades Solicitadas</h4></td>
        <td><?php echo $cantidadEspecialidades->num_rows ?></td>
        <td><a class="verDetalle" id="cantidadesEspecialidades.php?fD=<?php echo $fechaDesde ?>&fH=<?php echo $fechaHasta ?>">VerDetalle</a></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Notas Realizadas</h4></td>
        <td><?php echo $cantidadNotas->num_rows ?></td>
        <td><a class="verDetalle" id="cantidadesNotas.php?fD=<?php echo $fechaDesde ?>&fH=<?php echo $fechaHasta ?>">VerDetalle</a></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Habilitaciones de Consultorio</h4></td>
        <td><?php echo $cantidadHabilitaciones->num_rows ?></td>
        <td><a class="verDetalle" id="cantidadesConsultorios.php?fD=<?php echo $fechaDesde ?>&fH=<?php echo $fechaHasta ?>">VerDetalle</a></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Autoprescripciones</h4></td>
        <td><?php echo $cantidadAutoprescripciones->num_rows ?></td>
        <td><!--<a class="verDetalle" id="cantidadesAutoprescripciones.php?fD=<?php //echo $fechaDesde  ?>&fH=<?php //echo $fechaHasta  ?>">VerDetalle</a>--></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Nuevos Matriculados</h4></td>
        <td><?php echo $cantidadNuevosMatriculados->num_rows ?></td>
        <td><!--<a class="verDetalle" id="cantidadesAutoprescripciones.php?fD=<?php //echo $fechaDesde  ?>&fH=<?php //echo $fechaHasta  ?>">VerDetalle</a>--></td>
    </tr>
    <tr>
        <td class="izquierda"><h4>Cantidad de Solicitudes de Certificados</h4></td>
        <td><?php
            if ($cantidadSolicitudCertificados->num_rows > 0) {
                $total = 0;
                while ($row = $cantidadSolicitudCertificados->fetch_assoc()) {
                    $total += $row['cantidad'];
                }
                
                echo $total;
            } else {
                echo 0;
            }
            ?></td>
        <td><a class="verDetalle" id="cantidadesSolicitudesCertificados.php?fD=<?php echo $fechaDesde ?>&fH=<?php echo $fechaHasta ?>">VerDetalle</a></td>
    </tr>
</table>
<div id="modalVerDetalle" style="display: none"></div>