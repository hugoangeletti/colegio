<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/colegiadoCargoLogic.php');

if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
} else {
    $accion = 1;
}
switch ($accion) {
    case 1:
        $titulo = 'Nuevo Consejero';
        $panel = 'panel-success';
        break;

    case 2:
        $titulo = 'Eliminar Consejero';
        $panel = 'panel-danger';
        break;

    case 3:
        $titulo = 'Editar Consejero';
        $panel = 'panel-info';
        break;

    default:
        $titulo = 'Consejero';
        break;
}

if (isset($_GET['idColegiadoCargo']) || isset($_POST['idColegiadoCargo'])) {
    $periodoActual = $_SESSION['periodoActual'];
    if (isset($_GET['idColegiadoCargo'])) {
        $idColegiadoCargo = $_GET['idColegiadoCargo'];
    } else {
        $idColegiadoCargo = $_POST['idColegiadoCargo'];
    }
    if (isset($_GET['id'])) {
        $idConsultorio = $_GET['id'];
    } else {
        $idConsultorio = NULL;
    }
    if (isset($_POST['mensaje'])) {
    ?>
       <div class="ocultarMensaje"> 
           <p class="<?php echo $_POST['clase'];?>"><?php echo $_POST['mensaje'];?></p>  
       </div>
     <?php
        $idCargoColegio = $_POST['idCargoColegio'];
        $idColegiado = $_POST['idColegiado'];
        $apellidoNombre = $_POST['apellidoNomre'];
        $fechaDesde = $_POST['fechaDesde'];
        $fechaHasta = $_POST['fechaHasta'];
        $fechaMesaDesde = $_POST['fechaMesaDesde'];
        $fechaMesaHasta = $_POST['fechaMesaHasta'];
        $idLiquidacion = $_POST['idLiquidacion'];
    } else {
        $idCargoColegio = '';
        $idColegiado = '';
        $apellidoNombre = '';
        $fechaDesde = '';
        $fechaHasta = '';
        $fechaMesaDesde = '';
        $fechaMesaHasta = '';
        $idLiquidacion = '';
    }
?>
<div class="panel <?php echo $panel; ?>">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-9">
                <h4>Consultorio</h4>
            </div>
            <div class="col-md-3 text-left">
                <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="colegiado_consultorios.php?idColegiado=<?php echo $idColegiado;?>">
                    <button type="submit"  class="btn btn-info" >Volver a Consultorios del colegiado</button>
                </form>
            </div>
        </div>
    </div>
    <div class="panel-body">
<?php
    $resColegiado = obtenerColegiadoCargoPorId($idColegiadoCargo);
    if ($resColegiado['estado'] && $resColegiado['datos']) {
        $colegiadoCargo = $resColegiado['datos'];
        $continua = TRUE;    
    ?>
        <div class="row">
            <div class="col-md-12 text-center"><h4><b><?php echo $titulo; ?></b></h4></div>
        </div>
        <form class="form-control" id="datosConsultorio" autocomplete="off" name="datosConsultorio" method="POST" onSubmit="" action="datosColegiadoConsultorio/abm_consultorio.php">
            <div class="row">
                <div class="col-md-5">
                    <label>Apellido y Nombres:&nbsp; </label>
                    <input class="form-control" type="text" id="apellidoNombre" name="apellidoNombre" value="<?php echo trim($colegiadoCargo['apellido']).', '.trim($colegiadoCargo['nombre']); ?>" readonly=""/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label>Cargo *</label>
                    <input class="form-control" type="text" id="cargo" name="cargo" value="<?php echo $$colegiadoCargo['nombreCargo']; ?>" required=""/>
                </div>
                <div class="col-md-3">
                    <label>Fecha desde *</label>
                    <input type="date" class="form-control" id="fechaHabilitacion" name="fechaHabilitacion" value="<?php echo $colegiadoCargo['fechaDesde'];?>" required>
                </div>
                <div class="col-md-3">
                    <label>Fecha hasta </label>
                    <input type="date" class="form-control" id="ultimaInspeccion" name="ultimaInspeccion" value="<?php echo $colegiadoCargo['fechaHasta'];?>">
                </div>
            </div>
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit"  class="btn btn-success btn-lg" >Confirma </button>
                    <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
                    <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                    <input type="hidden" name="idColegiadoConsultorio" id="idColegiadoConsultorio" value="<?php echo $idConsultorio; ?>" />
                </div>
            </div>    
        </form>
    <?php
    } else {
    ?>
        <div class="col-md-12">
            <div class="<?php echo $resColegiado['clase']; ?>" role="alert">
                <span class="<?php echo $resColegiado['icono']; ?>" aria-hidden="true"></span>
                <span><strong><?php echo $resColegiado['mensaje']; ?></strong></span>
            </div>        
        </div>
    <?php
    } 
    ?>
    </div>    
</div>
    <?php
}
require_once '../html/footer.php';
?>
<!--AUTOCOMLETE-->
<script src="../public/js/bootstrap3-typeahead.js"></script>    
<script language="JavaScript">
    $(function(){
        var nameIdMap = {};
        $('#localidad_buscar').typeahead({ 
                source: function (query, process) {
                return $.ajax({
                    dataType: "json",
                    url: 'localidad.php',
                    data: {query: query},
                    type: 'POST',
                    success: function (json) {
                        process(getOptionsFromJson(json.data));
                    }
                });
            },
           
            minLength: 3,
            //maxItem:15,
            
            updater: function (item) {
                $('#idLocalidad').val(nameIdMap[item]);
                return item;
            }
        });
        function getOptionsFromJson(json) {
             
            $.each(json, function (i, v) {
                //console.log(v);
                nameIdMap[v.nombre] = v.id;
            });
            return $.map(json, function (n, i) {
                return n.nombre;
            });
        }
    });  
   
</script>