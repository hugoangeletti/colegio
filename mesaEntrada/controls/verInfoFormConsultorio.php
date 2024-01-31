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
    $numeroTramite = true;
    $idZona = "";
    if(isset($_GET['iEvento']))
    {
        $hayHabilitacionConsultorio = true;
        $consultaDatosHabilitacion = obtenerHabilitacionConsultorioPorId($_GET['iEvento']);
        $datosHabilitacion = $consultaDatosHabilitacion -> fetch_assoc();
        $okey = true;
        $matricula = $datosHabilitacion['Matricula'];
        $consultaLocalidad = obtenerPartidoPorIdLocalidad($datosHabilitacion['IdLocalidad']);
        $localidad = $consultaLocalidad -> fetch_assoc();
        $idZona = $localidad['IdZona'];
        $_GET['idConsultorio'] = $datosHabilitacion['IdConsultorio'];
    }
    else
    {
        $hayHabilitacionConsultorio = false;
    }

?>
<script>
    $(document).ready(function(){
        $(".tituloWrap").hide();
    });
</script>
<div id="titulo">
    <h3>Mesa de Entrada</h3>
    <h4><?php echo $titulo ?> Solicitud de Habilitación de Consultorio</h4>
    <?php
        if($numeroTramite)
        {
    ?>
        <h4>Trámite Nº <?php echo $datosHabilitacion['IdMesaEntrada'] ?></h4>
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
    
    $verificacion = verificarPermisoColegiadoHabilitacionConsultorio($estadoMatricular['Estado'], 1, $estadoTesoreria);
    
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
<div class="divFormularioConsultorio">
<form id="formHabilitacionConsultorio" action="<?php echo $actionForm ?>" method="post">
    <br>
    <?php
    require_once 'mostrarConsultorio.php';
    ?>
    <br>
    <table class="tablaConsultorio">
        <tr>
            <td><b>Especialidad:</b></td>
            <td colspan='5'>
                <select name="especialidad" required disabled>
                    <option value="">Seleccione una Especialidad</option>
                    <?php
                        $especialidades = obtenerEspecialidades();
                        
                        if($especialidades)
                        {
                            if($especialidades -> num_rows != 0)
                            {
                                while($row = $especialidades -> fetch_assoc()){
                                    echo "<option value=".$row['Id']." ";
                                    if($row['Id'] == $datosHabilitacion['IdEspecialidad'])
                                    {
                                        echo "selected";
                                    }
                                    echo ">".utf8_encode($row['Especialidad'])."</option>";
                                }
                            }
                        }
                    ?>
                </select>
            </td>
        </tr>
        <?php
            if($datosConsultorio['TipoConsultorio'] == "U")
            {
                $autorizados = obtenerAutorizadosPorIdMesaEntradaConsultorio($datosHabilitacion['IdMesaEntradaConsultorio']);
                
                if($autorizados)
                {
                    if($autorizados -> num_rows != 0)
                    {
                        $i = 1;
                        while ($aut = $autorizados -> fetch_assoc())
                        {
                            ?>
    <tr>
        <td><b>Matrícula del Colegiado Autorizado <?php echo $i ?>:</b></td>
        <td><?php echo $aut['Matricula']." - ".utf8_encode($aut['Apellido'])." ".utf8_encode($aut['Nombres']) ?></td>
    </tr>
                            <?php
                            $i ++;
                        }
                    }
                }
            }
        ?>
    </table>
    <br>
        <?php
        require_once 'mostrarDatosPersonalesColegiado.php';
        ?>
    <br>
    <table>
    <tr>
        <?php
            if(isset($hayHabilitacionConsultorio)&&($hayHabilitacionConsultorio))
            {
                if(isset($_GET['fecha']))
                {
                ?>
        <td><input type="button" onclick="location='listaMesaEntrada.php?fecha=<?php echo $_GET['fecha'] ?>'" value="Volver" /></td>
                <?php
                }
                else
                {
                    if(isset($_GET['consultorio']))
                    {
                    ?>
            <td><input type="button" onclick="location='buscarPorConsultorio.php?BoM=ok&idConsultorio=<?php echo $_GET['consultorio']?>'" value='Volver'/></td>
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
                ?>
        <td><input type="button" onclick="location=window.location.search;" value="Cancelar" /></td>
                <?php
            }
        ?>
    </tr>
    </table>
</form>
</div>
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


