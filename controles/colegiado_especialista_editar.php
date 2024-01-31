<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/colegiadoEspecialistaLogic.php');
require_once ('../dataAccess/resolucionesLogic.php');

$continua = TRUE;
$mensaje = "";
if (isset($_GET['id']) && $_GET['id'] <> ""){
    $idColegiadoEspecialista = $_GET['id'];
    $resEspecialista = obtenerColegiadoEspecialistaPorId($idColegiadoEspecialista);
    if ($resEspecialista['estado']){
        $datos = $resEspecialista['datos'];
        $fechaEspecialista = $datos['fechaEspecialista'];
        $fechaRecertificacion = $datos['fechaRecertificacion'];
        $distritoOrigen = $datos['distritoOrigen'];
        $fechaVencimiento = $datos['fechaVencimiento'];
        $origen = $datos['tipoespecialista'];
        $nombreEspecialidad = $datos['nombreEspecialidad'];
        $idResolucionDetalle = $datos['idResolucionDetalle'];
        $incisoArticulo8 = $datos['incisoArticulo8'];
        if (isset($incisoArticulo8) && $incisoArticulo8 <> "") {
            $mostrarIncisios = "display: display;";
        } else {
            $mostrarIncisios = "display: none;";
        }
        $idTipoEspecialista = $datos['idTipoEspecialista'];

        //busco los datos del colegiado
        $idColegiado = $datos['idColegiado'];
        $resColegiado = obtenerColegiadoPorId($idColegiado);
        if ($resColegiado['datos']) {
            $colegiado = $resColegiado['datos'];
            $matricula = $colegiado['matricula'];
            $apellidoNombre = trim($colegiado['apellido']).' '.trim($colegiado['nombre']);
        } else { 
            $mensaje .= $resColegiado['mensaje'];
            $continua = FALSE;
        }

    } else {
        //error al buscar expediente
        $mensaje .= $resEspecialista['mensaje'];
        $continua = FALSE;
    }
} else {
    $mensaje .= "Ingreso incorrecto";
    $continua = FALSE;
}
        
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
        $incisoArticulo8 = $_POST['incisoArticulo8'];
    }   
    ?>  
    <div class="container-fluid">
        <div class="panel panel-default">
        <div class="panel-heading"><h4><b>Editar datos del especialista</b></h4></div>
        <div class="panel-body"> 
            <form id="formEColegiadoEspecialista" name="formEColegiadoEspecialista" method="POST" onSubmit="" action="datosColegiadoEspecialista\abm_colegiadoEspecialista.php">
                <div class="row">
                    <div class="col-md-6">
                        <b>Apellido y Nombre</b>  
                        <input type="text" class="form-control" id="apellidoNombre" name="apellidoNombre" value="<?php echo $apellidoNombre; ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <b>Matrícula</b>  
                        <input type="text" class="form-control" id="matricula" name="matricula" value="<?php echo $matricula; ?>" readonly>
                    </div>
                </div>

                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-6">
                        <b>Especialidad</b>  
                        <input type="text" class="form-control" id="especialidad" name="especialidad" value="<?php echo $nombreEspecialidad; ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <b>Fecha Especialista</b>  
                        <input type="text" class="form-control" id="fechaEspecialista" name="fechaEspecialista" value="<?php echo cambiarFechaFormatoParaMostrar($fechaEspecialista); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <b>Fecha Recertificación</b>  
                        <input type="text" class="form-control" id="fechaEspecialista" name="fechaEspecialista" value="<?php echo cambiarFechaFormatoParaMostrar($fechaRecertificacion); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <b>Fecha Vencimiento</b>  
                        <input type="text" class="form-control" id="fechaEspecialista" name="fechaEspecialista" value="<?php echo cambiarFechaFormatoParaMostrar($fechaVencimiento); ?>" readonly>
                    </div>
                </div>

                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-6">
                        <b>Origen</b>  
                        <?php 
                        $resTipoEspecialista = obtenerTiposEspecialista();
                        if ($resEspecialista['estado']) {
                        ?>
                        <select class="form-control" id="idTipoEspecialista" name="idTipoEspecialista"  onChange="habilitar(this)" required>
                            <option value="">Seleccione el Inciso correspondiente</option>
                            <?php 
                            foreach ($resTipoEspecialista['datos'] as $dato) {
                            ?>
                                <option value="<?php echo $dato['id'] ?>" <?php if ($idTipoEspecialista == $dato['id']) { ?> selected="" <?php } ?>><?php echo $dato['nombre']; ?></option>
                            <?php
                            }
                            ?>
                        </select>                        
                        <?php 
                        }
                        ?>                    
                    </div>
                    <div class="col-md-6"id="incisoArt8" style="<?php echo $mostrarIncisios; ?>">
                        <b>Inciso - Articulo 8 por Excepción</b>&nbsp;&nbsp;&nbsp;
                        <select class="form-control" id="inciso" name="inciso" >
                            <option value="">Seleccione el Inciso correspondiente</option>
                            <option value="a" <?php if ($incisoArticulo8 == "a") { ?> selected="" <?php } ?>>Inciso a - </option>
                            <option value="b" <?php if ($incisoArticulo8 == "b") { ?> selected="" <?php } ?>>Inciso b - Universitario</option>
                            <option value="c" <?php if ($incisoArticulo8 == "c") { ?> selected="" <?php } ?>>Inciso c - CONFEMECO</option>
                            <option value="d" <?php if ($incisoArticulo8 == "d") { ?> selected="" <?php } ?>>Inciso d - Por puntaje</option>
                            <option value="e" <?php if ($incisoArticulo8 == "e") { ?> selected="" <?php } ?>>Inciso e - Curso Superior</option>
                            <option value="f" <?php if ($incisoArticulo8 == "f") { ?> selected="" <?php } ?>>Inciso f - Residencia</option>
                        </select>                                            
                    </div>
                </div>

                <div class="row">&nbsp;</div>
                <div class="row">
                     <div style="text-align:center">
                         <button type="submit"  class="btn btn-success " >Confirma datos</button>
                         <input type="hidden" name="accion" id="accion" value="3">
                         <input type="hidden" name="idColegiadoEspecialista" id="idColegiadoEspecialista" value="<?php echo $idColegiadoEspecialista; ?>">
                         <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>">
                     </div>
                </div>  
         </form>   
        </div>
     </div>
<!-- BOTON VOLVER -->    
<div class="col-md-12"">
    <form  method="POST" action="colegiado_especialista.php?idColegiado=<?php echo $idColegiado; ?>">
        <button type="submit" class="btn btn-info" name='volver' id='name'>Volver </button>
    </form>
</div>  
<div class="row">&nbsp;</div>
<?php
}
require_once '../html/footer.php';
?>
<script type="text/javascript">
    function habilitar(sel) {
        if (sel.value == 2){ //exceptuado art.8
            divT = document.getElementById("incisoArt8");
            divT.style.display = "";
            divT = document.getElementById("distrito");
            divT.style.display = "none";
            divT = document.getElementById("calificacion");
            divT.style.display = "none";
            divT = document.getElementById("especialidad");
            divT.style.display = "";
            document.getElementById("especialidad").required = true;
            document.getElementById("especialidadDetalle").required = true;
        }else{
            if (sel.value == 7){ //otro distrito
                divT = document.getElementById("distrito");
                divT.style.display = "";
                divT = document.getElementById("incisoArt8");
                divT.style.display = "none";
                divT = document.getElementById("calificacion");
                divT.style.display = "none";
                divT = document.getElementById("especialidad");
                divT.style.display = "";
                document.getElementById("especialidad").required = true;
                document.getElementById("especialidadDetalle").required = true;
            }else{
                if (sel.value == 5){ //calificacion agregada
                    divT = document.getElementById("distrito");
                    divT.style.display = "none";
                    divT = document.getElementById("incisoArt8");
                    divT.style.display = "none";
                    divT = document.getElementById("calificacion");
                    divT.style.display = "";
                    divT = document.getElementById("especialidad");
                    divT.style.display = "none";
                    document.getElementById("especialidad").required = false;
                    document.getElementById("especialidadDetalle").required = false;
                }else{
                    divT = document.getElementById("incisoArt8");
                    divT.style.display = "none";
                    divT = document.getElementById("distrito");
                    divT.style.display = "none";
                    divT = document.getElementById("calificacion");
                    divT.style.display = "none";
                    divT = document.getElementById("especialidad");
                    divT.style.display = "";
                    document.getElementById("especialidad").required = true;
                    document.getElementById("especialidadDetalle").required = true;
                }
            }
        }
    }
    
</script>