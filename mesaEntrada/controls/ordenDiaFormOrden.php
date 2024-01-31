<?php
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/ordenDiaLogic.php';
    require_once '../dataAccess/funciones.php';
    
    if(isset($_GET['action']))
    {
        switch ($_GET['action'])
        {
            case "B":
                    $titulo = "Baja de ";
                    $actionForm = "borrarModificarOrdenDia.php";
                    $readOnly = true;
                break;
            case "M":
                    $titulo = "Modificación de ";
                    $actionForm = "borrarModificarOrdenDia.php";
                    $numeroReadOnly = true;
                break;
        }
        
        if(isset($_GET['iOrden']))
        {
            $idOrdenDia = $_GET['iOrden'];
            
            $consultaDatosOrden = obtenerOrdenPorId($idOrdenDia);
            
            if(!$consultaDatosOrden)
            {
                die("Hubo un error en la base de datos.");
            }
            else
            {
                if($consultaDatosOrden -> num_rows == 0)
                {
                    die("No existe la orden que usted desea examinar.");
                }
                else
                {
                    $datosOrden = $consultaDatosOrden -> fetch_assoc();
                }
            }
        }
    }
    else
    {
        $titulo = "Alta de ";
        $actionForm = "agregarOrdenDia.php";
    }


?>
<div id="titulo">
    <h3>Orden del Día</h3>
    <h4><?php echo $titulo ?>Orden del Día</h4>
    <?php
        if(isset($datosOrden))
        {
    ?>
    <h4>Nº de Orden: <?php echo $datosOrden['Numero'] ?></h4>
    <?php
        }
    ?>
</div>
<script type="text/javascript">
$(function(){
    $('#formOrdenDia').submit(function(e){
        e.preventDefault();
        var form = $(this);
        var post_url = form.attr('action');
        var post_data = form.serialize();
        if(verif_fecha('fechaOrden'))
        {
            if(verif_fecha('fechaDesde'))
            {
                if(verif_fecha('fechaHasta'))
                {
                    var fechaDesde = $("#fechaDesde").val().split(/-/);
                    fechaDesde.reverse();
                    
                    var fechaHasta = $("#fechaHasta").val().split(/-/);
                    fechaHasta.reverse();
                    
                    var fechaOrden = $("#fechaOrden").val().split(/-/);
                    fechaOrden.reverse();
                    
                    if(fechaDesde.join('-') < fechaHasta.join('-'))
                    {
                        if(fechaHasta.join('-') <= fechaOrden.join('-'))
                        {
                            $.ajax({
                                type: 'POST',
                                url: post_url,
                                data: post_data,
                                success: function(msg) {
                                    alert(msg);
                                    if(($.trim(msg) == "La orden se dio de alta correctamente.")||($.trim(msg) == "La modificación se realizó correctamente.")||($.trim(msg) == "La orden se dio de baja correctamente."))
                                    {
                                        
                                        location.reload();
                                    }
                               }
                            });
                        }
                        else
                        {
                            alert("¡Fecha Hasta debe ser menor a Fecha de Reunión!");
                            $("#fechaHasta").focus();
                        }
                    }
                    else
                    {
                        alert("¡Fecha Desde debe ser menor a Fecha Hasta!");
                        $("#fechaDesde").focus();
                    }
                }
                else
                {
                    $("#fechaHasta").focus();
                }
            }
            else
            {
                $("#fechaDesde").focus();
            }
        }
        else
        {
            $("#fechaOrden").focus();
        }
    });
});
</script>     
<br /><br />
<form id="formOrdenDia" action="<?php echo $actionForm ?>" method="post">
    <table>
        <tr>
            <td><b>Período:</b></td>
            <td><input type="text" name="periodo" readonly="readonly" value="<?php if(isset($datosOrden)){ echo $datosOrden['Periodo'];}else{ echo date("Y");} ?>"/></td>
        </tr>
        <tr>
            <td><b>Fecha de Reunión:</b></td>
            <td><input type="text" required id="fechaOrden" placeholder="Ingrese Fecha de Reunión" maxlength="10" name="fechaOrden" <?php if(isset($readOnly)&&($readOnly)){echo "readonly=readonly";}?> value="<?php if(isset($datosOrden)){ echo invertirFecha($datosOrden['Fecha']);} ?>" />  Debe Ingresar la Fecha con este formato(dd-mm-aaaa)</td>
        </tr>
        <tr>
            <td><b>Número:</b></td>
            <td><input type="text" required name="numero" placeholder="Ingrese Nº de Orden" <?php if((isset($readOnly)&&($readOnly))||(isset($numeroReadOnly)&&($numeroReadOnly))){echo "readonly=readonly";}?> value="<?php if(isset($datosOrden)){ echo $datosOrden['Numero'];} ?>"/></td>
        </tr>
        <tr>
            <td><b>Fecha Desde:</b></td>
            <td><input type="text" required id="fechaDesde" placeholder="Ingrese Fecha de Inicio" maxlength="10" name="fechaDesde" <?php if(isset($readOnly)&&($readOnly)){echo "readonly=readonly";}?> value="<?php if(isset($datosOrden)){ echo invertirFecha($datosOrden['FechaDesde']);} ?>"/>  Debe Ingresar la Fecha con este formato(dd-mm-aaaa)</td>
        </tr>
        <tr>
            <td><b>Fecha Hasta:</b></td>
            <td><input type="text" required id="fechaHasta" placeholder="Ingrese Fecha de Cierre" maxlength="10" name="fechaHasta" <?php if(isset($readOnly)&&($readOnly)){echo "readonly=readonly";}?> value="<?php if(isset($datosOrden)){ echo invertirFecha($datosOrden['FechaHasta']);} ?>"/>  Debe Ingresar la Fecha con este formato(dd-mm-aaaa)</td>
        </tr>
        <tr>
            <td><b>Observaciones</b></td>
            <td><textarea cols="60" rows="5" name="observaciones" <?php if(isset($readOnly)&&($readOnly)){echo "readonly=readonly";}?>><?php if(isset($datosOrden)){ echo $datosOrden['Observaciones'];} ?></textarea></td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td>
                <input type="button" value="Cancelar" onclick="location=window.location.search;" />
            </td>
            <td>
                <input type="submit" value="Confirmar" />
            </td>
        </tr>
        <?php
            if(isset($datosOrden))
            {
        ?>
        <input type="hidden" name="iOrden" value="<?php echo $datosOrden['Id'] ?>" />
        <input type="hidden" name="tipoAccion" value="<?php echo $_GET['action'] ?>" />
        <?php
            }
        ?>
    </table>
</form>