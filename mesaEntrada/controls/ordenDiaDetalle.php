<?php
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/ordenDiaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_GET['iOrden'])) {
    $consultaInfoOrden = obtenerOrdenPorId($_GET['iOrden']);
    $infoOrden = $consultaInfoOrden->fetch_assoc();
    $movimientosOrdenDia = obtenerMovimientosPorIdOrdenDia($_GET['iOrden']);
}
?>

<div id="titulo">
    <h3>Orden del Día</h3>
    <h4>Detalle Orden del Día</h4>
    <?php
    if (isset($infoOrden) && (!is_null($infoOrden))) {
        ?>
        <h4>Nº de Orden: <?php echo $infoOrden['Numero'] ?></h4>
        <?php
    }
    ?>
</div>
<script type="text/javascript">
    $(function() {
        $(".generarOrden").click(function() {
            $("#page-wrap").load("ordenDiaGenerarDetalle.php?iOrden=<?php echo $_GET['iOrden'] ?>");
        });
        $(".detalle").click(function() {
            var iOrden = $(this).attr("id");
            var st = $(this).attr("data");
            $("#page-wrap").load("ordenDiaListadoDetalle.php?iOrden=" + iOrden + "&st=" + st);
        });
        $(".regenerar").click(function() {
            var data = $(this).attr("data");
            if (confirm("¿Está seguro que quiere regenerar la orden?"))
            {
                $.ajax({
                    url: "borrarDetalleOrdenDia.php?iOrden=" + data,
                    success: function(msg) {
                        if ($.trim(msg) == "El detalle fue eliminado correctamente.")
                        {
                            alert(msg);
                            $("#page-wrap").load("ordenDiaGenerarDetalle.php?iOrden=" + data);
                        }
                    }
                });
            }
        });
        $(".imprimir").click(function() {
            var id = $(this).attr("id");
            var data = $(this).attr("data");
            $.ajax({
                success: function() {
                    window.open('ordenDiaPlanilla.php?iOrden=' + id + '&planilla=' + data, '_blank');
                }
            });

        });

    });
</script>
<?php
if (!$movimientosOrdenDia) {
    ?>
    <br>
    <span class="mensajeERROR">Hubo un problema en el sistema. Repórtarlo.</span>
    <br>
    <?php
} else {
    if ($movimientosOrdenDia->num_rows == 0) {
        $archivados = obtenerMovimientosPorIdOrdenDiaPorPlanilla($_GET['iOrden'], 3);

        if ($archivados->num_rows > 0) {
            ?>
            <?php
            $ordenesPosteriores = obtenerOrdenesPosteriores($_GET['iOrden']);

            if ((!$ordenesPosteriores) || ($ordenesPosteriores->num_rows == 0)) {
                ?>

                <input type="button" value="Agregar Movimientos" class="generarOrden" /><br /><br>
                <?php
            }
            ?>

            <table class="ordenDiaDetalle">
                <tr>
                    <td><h4>Tipo de Planilla</h4></td>
                    <td><h4>Cantidad de Movimientos</h4></td>
                    <td><h4>Detalle</h4></td>
                </tr>
                <tr>
                    <td><h4>Archivados</h4></td>
                    <td><?php
                        if ($archivados) {
                            echo $archivados->num_rows;
                        }
                        ?> Movimientos/Notas para dicha planilla.</td>
                    <td><a class="detalle" id="<?php echo $_GET['iOrden']; ?>" data="3">Ver Detalle</a></td>
                </tr>
            </table>
            <br>
            <?php
            $ordenesPosteriores = obtenerOrdenesPosteriores($_GET['iOrden']);

            if ((!$ordenesPosteriores) || ($ordenesPosteriores->num_rows == 0)) {
                ?>

                <div class="botonesConfirmar">
                    <input class="regenerar" type="button" value="Regenerar" data="<?php echo $_GET['iOrden'] ?>" />
                </div>
                <?php
            }
        } else {
            ?>
            <input type="button" value="Generar Orden del Día" class="generarOrden" /><br />
            <?php
        }
    } else {
        $planilla1 = obtenerMovimientosPorIdOrdenDiaPorPlanilla($_GET['iOrden'], 1);
        $planilla2 = obtenerMovimientosPorIdOrdenDiaPorPlanilla($_GET['iOrden'], 2);
        ?>
        <br/><br/>
        <?php
        $ordenesPosteriores = obtenerOrdenesPosteriores($_GET['iOrden']);

        if ((!$ordenesPosteriores) || ($ordenesPosteriores->num_rows == 0)) {
            ?>

            <input type="button" value="Agregar Movimientos" class="generarOrden" /><br /><br>
            <?php
        }
        ?>
        <table class="ordenDiaDetalle">
            <tr>
                <td><h4>Tipo de Planilla</h4></td>
                <td><h4>Cantidad de Movimientos</h4></td>
                <td><h4>Detalle</h4></td>
                <td><h4>Imprimir</h4></td>
            </tr>
            <tr>
                <td><h4>Planilla Asuntos Internos</h4></td>
                <td><?php
                    if ($planilla1) {
                        echo $planilla1->num_rows;
                    }
                    ?> Movimientos/Notas para dicha planilla.</td>
                <td><a class="detalle" id="<?php echo $_GET['iOrden']; ?>" data="1">Ver Detalle</a></td>
                <td><a class="imprimir" id="<?php echo $_GET['iOrden']; ?>" data="1">Imprimir</a></td>
            </tr>
            <tr>
                <td><h4>Planilla Notas Recibidas</h4></td>
                <td><?php
                    if ($planilla2) {
                        echo $planilla2->num_rows;
                    }
                    ?> Movimientos/Notas para dicha planilla.</td>
                <td><a class="detalle" id="<?php echo $_GET['iOrden']; ?>" data="2">Ver Detalle</a></td>
                <td><a class="imprimir" id="<?php echo $_GET['iOrden'] ?>" data="2">Imprimir</a></td>
            </tr>
        </table>
        <br>   
        <?php
        $ordenesPosteriores = obtenerOrdenesPosteriores($_GET['iOrden']);

        if ((!$ordenesPosteriores) || ($ordenesPosteriores->num_rows == 0)) {
            ?>

            <div class="botonesConfirmar">
                <input class="regenerar" type="button" value="Regenerar" data="<?php echo $_GET['iOrden'] ?>" />
            </div>
            <?php
        }
    }
}
?>
<br /><br />
<input type="button" value="Volver" onclick="location = window.location.search;" />