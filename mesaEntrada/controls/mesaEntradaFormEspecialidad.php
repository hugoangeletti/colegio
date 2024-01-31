<?php
    require_once 'seguridad.php';
    
    if(isset($_GET['action']))
    {
        $accion = $_GET['action'];
    }
?>
<script>
    $(document).ready(function(){
        $(".tituloWrap").hide();
    });
</script>
<div id="titulo">
    <h3>Mesa de Entrada</h3>
    <h4>Alta de Solicitud de Especialidad</h4>
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
    
    $verificacion = verificarPermisoColegiado($estadoMatricular['Estado'], 2);
    
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
    $(document).ready(function(){
        $(".formTipoEspecialidad :input").click(function(){
            if(($(this).val() == 'E')||($(this).val() == 'X')||($(this).val() == 'O'))
                {
                    $(".formTipoEspecialidad").attr("action","buscarEspecialidad.php");
                    $.ajax({ 
                        url: $(".formTipoEspecialidad").attr("action"), 
                        type: "post", 
                        data: $(".formTipoEspecialidad").serialize(), 
                        success: function(data){ 
                            $("#formularioEspecialidad").html(data);
                        } 
                    });
                }else{
                    $(".formTipoEspecialidad").attr("action","formEspecialidad.php?action=<?php echo $accion ?>");
                    $.ajax({ 
                        url: $(".formTipoEspecialidad").attr("action"), 
                        type: "post", 
                        data: $(".formTipoEspecialidad").serialize(), 
                        success: function(data){ 
                            $("#page-wrap").html(data);
                        } 
                    });
                }
              
        });
    });
</script>
<div id="formularioEspecialidad">
    <?php
    require_once 'buscarEspecialidad.php';
    ?>
    <!--
    <fieldset>
        <legend>Tipo de especialidad solicitada</legend>
    <form class="formTipoEspecialidad" action="">
        <p>(Seleccione una de las opciones correspondiente al trámite que desea realizar el colegiado)</p>
        <br />
        <table>
            <tr>
                <td><label for='nuevaEspecialidad'>Nueva Especialidad</label></td>
                <td><input id='nuevaEspecialidad' type="radio" name="tipoEspecialidad" value="E" /></td>
            </tr>
            <tr>
                <td><label for='especialistaExceptuado'>Especialista Exceptuado Art.8</label></td>
                <td><input id='especialistaExceptuado' type="radio" name="tipoEspecialidad" value="X" /></td>
            </tr>
            <tr>
                <td><label for='especialistaJerarquizado'>Especialista Jerarquizado</label></td>
                <td><input id='especialistaJerarquizado' type="radio" name="tipoEspecialidad" value="J" /></td>
            </tr>
            <tr>
                <td><label for='especialistaConsultor'>Especialista Consultor</label></td>
                <td><input id='especialistaConsultor' type="radio" name="tipoEspecialidad" value="C" /></td>
            </tr>
            <tr>
                <td><label for='recertificacion'>Recertificación</label></td>
                <td><input id='recertificacion' type="radio" name="tipoEspecialidad" value="R" /></td>
            </tr>
            <tr>
                <td><label for='nuevaCalificacion'>Nueva Calificación Agregado</label></td>
                <td><input id='nuevaCalificacion' type="radio" name="tipoEspecialidad" value="A" /></td>
            </tr>
            <tr>
                <td><label for='especialistaOtroDistrito'>Especialista de Otro Distrito</label></td>
                <td><input id='especialistaOtroDistrito' type="radio" name="tipoEspecialidad" value="O" /></td>
            </tr>
        <input type="hidden" name="matricula" value="<?php echo $aColegiado['Matricula'] ?>" />
        </table>
    </form>
    </fieldset>
    -->
</div>

<input type="button" onclick="location=window.location.search;" value="Cancelar" />
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
?>
</fieldset>

