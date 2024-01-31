<?php
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';
?>

<script type="text/javascript">
    $(function() {
        $('#formConfirmarHabilitacion').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var post_url = form.attr('action');
            var post_data = form.serialize();
            console.log($.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                dataType: "json",
                success: function(registro) {
                    alert(registro.texto);
                    if (registro.estado == 1)
                    {
                        $("#modalAgregarMovimiento").dialog("close");
                        window.location.reload();
                    }
                }
            }));

        });
    });

    $(function() {
        $(".tipoMovimiento").change(function(e) {
            e.preventDefault();
            var idTM = $(".tipoMovimiento option:selected").val();
            $(".tdDistrito").remove();
            if ((idTM == 1))
            {
                $(".ocultar").hide();
            }
            else
            {
                $(".ocultar").show();
            }
        });
    });
    $(function() {
        $(".cancelarEditarHabilitacionAsignada").click(function() {
            $("#modalAgregarMovimiento").dialog("close");
        });
    });
</script>
<form id="formConfirmarHabilitacion" action="agregarMovimientoDistritos.php" method="post">
    <table id='tablaBuscarInspector'>
        <tr>
            <td><b>Matrícula:</b></td>
            <td><input type="text" name="matricula" required></td>
        </tr>
        <tr>
            <td><b>Movimiento:</b></td>
            <td>
                <select name="tipoMovimiento" required class="tipoMovimiento">
                    <option value="">Seleccione un movimiento</option>
                    <?php
                    $movimientos = obtenerTiposMovimientosOtrosDistritos();

                    while ($row = $movimientos->fetch_assoc()) {
                        ?>
                        <option value="<?php echo $row['Id']; ?>"><?php echo utf8_encode($row['DetalleCompleto']); ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr class="ocultar">
            <td><b>Fecha Desde:</b></td>
            <td><input type="text" id="fechaDesde" placeholder="Ingrese Fecha Desde" maxlength="10" name="fechaDesde" style="width: 210px"/>  Debe Ingresar la Fecha con este formato(dd-mm-aaaa)</td>
        </tr>
        <tr>
            <td><b>Fecha Hasta:</b></td>
            <td><input type="text" id="fechaHasta" placeholder="Ingrese Fecha Hasta" maxlength="10" name="fechaHasta" style="width: 210px"/>  Debe Ingresar la Fecha con este formato(dd-mm-aaaa)</td>
        </tr>
        <tr class="ocultar">
            <td><b>Distrito Cambio:</b></td>
            <td><input type="text" maxlength="2" name="distritoCambio"></td>
        </tr>
        <tr>
            <td><b>Distrito Origen:</b></td>
            <td><input type="text" maxlength="2" name="distritoOrigen"></td>
        </tr>
        <tr class="ocultar">
            <td><b>Observaciones:</b></td>
            <td><textarea cols="80" rows="3" name="observaciones"></textarea></td>
        </tr>
    </table>
    <div class="volver">
        <input class="cancelarEditarHabilitacionAsignada" type="button" value="Cancelar" />
    </div>
    <input type="hidden" name="idMesaEntrada" value="<?php echo $_GET['idM'] ?>" />
    <input type="submit" value="Confirmar" />
</form>