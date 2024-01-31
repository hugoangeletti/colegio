<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
?>
<script type="text/javascript">
    $(function(){
        $(".cancelarEnvioUniversidad").click(function(){
            $("#modalGenerarEnvioUniversidad").dialog("close");
        });
        
        
        $('#agregarEnvioUniversidad').submit(function(e){
            e.preventDefault();
            var form = $(this);
            var post_url = form.attr('action');
            var post_data = form.serialize();
            
            if(verif_fecha('fechaDesde') && (verif_fecha('fechaHasta')))
            {
                if(verif_desde_hasta('fechaDesde', 'fechaHasta'))
                {

                    $(".loading").show();
                    console.log($.ajax({
                        type: 'POST',
                        url: post_url,
                        data: post_data,
                        dataType: "json",
                        complete: function(){
                            $(".loading").hide();
                        },
                        success: function(msg) {
                            $(".loading").hide();
                            alert(msg.texto);
                            if(msg.estado)
                            {
                                location.reload();
                            }
                       }
                    }));
                }
            }
        });
    });
</script>

<?php

$ultimaFecha = obtenerUltimaFechaEnvioUniversidad();

    $ult = $ultimaFecha->fetch_assoc();
    
    //var_dump($ult);
    if (is_null($ult['FechaHasta'])) {
        $ultimaFecha = date('Y-m-d');
        $readOnly = false;
    } else {
        //$datoId = $ultimaFecha->fetch_assoc();
        $ultimaFecha = $ult['FechaHasta'];
        $readOnly = true;
    }
    
    //var_dump($readOnly);
?>

<form id="agregarEnvioUniversidad" action="../../envio-universidad/envio-universidad.php" method="post">
    <fieldset>
        <legend></legend>
        <table class="tablaRemitente">
            <tr>
                <td>Fecha Desde:</td>
                <td><input id="fechaDesde" name="fecha_desde" type="text" maxlength="200" required placeholder="Fecha Desde (dd-mm-aaaa)" style="width: 600px;" value="<?php echo invertirFecha($ultimaFecha) ?>" <?php if($readOnly){ ?>readonly="true"<?php } ?>></td>
            </tr>
            <tr>
                <td>Fecha Hasta:</td>
                <td><input id="fechaHasta" name="fecha_hasta" type="text" maxlength="200" required placeholder="Fecha Hasta (dd-mm-aaaa)" style="width: 600px;" value="<?php echo date('d-m-Y') ?>"></td>
            </tr>
            <tr>
                <td><input type="button" class="cancelarEnvioUniversidad" value="Cancelar" /></td>
                <td><input type="submit" value="Confirmar" /></td>
            </tr>
        </table>
    </fieldset>
</form>