<?php
require_once 'seguridad.php';

require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

$idInspectorHabilitacion = $_GET['idIH'];

$consultaDatosInspectorHabilitacion = obtenerInspectorHabilitacionPorId($idInspectorHabilitacion);
if (!$consultaDatosInspectorHabilitacion) {
    $datosInspectorHabilitacion = null;
} else {
    if ($consultaDatosInspectorHabilitacion->num_rows == 0) {
        $datosInspectorHabilitacion = null;
    } else {
        $datosInspectorHabilitacion = $consultaDatosInspectorHabilitacion->fetch_assoc();
    }
}
?>

<script type="text/javascript">
    function clickSi()
    {
        $(".habilitadoNo").attr("checked", false);
        $(this).attr("checked", true);
        $(".fecHab").show();
        $("#fechaHabilitacion").attr("required", true);
    }
    function clickNo()
    {
        $(".habilitadoSi").attr("checked", false);
        $(this).attr("checked", true);
        $(".fecHab").hide();
        $("#fechaHabilitacion").attr("required", false);
    }
    
    $(document).ready(function(){
        <?php 
            if($datosInspectorHabilitacion['EstadoInspeccion'] == 'N'){
        ?>
        $(".fecHab").hide();
        <?php 
            } 
        ?>
    });
    
    $(function() {
        $(".cancelarEditarHabilitacionConfirmada").click(function() {
            $("#modalEditarHabilitacionConfirmada").dialog("close");
        });
        
        
        
        $(".habilitadoSi").click(function() {
            clickSi();
        });
        $(".habilitadoNo").click(function() {
            clickNo();
        });

        $('#formConfirmarHabilitacion').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var post_url = form.attr('action');
            var post_data = form.serialize();
            if (verif_fecha('fechaInspeccion'))
            {
                $.ajax({
                    type: 'POST',
                    url: post_url,
                    data: post_data,
                    success: function(msg) {
                        alert(msg);
                        if ($.trim(msg) == "La modificación se realizó correctamente.")
                        {

                            $("#modalEditarHabilitacionConfirmada").dialog("close");
                            $("#page-wrap").load("listadoHabilitaciones.php?lH=<?php echo $_GET['lH'] ?>&idIns=<?php echo $_GET['idIns'] ?>");
                        }
                    }
                });
            }
            else
            {
                $("#fechaInspeccion").focus();
            }
        });
    });
</script>
<form id="formConfirmarHabilitacion" action="borrarModificarInspeccion.php" method="post">
    <table id='tablaBuscarInspector'>
        <tr>
            <td><b>¿Habilitado?</b></td>
            <td><label for='habilitadoSi'>Sí<input id='habilitadoSi' class="habilitadoSi" type="radio" name="habilitado" value="S" required <?php if($datosInspectorHabilitacion['EstadoInspeccion'] == 'H'){echo "checked='checked'";} ?>/></label></td>
            <td><label for='habilitadoNo'>No<input id='habilitadoNo' class="habilitadoNo" type="radio" name="habilitado" value="N" required <?php if($datosInspectorHabilitacion['EstadoInspeccion'] == 'N'){echo "checked='checked'";} ?>/></label></td>
        </tr>
        <tr>
            <td><b>Fecha de Inspección:</b></td>
            <td colspan="4"><input type="text" required id="fechaInspeccion" placeholder="Ingrese Fecha de Inspección" maxlength="10" name="fechaInspeccion" style="width: 210px" value="<?php echo invertirFecha($datosInspectorHabilitacion['FechaInspeccion']); ?>"/>  Debe Ingresar la Fecha con este formato(dd-mm-aaaa)</td>
        </tr>
        <tr class="fecHab">
            <td><b>Fecha de Habilitación:</b></td>
            <td colspan="4"><input type="text" required id="fechaHabilitacion" placeholder="Ingrese Fecha de Habilitación" maxlength="10" name="fechaHabilitacion" style="width: 210px" value="<?php if(!is_null($datosInspectorHabilitacion['FechaHabilitacion'])){ echo invertirFecha($datosInspectorHabilitacion['FechaHabilitacion']);} ?>"/>  Debe Ingresar la Fecha con este formato(dd-mm-aaaa)</td>
        </tr>
    </table>
    <div class="volver">
        <input class="cancelarEditarHabilitacionConfirmada" type="button" value="Cancelar" />
    </div>
    <input type="hidden" name="idInspectorHabilitacion" value="<?php echo $_GET['idIH'] ?>" />
    <input type="hidden" name="tipoAccion" value="M"/>
    <div class="confirmarForm">
        <input type="submit" value="Confirmar" />
    </div>
</form>