<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiacionAnualLogic.php');

if (isset($_POST['periodo'])) {
    $periodo = $_POST['periodo'];
} else {
    $periodo = $_SESSION['periodoActual'];
}
$accion = 1;
$titulo = 'Nuevo Informe';
$panel = 'panel-info';
$textoBoton = 'Confirmar';
$claseBoton = 'btn-info';
$readOnly = '';

$continua = TRUE;
if ($accion <> 1) {
    if (isset($_POST['idColegiacionAnual']) && $_POST['idColegiacionAnual']) {
        $idColegiacionAnual = $_POST['idColegiacionAnual'];
    } else {
        $resFalsosMedicos['clase'] = "alert alert-warning";
        $resFalsosMedicos['icono'] = "glyphicon glyphicon-exclamation-sign";
        $resFalsosMedicos['mensaje'] = "Datos mal ingresados";
        $continua = FALSE;
    }
} else {
    $idColegiacionAnual = NULL;
}
?>
<div class="panel <?php echo $panel; ?>">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-9">
                <h4>Informe Contable</h4>
            </div>
            <div class="col-md-3 text-left">
                <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="informe_contable_lista.php">
                    <button type="submit"  class="btn <?php echo $claseBoton ?>" >Volver al listado</button>
                    <input type="hidden" id="periodo" name="periodo" value="<?php echo $periodo; ?>">
                </form>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <?php
        if (isset($_POST['mensaje'])) {
        ?>
           <div class="ocultarMensaje"> 
               <p class="<?php echo $_POST['clase'];?>"><?php echo $_POST['mensaje'];?></p>  
           </div>
         <?php
            $periodo = $_POST['periodo'];
            $mesProcesado = $_POST['mesProcesado'];
        } else {
            $mesProcesado = NULL;
        }
        if ($continua) {
        ?>
            <div class="row">
                <div class="col-md-12 text-center"><h4><b><?php echo $titulo; ?></b></h4></div>
            </div>
            <form id="datosColegiacion" autocomplete="off" name="datosColegiacion" method="POST" action="datosContable/abm_informe_contable.php">
                <div class="row">
                    <div class="col-md-4">
                        <label>Período: * </label>
                        <input class="form-control" autofocus autocomplete="OFF" type="number" id="periodo" name="periodo" value="<?php echo $periodo; ?>" placeholder="Ingrese Periodo" required="" readonly="" />
                    </div>
                    <div class="col-md-2">
                        <label>Mes: </label>
                        <select class="form-control" id="mesProcesado" name="mesProcesado" required >
                            <?php 
                            $i = 1;
                            while ($i <= 12) {
                                switch ($i) {
                                    case '1':
                                        $mesProcesado = $periodo.'05';
                                        $labelMes = obtenerMes(5).' de '.$periodo;
                                        break;
                                    
                                    case '2':
                                        $mesProcesado = $periodo.'06';
                                        $labelMes = obtenerMes(6).' de '.$periodo;
                                        break;
                                    
                                    case '3':
                                        $mesProcesado = $periodo.'07';
                                        $labelMes = obtenerMes(7).' de '.$periodo;
                                        break;

                                    case '4':
                                        $mesProcesado = $periodo.'08';
                                        $labelMes = obtenerMes(8).' de '.$periodo;
                                        break;

                                    case '5':
                                        $mesProcesado = $periodo.'09';
                                        $labelMes = obtenerMes(9).' de '.$periodo;
                                        break;

                                    case '6':
                                        $mesProcesado = $periodo.'10';
                                        $labelMes = obtenerMes(10).' de '.$periodo;
                                        break;

                                    case '7':
                                        $mesProcesado = $periodo.'11';
                                        $labelMes = obtenerMes(11).' de '.$periodo;
                                        break;

                                    case '8':
                                        $mesProcesado = $periodo.'12';
                                        $labelMes = obtenerMes(12).' de '.$periodo;
                                        break;

                                    case '9':
                                        $mesProcesado = ($periodo+1).'01';
                                        $labelMes = obtenerMes(1).' de '.($periodo+1);
                                        break;

                                    case '10':
                                        $mesProcesado = ($periodo+1).'02';
                                        $labelMes = obtenerMes(2).' de '.($periodo+1);
                                        break;

                                    case '11':
                                        $mesProcesado = ($periodo+1).'03';
                                        $labelMes = obtenerMes(3).' de '.($periodo+1);
                                        break;

                                    case '12':
                                        $mesProcesado = ($periodo+1).'04';
                                        $labelMes = obtenerMes(4).' de '.($periodo+1);
                                        break;

                                    default:
                                        $mesProcesado = $periodo.'XX';
                                        $labelMes = "ERROR";
                                        break;
                                }
                                ?>
                                <option value="<?php echo $mesProcesado; ?>"><?php echo $labelMes; ?></option>
                                <?php
                                $i += 1;
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">&nbsp;</div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="submit"  class="btn <?php echo $claseBoton ?> btn-lg" ><?php echo $textoBoton; ?> </button>
                        <input type="hidden" name="accion" id="accion" value="<?php echo $accion; ?>" />
                        <input type="hidden" name="idColegiacionAnual" id="idColegiacionAnual" value="<?php echo $idColegiacionAnual; ?>" />
                    </div>
                </div>    
            </form>
        <?php
        } else {
        ?>
            <div class="col-md-12">
                <div class="<?php echo $resColegiacion['clase']; ?>" role="alert">
                    <span class="<?php echo $resColegiacion['icono']; ?>" aria-hidden="true"></span>
                    <span><strong><?php echo $resColegiacion['mensaje']; ?></strong></span>
                </div>        
            </div>
        <?php 
        }
        ?>
    </div>    
</div>
<?php
require_once '../html/footer.php';
