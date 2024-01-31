<?php
    require_once 'seguridad.php';

    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    $colegios = array(1, 2, 3, 4, 5, 6, 7, 10, 11);
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
        case 'A': $actionForm = "agregarNota.php";
                  $titulo = "Alta de";
            break;
        case 'B': $actionForm = "borrarModificarNota.php";
                  $readOnly = true;
                  $titulo = "Baja de";
                  $stHidden = true;
                  $numeroTramite = true;
            break;
        case 'M': $actionForm = "borrarModificarNota.php";
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
     * correspondientes a las Notas.
     */
    
    /*
     * Todas las funciones que se encuentran en este php devuelven un objeto
     * query, el cual deberá controlarse a partir de las sentencias explícitas
     * de abajo, como por ejemplo $consultaNota.
     */
    
    if(isset($_GET['iEvento']))
    {
        $hayNota = true;
        $consultaNota = obtenerNotaPorId($_GET['iEvento']);
        if(!$consultaNota)
        {
            $datosNota = null;
        }
        else
        {
            if($consultaNota -> num_rows == 0)
            {
                $datosNota = null;
            }
            else
            {
                $datosNota = $consultaNota -> fetch_assoc();
            }
        }
        $okey = true;
        $matricula = $datosNota['Matricula'];
    }
    else
    {
        $hayNota = false;
    }

?>
<script>
    $(document).ready(function(){
        $(".tituloWrap").hide();
    });
</script>
<div id="titulo">
    <h3>Mesa de Entrada</h3>
    <h4><?php echo $titulo ?> Solicitud de Notas/Oficio</h4>
    <?php
        if($numeroTramite)
        {
    ?>
        <h4>Trámite Nº <?php echo $datosNota['IdMesaEntrada'] ?></h4>
    <?php
        }
    ?>
</div>
<fieldset>
<?php
// Es el formulario que puede contar tanto con un colegiado como con un
// remitente, por lo que pregunta por cuál de los dos se deben cargar los datos
// y así se cargar algunas variables para determinar su correcta ejecucion
$porAlta = false;
if(isset($_POST['matricula']) || isset($_GET['matricula']))
{
    $col = true;
    $nameHidden = "idColegiado";
    include 'mostrarColegiado.php';
    $porAlta = true;
}
else if(isset ($_GET['remitente']))
{
    $rem = true;
    $nameHidden = "idRemitente";
    include 'mostrarRemitente.php';
    $porAlta = true;
}
if(!$porAlta)
{
if(!is_null($matricula))
{
    $col = true;
    $nameHidden = "idColegiado";
    include 'mostrarColegiado.php';
}
else
{
    $remitente = $datosNota['IdRemitente'];
    $rem = true;
    $nameHidden = "idRemitente";
    include 'mostrarRemitente.php';
}
}
/*
 *  $col -> es quien determinará si es un colegiado. En su caso -> true.
 *  $nameHidden -> es quien determinará el nombre del NAME del HIDDEN a pasar. 
  */  

/*
 * La variable $error viene de los php (includes) mostrarColegiado o mostrarRemitente.
 * Que determina si el colegiado o el remitente se pudo mostrar correctamente.
 * 
 * La variable $estadoMatricular se obtiene del mismo lugar, la cual determina
 * si puede realizar la acción o no.
 */

if(!$error)
{
    if(isset($estadoMatricular))
    {
        $verificacion = verificarPermisoColegiado($estadoMatricular['Estado'], 3);        
    }
    else
    {
        $verificacion = 1;
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

    /*
     * La variable $permiso determina si el colegiado tiene permiso para realizar
     * el pedido de Nota.
     */
    
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
                    if($.trim(msg.texto) == "La nota se dio de alta correctamente.")
                    {
                        window.open('hojaRuta.php','_blank');
                
                        location.reload();
                    }
                    else
                    {
                        alert(msg.texto);
                    }
                    if(($.trim(msg.texto) == "La modificación se realizó correctamente.")||($.trim(msg.texto) == "La nota se dio de baja correctamente."))
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
            <td><b>Tema:</b></td>
            <td>
                <textarea cols="40" rows="3" name="tema" <?php if(isset($readOnly)&&($readOnly)){ echo "readonly=readonly";} ?>><?php if($hayNota){ if(!is_null($datosNota['Tema'])){ echo utf8_encode($datosNota['Tema']);}}?></textarea>
            </td>
            <?php
                if(isset($aRemitente['Id']) && ($aRemitente['Id'] != "") && (in_array($aRemitente['Id'],$colegios)))
                {
            ?>
            <td>
                <b><label for='incluyeLista'>Incluye lista de movimientos: </label></b><input type="checkbox" id="incluyeLista" name="incluyeLista" <?php if(isset($datosNota['IncluyeMovimiento']) && ($datosNota['IncluyeMovimiento'] == "S")){ echo "checked"; } ?> <?php if(isset($readOnly)&&($readOnly)){ echo "disabled";} ?>>
            </td>
            <?php
                }
            ?>
        </tr>
        <tr>
            <td><b>Observaciones:</b></td>
            <td>
                <textarea cols="60" rows="6" name="observaciones" <?php if(isset($readOnly)&&($readOnly)){ echo "readonly=readonly";} ?>><?php if($hayNota){ if(!is_null($datosNota['Observaciones'])){ echo trim(utf8_encode($datosNota['Observaciones']));}}?></textarea>
            </td>
        </tr>
    <input type="hidden" name="<?php echo $nameHidden ?>" value="<?php if(isset($col)&&($col)){ echo $aColegiado['Id'];}else if(isset ($rem)&&($rem)){ echo $aRemitente['Id'];} ?>" />
    <?php
        if(isset($stHidden)&&($stHidden))
        {
    ?>
    <input type="hidden" name="idMesaEntrada" value="<?php echo $datosNota['IdMesaEntrada'] ?>" />
    <?php
        }
    ?>
    <input type="hidden" name="tipoAccion" value="<?php echo $_GET['action'] ?>" />
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <?php
            if(isset($hayNota)&&($hayNota))
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
                        if(isset($_GET['rT']))
                        {
                        ?>
        <td><input type="button" onclick="location='buscarPorRemitente.php?BoM=ok&remitente=<?php echo $_GET['rT'] ?>'" value="Volver"/></td>
                        <?php
                        }
                        else
                        {
                        ?>
            <td><input type="button" onclick="location='buscarPorMatricula.php?BoM=ok&matricula=<?php echo $_GET['mT'] ?>'" value="Volver"/></td>
                        <?php
                        }
                    }
                }
                else
                {
                    if(isset($_GET['fecha']))
                    {
                ?>
        <td><input type="button" onclick="location='listaMesaEntrada.php?fecha=<?php echo $_GET['fecha'] ?>'" value="Cancelar" /></td>
                <?php
                    }
                    else
                    {
                    ?>
        <td><input type="button" onclick="$('#page-wrap').load('ordenDiaListadoDetalle.php?iOrden=<?php echo $_GET['orden'] ?>&st=<?php echo $_GET['st'] ?>');" value="Volver"/></td>
                    <?php
                    }
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
