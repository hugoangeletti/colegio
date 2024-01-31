<?php
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/ordenDiaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_GET['iOrden'])) {
    $consultaInfoOrden = obtenerOrdenPorId($_GET['iOrden']);
    $infoOrden = $consultaInfoOrden->fetch_assoc();
    $movimientosOrdenDia = obtenerMovimientosParaOrdenDia($infoOrden['FechaDesde'], $infoOrden['FechaHasta']);
}
?>

<div id="titulo">
    <h3>Orden del Día</h3>
    <h4>Alta Detalle Orden del Día</h4>
    <?php
    if (isset($infoOrden) && (!is_null($infoOrden))) {
        ?>
        <h4>Nº de Orden: <?php echo $infoOrden['Numero'] ?></h4>
        <?php
    }
    ?>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".me2").attr("checked", "checked");
        if ($(".me2").attr("checked") == "checked")
        {
            $(".me1").attr("disabled", true);
            $(".meD").attr("disabled", true);
        }
        else
        {
            $(".me1").attr("disabled", false);
            $(".meD").attr("disabled", false);
        }
    });
    $(function() {
        $(".botonVolver").click(function(){
            $("#page-wrap").load("ordenDiaDetalle.php?iOrden=<?php echo $_GET['iOrden'] ?>");
        });
        
        $(".me1").change(function() {
            var data = $(this).attr("data");
            if ($(this).attr("checked") == "checked")
            {
                $(this).removeAttr("checked");
                $("#me2" + data).attr("disabled", false);
                $("#meD" + data).attr("disabled", false);
            }
            else
            {
                $(this).attr("checked", "checked");
                $("#me2" + data).attr("disabled", true);
                $("#meD" + data).attr("disabled", true);
            }
        });

        $(".me2").change(function() {
            var data = $(this).attr("data");
            if ($(this).attr("checked") == "checked")
            {
                $(this).removeAttr("checked");
                $("#me1" + data).attr("disabled", false);
                $("#meD" + data).attr("disabled", false);
            }
            else
            {
                $(this).attr("checked", "checked");
                $("#me1" + data).attr("disabled", true);
                $("#meD" + data).attr("disabled", true);
            }
        });

        $(".meD").change(function() {
            var data = $(this).attr("data");
            if ($(this).attr("checked") == "checked")
            {
                $(this).removeAttr("checked");
                $("#me1" + data).attr("disabled", false);
                $("#me2" + data).attr("disabled", false);
            }
            else
            {
                $(this).attr("checked", "checked");
                $("#me1" + data).attr("disabled", true);
                $("#me2" + data).attr("disabled", true);
            }
        });

        $('#formDetalleOrden').submit(function(e) {
            e.preventDefault();
            //Agregado para no tener problemas en el dobleclick
            $(':submit', this).click(function() {
                return false;
            });
            $(':submit', this).hide();
            var form = $(this);
            var post_url = form.attr('action');
            var post_data = form.serialize();
            $.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                success: function(msg) {
                    console.log(msg);
                    alert(msg);
                    if (($.trim(msg) == "El detalle se dio de alta correctamente."))
                    {

                        //location.reload();
                        //window.location.replace("ordenDiaDetalle.php?iOrden=<?php echo $_GET['iOrden'] ?>");
                        $("#page-wrap").load("ordenDiaDetalle.php?iOrden=<?php echo $_GET['iOrden'] ?>");
                    }
                }
            });
        });

    });
</script>
<?php
if (!$movimientosOrdenDia) {
    ?>
    <br>
    <span class="mensajeERROR">Hubo un problema en el sistema. Reportarlo.</span>
    <br>
    <?php
} else {
    if ($movimientosOrdenDia->num_rows == 0) {
        ?>
        <br>
        <p class="mensajeWARNING">No existen movimientos para realizar la Orden del Día.</p>
        <br>
        <br><br>
        <input class="botonVolver" type="button" value="Volver" onclick="//location = window.location.search;">
        <?php
    } else {
        ?>
        <form id="formDetalleOrden" action="agregarOrdenDiaDetalle.php" method="post">
            <p>1 - Corresponde a la Planilla de Asuntos Internos.</p>
            <p>2 - Corresponde a la Planilla de Notas Recibidas.</p>
            <p>Archivado - Descarta el Trámite Definitivamente.</p>
            <p>Si no selecciona ninguna opción, automáticamente quedará pospuesto para la próxima reunión.</p><br/>
            <table class='tablaTabs'>
                <tr>
                    <td>1</td>
                    <td>2</td>
                    <td>Archivado</td>
                    <td>Fecha de Trámite</td>
                    <td>Tipo de Trámite</td>
                    <td>Colegiado/Remitente</td>
                    <td>Tema/Observaciones</td>
                </tr>
                <?php
                while ($mod = $movimientosOrdenDia->fetch_assoc()) {
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="mesaEntrada1[]" class="me1" id="me1<?php echo $mod['IdMesaEntrada'] ?>" data="<?php echo $mod['IdMesaEntrada'] ?>" value="<?php echo $mod['IdMesaEntrada'] ?>"/>
                        </td>
                        <td>
                            <input type="checkbox" name="mesaEntrada2[]" class="me2" id="me2<?php echo $mod['IdMesaEntrada'] ?>" data="<?php echo $mod['IdMesaEntrada'] ?>" value="<?php echo $mod['IdMesaEntrada'] ?>"/>
                        </td>
                        <td>
                            <input type="checkbox" name="mesaEntradaD[]" class="meD" id="meD<?php echo $mod['IdMesaEntrada'] ?>" data="<?php echo $mod['IdMesaEntrada'] ?>" value="<?php echo $mod['IdMesaEntrada'] ?>"/>
                        </td>
                        <td>
                            <?php echo invertirFecha($mod['FechaIngreso']) ?>
                        </td>
                        <td>
                            <?php echo utf8_encode($mod['NombreMovimiento']); ?>
                        </td>
                        <td>
                            <?php
                            if (!is_null($mod['Matricula'])) {
                                echo utf8_encode($mod['Apellido']) . " " . utf8_encode($mod['Nombres']);
                            } else {
                                echo utf8_encode($mod['NombreRemitente']);
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (!is_null($mod['Tema'])) {
                                echo utf8_encode($mod['Tema']);
                            } else {
                                if (!is_null($mod['DetalleCompleto'])) {
                                    echo utf8_encode($mod['DetalleCompleto']);
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                <input type="hidden" name="iOrden" value="<?php echo $_GET['iOrden'] ?>">
                </tr>
            </table>
            <br>
            <div class="botonesConfirmar">
                <input class="botonVolver" type="button" onclick="//location = window.location.search;" value="Cancelar">
                <input type="submit" class="btnSubmit" value="Confirmar">
            </div>
        </form>
        <?php
    }
}
?>