<?php
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
if(isset($_GET['action']))
{
    $actionForm = "";
    $readOnly = true;
    $titulo = "Información de";
    $stHidden = true;
    $numeroTramite = true;
    if(isset($_GET['iEvento']))
    {
        $hayEspecialidad = true;
        $consultaDatosEspecialidad = obtenerEspecialidadMesaEntradaPorId($_GET['iEvento']);
        if(!$consultaDatosEspecialidad)
        {
            $datosEspecialidad = null;
        }
        else
        {
            if($consultaDatosEspecialidad -> num_rows == 0)
            {
                $datosEspecialidad = null;
            }
            else
            {
                $datosEspecialidad = $consultaDatosEspecialidad -> fetch_assoc();
            }
        }
        $okey = true;
        $matricula = $datosEspecialidad['Matricula'];
    }
    else
    {
        $hayEspecialidad = false;
    }

?>
<div id="titulo">
    <h3>Mesa de Entrada</h3>
    <h4><?php echo $titulo ?> Solicitud de Especialidad</h4>
    <?php
        if($numeroTramite)
        {
    ?>
        <h4>Trámite Nº <?php echo $datosEspecialidad['IdMesaEntrada'] ?></h4><br/>
        <h4>Expediente Nº<?php echo $datosEspecialidad['NumeroExpediente'] ?></h4>
    <?php
        }
    ?>

</div>
<fieldset>
<?php
    
    include 'mostrarColegiado.php';
    
    /*
     * mostrarColegiado muestra la parte estática del colegiado
     * y devuelve una variable $error que determina si hubo algún error
     * al realizar la consulta, lo cual no debería mostrar nada
     * sólo el botón volver que se encuentra en el else.
     */
    if(!$error)
    {
?>
<script type="text/javascript">
$(function(){
    $('#formEspecialidad').submit(function(e){
        e.preventDefault();
        var form = $(this);
        var post_url = form.attr('action');
        var post_data = form.serialize();
        var dis = $(".inputDistrito").val();
        var ok = true;
        
        if(!(typeof(dis) === "undefined") )
        {
            if(isNaN(dis))
            {
                alert("No se admiten letras en ese campo.");
                $(".inputDistrito").focus();
                ok = false;
            }
        }
        if(ok)
        {
            $.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                dataType: "json",
                success: function(msg) {


                    if($.trim(msg.texto) == "La especialidad se dio de alta correctamente.")
                    {
                        window.open('expediente.php','_blank');

                        location.reload();
                    }
                    else
                    {
                        alert(msg.texto);
                    }
                    if(($.trim(msg.texto) == "La modificación se realizó correctamente.")||($.trim(msg.texto) == "La especialidad se dio de baja correctamente."))
                    {

                            location.reload();
                    }
               }
            });
        }
    });
    
    $("input[name='tipoEspecialidad']").change(function(e){
        e.preventDefault();
        var seleccionado = $("input[name='tipoEspecialidad']:checked").val();
        $(".tdDistrito").remove();
        if(seleccionado == "O")
            {
                $(".trDistrito").append("<td class='tdDistrito'><b>Distrito al cual egresa:</b></td><td class='tdDistrito'><input class='inputDistrito' type='text' name='distrito' required /></td>");
            }
    });
});
</script>    
<div id="formularioEspecialidad">
<form id="formEspecialidad" action="<?php echo $actionForm ?>" method="post">
    <table>
<?php

        if($hayEspecialidad)
        {
            $presenta = false;
            $consultaEstadoDeuda = obtenerEstadoDeudaColegiadoPorIdMesaEntrada($_GET['iEvento']);
            $estadoDeuda = $consultaEstadoDeuda -> fetch_assoc();
            
            if($estadoDeuda['IdTipoEstadoCuota'] != 2)
            {
                $consultaTodasEspecialidades = obtenerEspecialidades();

                if(!$consultaTodasEspecialidades)
                {
                    ?>
                <br>
                <span class="mensajeERROR">Hubo error en la Base de Datos</span>
                <br>
                    <?php
                }
                else
                {
                    if($consultaTodasEspecialidades -> num_rows == 0)
                    {
                        ?>
                    <br>
                    <span class="mensajeWARNING">No hay especialidades cargadas.</span>
                    <br>
                        <?php
                    }
                    else
                    {
                        $presenta = true;
                        ?>
        <tr>
            <td><b>Trámite realizado:</b></td>
            <td><?php echo mostrarNombreTramiteEspecialidad($datosEspecialidad['TipoEspecialidad']); ?></td>
        </tr>
        <?php
                if($datosEspecialidad['TipoEspecialidad'] == "O")
                {
                    ?>
        <tr>
            <td><b>Distrito:</b></td>
            <td><?php echo $datosEspecialidad['Distrito'] ?></td>
        </tr>
                    <?php
                }
            ?>
                 <tr>
                     <td><b>Especialidad Seleccionada:</b></td>
                     <td>
                        <select name="especialidad" <?php if(isset($readOnly)&&($readOnly)){ echo "disabled";} ?>>
                        <?php
                            while($especialidades = $consultaTodasEspecialidades -> fetch_assoc())
                            {
                                ?>

                    <option value="<?php echo $especialidades['Id'] ?>" <?php if($especialidades['Id'] == $datosEspecialidad['IdEspecialidad']){echo "selected";} ?>><?php echo utf8_encode($especialidades['Especialidad']) ?></option>

                                <?php
                            }
                            ?>
                        </select>
                     </td>
                </tr>
                <?php
                    }
                }
            }
            else
            {
                echo "<p class='codError'>Para poder anular este movimiento, primero debe anular el recibo de pago.</p>";
            }
        }
        if($presenta)
        {
        ?>
        <tr>
            <td><b>Observaciones:</b></td>
            <td colspan="2"><textarea cols="60" rows="6" name="observaciones" <?php if(isset($readOnly)&&($readOnly)){ echo "readonly=readonly";} ?>><?php if($hayEspecialidad){echo utf8_encode($datosEspecialidad['Observaciones']);} ?></textarea><br/></td>
        </tr>         
                <input type="hidden" name="idColegiado" value="<?php echo $aColegiado['Id'] ?>" />
                <?php
                if(isset($stHidden)&&($stHidden))
                {
                ?>
                <input type="hidden" name="idMesaEntrada" value="<?php echo $datosEspecialidad['IdMesaEntrada'] ?>" />
                <?php
                }
                ?>
                <input type="hidden" name="tipoAccion" value="<?php echo $_GET['action'] ?>" />

                <tr>
                    <?php
                        if(isset($hayEspecialidad)&&($hayEspecialidad))
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
                    <td><input type="button" onclick="location='buscarPorMatricula.php?BoM=ok&matricula=<?php echo $_GET['mT'] ?>'" value='Volver'/></td>
                            <?php
                            }
                        }
                        else
                        {
                            ?>
                    <td><input type="button" onclick="location=window.location.search;" value="Cancelar" /></td>
                            <?php
                        }
                    ?>
                </tr>
            </table>
        <?php
        }
        else
        {
        ?>
            <br>
            <input type="button" onclick="location=window.location.search;" value="Volver" />
        <?php
        }
        ?>
    </form>
    </div>
</fieldset>
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