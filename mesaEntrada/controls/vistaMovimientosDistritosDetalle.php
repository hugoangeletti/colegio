<?php
include_once 'head_config.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_GET['idM']) && ($_GET['idM'] != "")) {
    $idMesaEntrada = $_GET['idM'];
} else {
    $idMesaEntrada = -1;
}

if (isset($_GET['tipo']) && ($_GET['tipo'] == "B")) {
    $tipo = "B";
    $movimientosAMostrar = obtenerColegiadoMovimientoBajaInscripcion($idMesaEntrada);
} else {
    $tipo = "O";
    $movimientosAMostrar = obtenerMovimientosPorNota($idMesaEntrada);
}
?>
<script>
    $(function() {
        $(".borrar").click(function() {
            if (confirm("¿Está seguro que desea dar de baja el movimiento correspondiente a la nota Nº <?php echo $idMesaEntrada; ?>?"))
            {
                var dataPost = $(this).attr("id");
                console.log($.post("borrarModificarMovimientoDistritos.php", {tipo: "<?php echo $tipo; ?>",idMesaEntrada: <?php echo $idMesaEntrada; ?>, idColegiadoMovimientoDistritos: dataPost, func: "getEstadoAndTextoAndImporte"}, function(data) {
                    alert(data.texto);
                    //location.reload();
                }, "json"));
            }
        });
    });
</script>
<table id="tablaEstadisticas">
    <tr>
        <td><h4>Matrícula</h4></td>
        <td><h4>Apellido y Nombre</h4></td>
        <td><h4>Movimiento</h4></td>
        <td><h4>Fecha Desde</h4></td>
        <td><h4>Borrar</h4></td>
    </tr>
    <?php
    if (!$movimientosAMostrar) {
        ?>
        <tr>
            <td colspan="5"><span class="mensajeERROR">Hubo un error en la base de datos.</span></td>
        </tr>
        <?php
    } else {
        if ($movimientosAMostrar->num_rows == 0) {
            ?>
            <tr>
                <td colspan="5"><span class="mensajeWARNING">No se encuentran movimientos asociados a esta nota.</span></td>
            </tr>
            <?php
        } else {
            while ($row = $movimientosAMostrar->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row['Matricula']; ?></td>
                    <td><?php echo utf8_encode($row['Apellido']) . " " . utf8_encode($row['Nombres']); ?></td>
                    <td><?php
                        if ($tipo == "B") {
                            echo "Baja de Inscripción de Otro Distrito";
                        } else {
                            echo utf8_encode($row['NombreMovimiento']);
                        }
                        ?></td>
                    <td><?php echo invertirFecha($row['FechaDesde']) ?></td>
                    <td><a class="borrar" id='<?php echo $row['Id'] ?>'>Borrar</a></td>
                </tr>
                <?php
            }
        }
    }
    ?>
</table>