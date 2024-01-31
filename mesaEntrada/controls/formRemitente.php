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
        $(".cancelarRemitente").click(function(){
            $("#modalGenerarRemitente").dialog("close");
        });
        
        $('#agregarRemitente').submit(function(e){
            e.preventDefault();
            var form = $(this);
            var post_url = form.attr('action');
            var post_data = form.serialize();
            $.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                dataType: "json",
                success: function(msg) {
                    alert(msg.texto);
                    if($.trim(msg.texto) == "El remitente se dio de alta correctamente.")
                    {
                        location.reload();
                    }
               }
            });
        });
    });
</script>

<form id="agregarRemitente" action="agregarRemitente.php" method="post">
    <fieldset>
        <legend>Alta de Remitente</legend>
        <table class="tablaRemitente">
            <tr>
                <td>Nombre:</td>
                <td><input name="nombre" type="text" maxlength="200" required placeholder="Ingrese el Nombre del Remitente" style="width: 600px;"/></td>
            </tr>
            <tr>
                <td><input type="button" class="cancelarRemitente" value="Cancelar" /></td>
                <td><input type="submit" value="Confirmar" /></td>
            </tr>
        </table>
    </fieldset>
</form>