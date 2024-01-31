<?php
require_once 'seguridad.php';

require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    /*
     * El formulario recibe un action el cual establecerá la acción dentro del
     * form, dependiendo Alta - Baja o Modificación.
     * 
     * En todos los casos se establece el título que se mostrará al usuario.
     * En el caso de Baja y Vista, los campos del formularios se verán 
     * deshabilitados, sin darle opción al usuario que los modifique.
     * 
     * Se establecen ciertas variables que determinan el pasaje de valores
     * necesarios para visualizar el requerimiento.
     */
    
if(isset($_GET['action']))
{
    $stHidden = false;
    $readOnly = false;
    $numeroTramite = false;
    switch ($_GET['action'])
    {
        case 'A': $actionForm = "agregarMatriculaJ.php";
                  $titulo = "Alta de";
            break;
        case 'B': $actionForm = "borrarModificarMatriculaJ.php";
                  $readOnly = true;
                  $titulo = "Baja de";
                  $stHidden = true;
                  $numeroTramite = true;
            break;
        case 'M': $actionForm = "borrarModificarMatriculaJ.php";
                  $titulo = "Modificación de";
                  $stHidden = true;
                  $numeroTramite = true;
            break;
        case 'V':
                  $actionForm = "";
                  $readOnly = true;
                  $titulo = "Información de";
                  $stHidden = true;
                  $numeroTramite = true;
            break;
    }
    
    /*
     * iEvento es el idMesaEntrada que se obtiene desde el Listado por Fecha de
     * Mesa de Entrada, con el cual se obtendrán todos los valores de la BD
     * correspondientes a las Matricula J.
     */
    
    /*
     * Todas las funciones que se encuentran en este php devuelven un objeto
     * query, el cual deberá controlarse a partir de las sentencias explícitas
     * de abajo, como por ejemplo $consultaDatosMatriculaJ.
     */
    
    if(isset($_GET['iEvento']))
    {
        $hayMatriculaJ = true;
        $consultaDatosMatriculaJ = obtenerMatriculaJPorId($_GET['iEvento']);
        if(!$consultaDatosMatriculaJ)
        {
            $datosMatriculaJ = null;
        }
        else
        {
            if($consultaDatosMatriculaJ -> num_rows == 0)
            {
                $datosMatriculaJ = null;
            }
            else
            {
                $datosMatriculaJ = $consultaDatosMatriculaJ -> fetch_assoc();
            }
        }
        $consultaColegiado = obtenerColegiadoPorId($datosMatriculaJ['IdColegiado']);
        $colegiado = $consultaColegiado -> fetch_assoc();
        $okey = true;
        $matricula = $colegiado['Matricula'];
    }
    else
    {
        $hayMatriculaJ = false;
    }

?>
<script>
    $(document).ready(function(){
        $(".tituloWrap").hide();
    });
</script>
<div id="titulo">
    <h3>Mesa de Entrada</h3>
    <h4><?php echo $titulo ?> Solicitud de Matrícula J</h4>
    <?php
        if($numeroTramite)
        {
    ?>
        <h4>Trámite Nº <?php echo $datosMatriculaJ['IdMesaEntrada'] ?></h4>
    <?php
        }
    ?>
</div>
<fieldset>
<?php
// Es el formulario que puede contar tanto con un colegiado como con un
// remitente, por lo que pregunta por cuál de los dos se deben cargar los datos
// y así se cargar algunas variables para determinar su correcta ejecucion

    include 'mostrarColegiado.php';

/*
 *  $col -> es quien determinará si es un colegiado. En su caso -> true.
 *  $nameHidden -> es quien determinará el nombre del NAME del HIDDEN a pasar. 
  */  
if(!$error)
{
    if(isset($estadoMatricular))
    {
        if($estadoMatricular['Estado'] == "J")
        {
            $verificacion = 1;
        }
        else
        {
            $verificacion = 0;
        }
    }
    else
    {
        $verificacion = -1;
    }

    
    $permiso = false;
    switch ($verificacion)
    {
        case -1:
            ?>
        <br>
        <span class="mensajeERROR">Hubo Error en la Verificación</span>
        <br>
            <?php
            break;
        case 0:
           ?>
    <br>
    <span class="mensajeERROR">No tiene permiso para realizar este evento.</span>
    <br>
            <?php
            break;
        default :
                  $permiso = true;
            break;
    }

    if($permiso)
    {
?>
<script type="text/javascript">
$(function(){
    $('#formNota').submit(function(e){
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
                    if($.trim(msg.texto) == "La matrícula J se dio de alta correctamente.")
                    {
                        //window.open('hojaRutaMatriculaJ.php','_blank');
                
                        location.reload();
                    }
                    else
                    {
                        alert(msg.texto);
                    }
                    if(($.trim(msg.texto) == "La modificación se realizó correctamente.")||($.trim(msg.texto) == "La matrícula J se dio de baja correctamente."))
                    {
                        location.reload();
                    }
           }
        });
    });
});
</script>     
<form id="formNota" action="<?php echo $actionForm ?>" method="post">
    <table>
        <tr>
            <td><b>Observaciones:</b></td>
            <td>
                <textarea cols="60" rows="6" name="observaciones" <?php if(isset($readOnly)&&($readOnly)){ echo "readonly=readonly";} ?>><?php if($hayMatriculaJ){ if(!is_null($datosMatriculaJ['Observaciones'])){ echo trim($datosMatriculaJ['Observaciones']);}}?></textarea>
            </td>
        </tr>
    <input type="hidden" name="idColegiado" value="<?php echo $aColegiado['Id'];?>" />
    <?php
        if(isset($stHidden)&&($stHidden))
        {
    ?>
    <input type="hidden" name="idMesaEntrada" value="<?php echo $datosMatriculaJ['IdMesaEntrada'] ?>" />
    <?php
        }
    ?>
    <input type="hidden" name="tipoAccion" value="<?php echo $_GET['action'] ?>" />
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <?php
            if(isset($hayMatriculaJ)&&($hayMatriculaJ))
            {
                if($_GET['action'] == "V")
                {
                    if(isset($_GET['fecha']))
                    {
                    ?>
        <td><input type="button" onclick="location='listaMesaEntrada.php?fecha=<?php echo $_GET['fecha'] ?>'" value="Volver" /></td>
                    <?php
                    }
                    else
                    {
                    ?>
        <td><input type="button" onclick="location='buscarPorMatricula.php?BoM=ok&matricula=<?php echo $_GET['mT'] ?>'" value="Volver"/></td>
                    <?php
                    }
                }
                else
                {
                ?>
        <td><input type="button" onclick="location='listaMesaEntrada.php?fecha=<?php echo $_GET['fecha'] ?>'" value="Cancelar" /></td>
                <?php
                }
            }
            else
            {
                ?>
        <td><input type="button" onclick="location=window.location.search;" value="Cancelar" /></td>
                <?php
            }
        
        if($_GET['action']!='V')
        {
        ?>
        <td><input type="submit" value="Confirmar" /></td>
        <?php
        }
        ?>
    </tr>
    </table>
</form>
<?php 
   }
    else
    {
?>
    <br/><input type="button" onclick="location=window.location.search;" value="Volver" />
<?php
    }
}
else
{
?>
    <br/><input type="button" onclick="location=window.location.search;" value="Volver" />
<?php
}
}
else
{
    echo "Hay un error";
?>
<input type="button" onclick="location=window.location.search;" value="Volver" />
<?php
}
?>
</fieldset>
<div id="hojaRuta" style="display: none">
    
</div>
