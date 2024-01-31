<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/colegiadoCargoLogic.php');

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
} else {
    $accion = 1;
    $idColegiadoCargo = NULL;
}
if (isset($_POST['idColegiadoCargo']) && $_POST['idColegiadoCargo'] > 0) {
    $idColegiadoCargo = $_POST['idColegiadoCargo'];
} else {
    $idColegiadoCargo = NULL;
}
if (isset($_POST['idColegiado']) && $_POST['idColegiado'] > 0) {
    $idColegiado = $_POST['idColegiado'];
} else {
    $idColegiado = NULL;
}
if (isset($_POST['idCargoColegio']) && $_POST['idCargoColegio'] > 0) {
    $idCargoColegio = $_POST['idCargoColegio'];
} else {
    $idCargoColegio = NULL;
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

    $periodoActual = $_SESSION['periodoActual'];
    if (isset($_POST['mensaje'])) {
    ?>
       <div class="ocultarMensaje"> 
           <p class="<?php echo $_POST['clase'];?>"><?php echo $_POST['mensaje'];?></p>  
       </div>
     <?php
    }
    $apellidoNombre = NULL;
    $fechaDesde = NULL;
    $fechaHasta = NULL;
    $fechaMesaDesde = NULL;
    $fechaMesaHasta = NULL;
?>
<div class="panel <?php echo $panel; ?>">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-9">
                <h4><?php echo $titulo; ?></h4>
            </div>
            <div class="col-md-3 text-left">
                <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="secretaria_consejeros.php?consulta=ok">
                    <button type="submit"  class="btn btn-info" >Volver a Consejeros</button>
                </form>
            </div>
        </div>
    </div>
    <div class="panel-body">
    <?php
    if ($accion <> 1) {
        $resColegiado = obtenerColegiadoCargoPorId($idColegiadoCargo);
        if ($resColegiado['estado']) {
            $colegiadoCargo = $resColegiado['datos'][0];
            $fechaDesde = $colegiadoCargo['fechaDesde'];
            $fechaHasta = $colegiadoCargo['fechaHasta'];
            $fechaMesaDesde = $colegiadoCargo['fechaMesaDesde'];
            $fechaMesaHasta = $colegiadoCargo['fechaMesaHasta'];
            $continua = TRUE;    
            ?>
            <h3>Apellido y Nombres:&nbsp; <?php echo trim($colegiadoCargo['apellido']).', '.trim($colegiadoCargo['nombre']); ?></h3>
            <h3>Matrícula:&nbsp; <?php echo $colegiadoCargo['matricula']; ?></h3>
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
    } else {
        if (!isset($_POST['idColegiado']) && !isset($_GET['idColegiado'])) {
        ?>
        <div class="row">&nbsp;</div>
        <div class="row">
            <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="secretaria_consejeros_form.php?consulta=ok">
                <div class="row">
                    <div class="col-md-3" style="text-align: right;">
                        <label>Matr&iacute;cula o Apellido y Nombre *</label>
                    </div>
                    <div class="col-md-7">
                        <input class="form-control" autofocus autocomplete="OFF" type="text" name="colegiado_buscar" id="colegiado_buscar" placeholder="Ingrese Matrícula o Apellido del colegiado" required=""/>
                        <input type="hidden" name="idColegiado" id="idColegiado" required="" />
                        <input type="hidden" name="accion" id="accion" value="1" />
                    </div>
                    <div class="col-md-2">
                        <button type="submit"  class="btn btn-success">Confirma colegiado</button>
                    </div>
                </div>
            </form>
        </div>
        <?php 
        }
    }

    if (isset($_POST['idColegiado']) || isset($_POST['idColegiadoCargo'])) {
    ?>
        <form id="datoCargo" autocomplete="off" name="datoCargo" method="POST" action="secretaria_consejeros_form.php?consulta=ok">
            <div class="row">
                <div class="col-md-6">
                    <label>Cargo *</label>
                    <select class="form-control" id="idCargoColegio" name="idCargoColegio" required="" onChange="this.form.submit()">
                        <option value="1" <?php if ($idCargoColegio == 1) { ?> selected="" <?php } ?>>Presidente</option>
                        <option value="2" <?php if ($idCargoColegio == 2) { ?> selected="" <?php } ?>>Secretario General</option>
                        <option value="3" <?php if ($idCargoColegio == 3) { ?> selected="" <?php } ?>>Tesorero</option>
                        <option value="4" <?php if ($idCargoColegio == 4) { ?> selected="" <?php } ?>>Vice Presidente</option>
                        <option value="5" <?php if ($idCargoColegio == 5) { ?> selected="" <?php } ?>>Pro Secretario</option>
                        <option value="6" <?php if ($idCargoColegio == 6) { ?> selected="" <?php } ?>>Pro Tesorero</option>
                        <option value="7" <?php if ($idCargoColegio == 7) { ?> selected="" <?php } ?>>Secretaria de Actas</option>
                        <option value="8" <?php if ($idCargoColegio == 8) { ?> selected="" <?php } ?>>Gerente</option>
                        <option value="9" <?php if ($idCargoColegio == 9) { ?> selected="" <?php } ?>>Secretario de Prensa y Difusión</option>
                        <option value="10" <?php if ($idCargoColegio == 10) { ?> selected="" <?php } ?>>Presidente</option>
                        <option value="11" <?php if ($idCargoColegio == 11) { ?> selected="" <?php } ?>>Consejero</option>
                        <option value="13" <?php if ($idCargoColegio == 13) { ?> selected="" <?php } ?>>Secretario de Coordinación y Enlace Institucional</option>
                    </select>
                    <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                    <input type="hidden" name="idColegiadoCargo" id="idColegiadoCargo" value="<?php echo $idColegiadoCargo; ?>" />
                </div>
            </div>
        </form>

        <form id="datosConsejero" autocomplete="off" name="datosConsejero" method="POST" action="datosConsejero/abm_consejero.php">
            <div class="row">
                <div class="col-md-3">
                    <label>Fecha desde *</label>
                    <input type="date" class="form-control" id="fechaDesde" name="fechaDesde" value="<?php echo $fechaDesde;?>" required>
                </div>
                <div class="col-md-3">
                    <label>Fecha hasta </label>
                    <input type="date" class="form-control" id="fechaHasta" name="fechaHasta" value="<?php echo $fechaHasta;?>">
                </div>
            </div>
            <div class="row">&nbsp;</div>
            <?php 
            if (isset($fechaMesaDesde)) {
            ?>
                <div class="row">
                    <div class="col-md-6">
                        <label>Cargo Mesa *</label>
                        <input class="form-control" type="text" id="cargo" name="cargo" value="<?php echo $colegiadoCargo['nombreCargo']; ?>" readonly=""/>
                    </div>
                    <div class="col-md-3">
                        <label>Fecha desde *</label>
                        <input type="date" class="form-control" id="fechaMesaDesde" name="fechaMesaDesde" value="<?php echo $fechaMesaDesde;?>" required>
                    </div>
                    <div class="col-md-3">
                        <label>Fecha hasta </label>
                        <input type="date" class="form-control" id="fechaMesaHasta" name="fechaMesaHasta" value="<?php echo $fechaMesaHasta;?>">
                    </div>
                </div>
                <div class="row">&nbsp;</div>
            <?php 
            }
            ?>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit"  class="btn btn-success btn-lg" >Confirma </button>
                    <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
                    <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                    <input type="hidden" name="idColegiadoCargo" id="idColegiadoCargo" value="<?php echo $idColegiadoCargo; ?>" />
                    <input type="hidden" name="idCargoColegio" id="idCargoColegio" value="<?php echo $idCargoColegio; ?>" />
                </div>
            </div>    
        </form>
    <?php 
    } 
    ?>
    </div>    
</div>
<?php
require_once '../html/footer.php';
?>
<!--AUTOCOMLETE-->
<script src="../public/js/bootstrap3-typeahead.js"></script>    
<script language="JavaScript">
    $(function(){
        var nameIdMap = {};
        $('#colegiado_buscar').typeahead({ 
                source: function (query, process) {
                return $.ajax({
                    dataType: "json",
                    url: 'colegiado.php',
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
                $('#idColegiado').val(nameIdMap[item]);
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