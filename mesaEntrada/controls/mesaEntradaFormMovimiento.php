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
    $stHidden = false;
    $readOnly = false;
    $numeroTramite = false;
    switch ($_GET['action'])
    {
        case 'A': $actionForm = "agregarMovimiento.php";
                  $titulo = "Alta de";
            break;
        case 'B': $actionForm = "borrarModificarMovimiento.php";
                  $readOnly = true;
                  $titulo = "Baja de";
                  $stHidden = true;
                  $numeroTramite = true;
            break;
        case 'M': $actionForm = "borrarModificarMovimiento.php";
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
    
    if(isset($_GET['iEvento']))
    {
        $hayMovimiento = true;
        $consultaDatosMovimiento = obtenerMovimientoPorId($_GET['iEvento']);
        if(!$consultaDatosMovimiento)
        {
            $datosMovimiento = null;
        }
        else
        {
            if($consultaDatosMovimiento -> num_rows == 0)
            {
                $datosMovimiento = null;
            }
            else
            {
                $datosMovimiento = $consultaDatosMovimiento -> fetch_assoc();
            }
        }
        $okey = true;
        $matricula = $datosMovimiento['Matricula'];
    }
    else
    {
        $hayMovimiento = false;
    }

?>
<script>
    $(document).ready(function(){
        $(".tituloWrap").hide();
    });
</script>
<div id="titulo">
    <h3>Mesa de Entrada</h3>
    <h4><?php echo $titulo ?> Solicitud de Movimiento Matricular</h4>
    <?php
        if($numeroTramite)
        {
    ?>
        <h4>Trámite Nº <?php echo $datosMovimiento['IdMesaEntrada'] ?></h4>
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
    
    $verificacion = verificarPermisoColegiado($estadoMatricular['Estado'], 1);
    
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
    $('#formMovimiento').submit(function(e){
        e.preventDefault();
        var form = $(this);
        var post_url = form.attr('action');
        var post_data = form.serialize();
        var dis = $(".inputDistrito").val();
        var ok = true;
        if(verif_fecha('fechaDesde'))
            {
                if(!(typeof(dis) === "undefined") )
                {
                    if(isNaN(dis))
                    {
                        alert("No se admiten letras en ese campo.");
                        $(".inputDistrito").focus();
                        ok = false;
                    }
                }
            }
            else
            {
                ok = false;
                $("#fechaDesde").focus();
            }
            if(ok)
            {
                $.ajax({
                    type: 'POST',
                    url: post_url,
                    data: post_data,
                    dataType: "json",
                    success: function(msg) {
                        if($.trim(msg.texto) == "El movimiento se dio de alta correctamente.")
                        {
                            
                            window.open('hojaRutaMovimiento.php','_blank');

                            location.reload();
                        }
                        else
                            {
                                
                                alert(msg.texto);
                            }
                        if(($.trim(msg.texto) == "La modificación se realizó correctamente.")||($.trim(msg.texto) == "El movimiento se dio de baja correctamente."))
                            {
                                location.reload();
                            }
                   }
                });
            }
            
    });
    
    $("#tipoMovimiento").change(function(e){
        e.preventDefault();
        var idTM = $("#tipoMovimiento option:selected").val();
        $(".tdDistrito").remove();
        document.getElementById("observaciones").required = false;
        if( (idTM == 6) || (idTM == 4))
        {
            $(".trDistrito").append("<td class='tdDistrito'><b>Distrito al cual egresa:</b></td><td class='tdDistrito'><input class='inputDistrito' type='text' name='distrito' required /></td>");
        }
        else
        {
            if( (idTM == 5) || (idTM == 8) || (idTM == 10))
            {
                $(".trDistrito").append("<td class='tdDistrito'><b>Distrito del cual ingresa:</b></td><td class='tdDistrito'><input class='inputDistrito' type='text' name='distrito' required /></td>");
            }
            else
            {
                if( (idTM == 2))
                {
                    document.getElementById("observaciones").required = true;
                }
                else
                {
                }
            }
        }
    });
});
</script>     
<?php
    if ($estadoTesoreria == 0 || $_SESSION['idUsuario'] == 1 || $_SESSION['idUsuario'] == 8 || $_SESSION['idUsuario'] == 24) {
        $generaMovimiento = TRUE;
    } else {
        $generaMovimiento = FALSE;
    }
    $movimientos = obtenerTipoMovimientoMesaEntrada($estadoColegiado);
    $todoMovimientos = obtenerTiposMovimientos();
    if($movimientos)
    {
        if($movimientos -> num_rows == 0)
        {
            ?>
        <br>
        <p class="mensajeERROR">Este colegiado no puede realizar movimientos.</p>
        <br>
        <input type="button" onclick="location=window.location.search;" value="Volver" />
            <?php
        }
        else
        {

            $yaHizoMovimiento = obtenerMovimientosPorIdColegiadoHoy($aColegiado['Id']);
            if($yaHizoMovimiento)
            {
                $pregunta = false;
                if(!$hayMovimiento)
                {
                    if($yaHizoMovimiento -> num_rows != 0)
                    {
                        $pregunta = true;
                    }
                }
                
                if(!$pregunta)
                {                    
                
?>
<form id="formMovimiento" action="<?php echo $actionForm ?>" method="post">
    <table>
        <?php 
        //if ($generaMovimiento) {
        ?>
            <tr>
                <td><b>Tipo de Movimiento:</b></td>
                <td>
                    <select id="tipoMovimiento" name="tipoMovimiento" required <?php if((isset($readOnly)&&($readOnly))||(isset($_GET['orden']))){ echo "disabled";} ?>>
                        <?php 
                        if (!$generaMovimiento) {
                        ?>
                            <option value="7">Fallecido</option>
                        <?php 
                        } else {
                        ?>
                            <option value="">Seleccione un Tipo de Movimiento</option>
                            <?php
                            if(isset($hayMovimiento)&&($hayMovimiento)) {
                                while($row = $todoMovimientos -> fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row['Id']; ?>" <?php if(isset($hayMovimiento)&&($hayMovimiento)) { if($row['Id'] == $datosMovimiento['IdTipoMovimiento']) { ?> selected <?php }} ?>> 
                                        <?php echo utf8_encode($row['DetalleCompleto']); ?></option>";
                                    <?php
                                }
                            } else {
                                while($row = $movimientos -> fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $row['Id']; ?>" <?php if(isset($hayMovimiento)&&($hayMovimiento)) { if($row['Id'] == $datosMovimiento['IdTipoMovimiento']) { ?> selected <?php }} ?>> <?php echo utf8_encode($row['DetalleCompleto']); ?></option>";
                                    <?php
                                }
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>Fecha Desde:</b></td>
                <td>
                    <input id="fechaDesde" name="fechaDesde" type="text" required onblur="return verif_fecha(this.id);" value="<?php if(isset($hayMovimiento)&&($hayMovimiento)){$fechaDesdeInvertir = explode("-", $datosMovimiento['Fecha']); $fechaDesde = $fechaDesdeInvertir[2]."-".$fechaDesdeInvertir[1]."-".$fechaDesdeInvertir[0]; echo $fechaDesde;}else{echo date("d-m-Y");} ?>" <?php if((isset($readOnly)&&($readOnly))||(isset($_GET['orden']))){ echo "readonly=readonly";} ?>/> Debe Ingresar la Fecha con este formato(dd-mm-aaaa)
                </td>
            </tr>
            <tr>
                <td><b>Motivo de Movimiento:</b></td>
                <td>
                    <select id="motivo" name="motivo" required <?php if((isset($readOnly)&&($readOnly))||(isset($_GET['orden']))){ echo "disabled";} ?>>
                        <option value="">Seleccione un Motivo de Cancelación</option>
                        <?php
                            $motivosCancelacion = obtenerMotivosCancelacion();
                            if($motivosCancelacion)
                            {
                                if($motivosCancelacion -> num_rows != 0)
                                {
                                    while($row = $motivosCancelacion -> fetch_assoc()){
                                            echo "<option value=".$row['IdMotivoCancelacion']." ";
                                                if(isset($hayMovimiento)&&($hayMovimiento))
                                                {
                                                    if($row['IdMotivoCancelacion'] == $datosMovimiento['IdMotivoCancelacion'])
                                                    {
                                                        echo "selected";
                                                    }
                                                }
                                            echo ">".utf8_encode($row['Nombre'])."</option>";
                                        }
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
<!--        <tr>
            <td><b>Patolog&iacute;a:</b></td>
            <td>
                <select id="idPatologia" name="idPatologia" 
                    <?php  //if((isset($readOnly)&&($readOnly))||(isset($_GET['orden']))){ echo "disabled";} ?>>
                    <option value="">Seleccione Patologia que motivó de Cancelación</option>
                    <?php
//                        $patologias = obtenerPatologias();
//                        if($patologias)
//                        {
//                            if($patologias -> num_rows != 0)
//                            {
//                                while($row = $patologias -> fetch_assoc()){
//                                        echo "<option value=".$row['Id']." ";
//                                            if(isset($hayMovimiento)&&($hayMovimiento))
//                                            {
//                                                if($row['Id'] == $datosMovimiento['IdPatologia'])
//                                                {
//                                                    echo "selected";
//                                                }
//                                            }
//                                        echo ">".utf8_encode($row['Nombre'])."</option>";
//                                    }
//                            }
//                        }
                    ?>
                </select>
            </td>
        </tr>-->
        <tr class="trDistrito">           
        </tr>
        <tr>
            <td><b>Observaciones:</b></td>
            <td>
                <textarea id="observaciones" cols="60" rows="6" name="observaciones" <?php if(isset($readOnly)&&($readOnly)){ echo "readonly=readonly";} ?>><?php if(isset($hayMovimiento)&&($hayMovimiento)){ if(!is_null($datosMovimiento['Observaciones'])){echo $datosMovimiento['Observaciones'];}}?></textarea>
            </td>
        </tr>
    <input type="hidden" name="idPatologia" value="" />
    <input type="hidden" name="idColegiado" value="<?php echo $aColegiado['Id'] ?>" />
    <?php
        if(isset($stHidden)&&($stHidden))
        {
            if(isset($_GET['orden']))
            {
            ?>
    <input type="hidden" name="tipoMovimiento" value="<?php echo $datosMovimiento['IdTipoMovimiento'] ?>" />
    <input type="hidden" name="motivo" value="<?php echo $datosMovimiento['IdMotivoCancelacion'] ?>" />
            <?php
            }
    ?>
    <input type="hidden" name="idMesaEntrada" value="<?php echo $datosMovimiento['IdMesaEntrada'] ?>" />
    <?php
        //}
        //cierra estadoTesoreria == 0
    }
    ?>
    <input type="hidden" name="tipoAccion" value="<?php echo $_GET['action'] ?>" />
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <?php
            if(isset($hayMovimiento)&&($hayMovimiento))
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
          
        if($_GET['action']!='V' && $generaMovimiento)
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
        <br>
        <span class="mensajeWARNING">El colegiado ya realizó un movimiento en esta fecha.</span>
                    <?php
                }
            }
            else
            {
                ?>
        <br>
        <span class="mensajeERROR">Problema en la base de datos.</span>
                <?php
            }
        }
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
?>
    <br/><input type="button" onclick="location=window.location.search;" value="Volver" />
<?php 
}
}
else
{
    ?>
    <br>
    <span class="mensajeERROR">Hay un error</span>
    <br>
<input type="button" onclick="location=window.location.search;" value="Volver" />
<?php
}
?>
</fieldset>


