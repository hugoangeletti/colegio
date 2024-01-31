<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/resolucionesLogic.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/mesaEntradaEspecialistaLogic.php');
require_once ('../dataAccess/resolucionesLogic.php');

$continua = TRUE;
if (isset($_GET['accion']) && isset($_GET['estado']) && isset($_GET['anio']) && isset($_GET['idResolucion'])) {
    $accion = $_GET['accion'];
    $estadoResoluciones = $_GET['estado'];
    $anioResoluciones = $_GET['anio'];
    $idResolucion = $_GET['idResolucion'];
    $resResolucion = obtenerResolucionPorId($idResolucion);
    if ($resResolucion['estado']) {
        $resolucion = $resResolucion['datos'];
        $fechaResolucion = $resolucion['fecha'];
        $tipoResolucion = $resolucion['idTipoResolucion'];
        $titulo = 'Resolución de Especialistas Nº '.$resolucion['numero'].' de fecha '.  cambiarFechaFormatoParaMostrar($fechaResolucion);
    } else {
        $continua = FALSE;        
    }
     
    if (isset($_GET['id'])){
        $idResolucionDetalle = $_GET['id'];
    } else {
        $idResolucionDetalle = NULL;
    }

    if (isset($idResolucionDetalle)){
        $resDetalle = obtenerResolucionDetallePorId($idResolucionDetalle);
        if ($resDetalle['estado']){
            $resolucionDetalle = $resDetalle['datos'];
            $idResolucion = $resolucionDetalle['idResolucion'];
            $tipoEspecialista = $resolucionDetalle['tipo'];
            $inciso = $resolucionDetalle['inciso'];
            $especialidad = $resolucionDetalle['especialidad'];
            $especialidadDetalle = $resolucionDetalle['especialidadDetalle'];
            $estado = $resolucionDetalle['estado']; 
            $fechaAprobada = $resolucionDetalle['fechaAprobada'];
            $fechaRecertificacion = $resolucionDetalle['fechaRecertificacion'];
            $idColegiado = $resolucionDetalle['idColegiado'];
            $matricula = $resolucionDetalle['matricula'];
            $apellidoNombre = trim($resolucionDetalle['apellido']).' '.trim($resolucionDetalle['nombre']);
        } else {
            $continua = FALSE;
        }
        $titulo .= " (Editar Matrícula)";
        $nombreBoton="Guardar cambios";
    } else {
        $titulo .= " (Nueva Matrícula)";
        $nombreBoton="Guardar";
        if ($tipoResolucion == "R") {
            $tipoEspecialista = "R";
        } else {
            $tipoEspecialista = "";
        }
        $especialidad = "";
        $especialidadDetalle = "";
        $estado = "A"; 
        $fechaAprobada = $fechaResolucion;
        $fechaRecertificacion = "";
        $idColegiado = "";
        $matricula = NULL;
        $apellidoNombre = "";
        $inciso = "";
    }        
} else {
    $continua = FALSE;
}
?>

<div class="container-fluid">
    <div class="panel panel-default">
    <div class="panel-heading"><h4><b><?php echo $titulo; ?></b></h4></div>
    <div class="panel-body">
        <?php
        if ($continua){
        ?>
            <?php
            if (isset($_POST['mensaje']))
            {
             ?>
                <div id="divMensaje"> 
                    <p class="<?php echo $_POST['tipomensaje'];?>"><?php echo $_POST['mensaje'];?></p>  
                </div>
             <?php    
                $idResolucion = $_POST['idResolucion'];
                $tipo = $_POST['tipo'];
                $especialidad = $_POST['especialidad'];
                $especialidadDetalle = $_POST['especialidadDetalle'];
                $estado = $_POST['estado']; 
                $fechaAprobada = $_POST['fechaAprobada'];
                $fechaRecertificacion = $_POST['fechaRecertificacion'];
                $idColegiado = $_POST['idColegiado'];
                $matricula = $_POST['matricula'];
                $apellidoNombre = $_POST['apellidoNombre'];
            }
            
            if (isset($_POST['expedienteNumero']) && isset($_POST['expedienteAnio'])) {
                $expedienteNumero = $_POST['expedienteNumero'];
                $expedienteAnio = $_POST['expedienteAnio'];
            } else {
                $expedienteNumero = NULL;
                $expedienteAnio = NULL;
            }

            ?>
            <form id="formExpediente" name="formExpediente" method="POST" onSubmit="" action="especialidades_resoluciones_matricula_form.php?idResolucion=<?php echo $idResolucion; ?>&estado=<?php echo $estadoResoluciones; ?>&accion=1&anio=<?php echo $anioResoluciones ?>">
                <div class="row">
                    <div class="col-md-2">
                        <b>Expediente *</b>
                        <input class="form-control" <?php if (!isset($expedienteNumero)) { ?> autofocus <?php } ?> autocomplete="OFF" type="text" name="expedienteNumero" id="expedienteNumero" required="" value="<?php echo $expedienteNumero; ?>"/>
                    </div>                    
                    <div class="col-md-2">
                        <b>Año *</b>
                        <input class="form-control" autocomplete="OFF" type="text" name="expedienteAnio" id="expedienteAnio" required="" value="<?php echo $expedienteAnio; ?>"/>
                    </div>                    
                    <div class="col-md-3">
                        <br>
                        <button type="submit"  class="btn btn-info " >Buscar</button>
                    </div>
                </div>  
            </form>
            <div class="row">&nbsp;</div>
            <?php
            if (isset($expedienteNumero) && isset($expedienteAnio)) {
                $expedienteNumero = $_POST['expedienteNumero'];
                $expedienteAnio = $_POST['expedienteAnio'];
                $resExpediente = obtenerEspecialistaPorExpediente($expedienteNumero, $expedienteAnio);
                if ($resExpediente['estado']) {
                    $datosExp = $resExpediente['datos']; 
                    $idColegiado = $datosExp['idColegiado'];
                    $matricula = $datosExp['matricula'];
                    $apellidoNombre = $datosExp['apellidoNombre'];
                    $idTipoEspecialista = $datosExp['idTipoEspecialista'];
                    $nombreTipoEspecialista = $datosExp['nombreTipoEspecialista'];
                    $nombreEspecialidad = $datosExp['nombreEspecialidad'];
            ?>
                <form id="formElecciones" name="formResolucion" method="POST" onSubmit="" action="datosResoluciones\abm_resolucion.php">
                    <div class="row">
                        <div class="col-md-2">
                            <b>Matr&iacute;cula</b>
                            <input class="form-control" type="text" name="matricula" id="matricula" value="<?php echo $matricula ?>" readonly="" />
                            <input type="hidden" name="idColegiado" id="idColegiado" required="" value="<?php echo $idColegiado; ?>" />
                        </div>                    
                        <div class="col-md-5">
                            <b>Apellido y Nombre</b>
                            <input class="form-control" type="text" name="apellidoNombre" id="apellidoNombre" value="<?php echo $apellidoNombre; ?>" readonly="" />
                        </div>                    
                        <div class="col-md-5">
                            <b>Especialidad</b><br>
                            <input class="form-control" type="text" name="nombreEspecialidad" id="nombreEspecialidad" value="<?php echo $nombreEspecialidad; ?>" readonly=""/>
                        </div>
                    </div>
                    <div class="row">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-5">
                            <b>Tipo de Especialista</b>  
                            <input class="form-control" type="text" name="nombreTipoEspecialista" id="nombreTipoEspecialista" value="<?php echo $nombreTipoEspecialista; ?>" readonly="" />
                        </div>
                        <div class="col-md-2">
                            <?php
                            if ($idTipoEspecialista == EXCEPTUADO_ART_8) {
                            ?>
                                <b>Inciso</b>&nbsp;&nbsp;&nbsp;
                                <select class="form-control" id="inciso" name="inciso" required="">
                                    <option value="" selected>Seleccione Inciso</option>
                                    <option value="a" <?php if($inciso == "a") { echo 'selected'; } ?>>a</option>
                                    <option value="b" <?php if($inciso == "b") { echo 'selected'; } ?>>b</option>
                                    <option value="c" <?php if($inciso == "c") { echo 'selected'; } ?>>c</option>
                                    <option value="d" <?php if($inciso == "d") { echo 'selected'; } ?>>d</option>
                                    <option value="e" <?php if($inciso == "e") { echo 'selected'; } ?>>e</option>
                                    <option value="f" <?php if($inciso == "f") { echo 'selected'; } ?>>f</option>
                                </select>
                                <!--
                                <div class="radio-inline" >
                                    <label><input type="radio" name="inciso" id="inciso" value="a" >a</label>
                                </div>
                                <div class="radio-inline" >
                                    <label><input type="radio" name="inciso" id="inciso" value="b" >b</label>
                                </div>
                                <div class="radio-inline" >
                                    <label><input type="radio" name="inciso" id="inciso" value="c" >c</label>
                                </div>
                                <div class="radio-inline" >
                                    <label><input type="radio" name="inciso" id="inciso" value="d" >d</label>
                                </div>
                                <div class="radio-inline" >
                                    <label><input type="radio" name="inciso" id="inciso" value="e" >e</label>
                                </div>
                                <div class="radio-inline" >
                                    <label><input type="radio" name="inciso" id="inciso" value="f" >f</label>
                                </div>
                                -->
                            <?php
                            }
                            ?>
                        </div>
                        <div class="col-md-2">
                            <b>Aprobada</b>  
                            <input class="form-control" type="date" name="fechaAprobada" value="<?php echo $fechaAprobada; ?>" readonly="" />
                        </div>                        
                        <div class="col-md-2">
                            <?php 
                            if ($idTipoEspecialista == RECERTIFICACION) {
                            ?>
                                <b>Recertificación</b>  
                                <input class="form-control" type="date" name="fechaRecertificacion" value="<?php echo $fechaRecertificacion; ?>" />
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">&nbsp;</div>
                    <div class="row">
                         <div style="text-align:center">
                             <button type="submit"  class="btn btn-success " ><?php echo $nombreBoton; ?></button>
                         </div>
                    </div>  
                    <?php
                    if ($idResolucionDetalle) {
                    ?>
                        <input type="hidden" name="idResolucionDetalle" id="idResolucionDetalle" value="<?php echo $idResolucionDetalle; ?>" />
                    <?php
                    }
                    ?>
                    <input type="hidden" name="idResolucion" id="idResolucion" value="<?php echo $idResolucion; ?>" />
                    <input type="hidden" id="estadoResoluciones" name="estadoResoluciones" value="<?php echo $estadoResoluciones; ?>">
                    <input type="hidden" id="anioResoluciones" name="anioResoluciones" value="<?php echo $anioResoluciones; ?>">
                    <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
             </form>   
        <?php
                } else {
                ?>
                    <div class="<?php echo $resExpediente['clase']; ?>" role="alert">
                    <span class="<?php echo $resExpediente['icono']; ?>" ></span>
                    <span><strong><?php echo $resExpediente['mensaje']; ?></strong></span>
                </div>
                <?php
                }
            }
        } 
        ?>
    </div>
</div>
<!-- BOTON VOLVER -->    
    <div class="col-md-12" style="text-align:right;">
        <form  method="POST" action="especialidades_resoluciones_matriculas.php?idResolucion=<?php echo $idResolucion; ?>&estado=<?php echo $estadoResoluciones; ?>&accion=3&anio=<?php echo $anioResoluciones ?>">
            <button type="submit" class="btn btn-default" name='volver' id='name'>Volver </button>
            <input type="hidden" id="estadoResoluciones" name="estadoResoluciones" value="<?php echo $estadoResoluciones; ?>">
            <input type="hidden" id="anioResoluciones" name="anioResoluciones" value="<?php echo $anioResoluciones; ?>">
       </form>
    </div>  

</div>
<?php
require_once '../html/footer.php';
