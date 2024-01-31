<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/mesaEntradaEspecialistaLogic.php');
require_once ('../dataAccess/cajaDiariaLogic.php');
?>
<script>
$(document).ready(
    function () {
                $('#tablaOrdenada').DataTable({
                    "iDisplayLength":50,
                    "order": [[ 1, "desc" ]],
                    "language": {
                        "url": "../public/lang/esp.lang"
                    },
                    "bLengthChange": true,
                    "bFilter": true,
                    dom: 'T<"clear">lfrtip'                    
                });
    }
);
</script>
<?php
$continua = TRUE;
$resCajaDiaria = obtenerCajaAbierta();
if ($resCajaDiaria['estado']) {
    $idCajaDiaria = $resCajaDiaria['datos']['idCajaDiaria'];
} else {
    $continua = FALSE;
}

if ($continua) {
    if (isset($_POST['listaIdMesaEntrada'])) {
        $listaIdMesaEntrada = $_POST['listaIdMesaEntrada'];
    } else {
        $listaIdMesaEntrada = NULL;
    }

    if (isset($listaIdMesaEntrada)) {
        $idsMesaEntrada = explode(',', $listaIdMesaEntrada);
        $idColegiado = $_POST['idColegiado'];
        $resColegiado = obtenerColegiadoPorId($idColegiado);
        if ($resColegiado['estado'] && $resColegiado['datos']) {
            $colegiado = $resColegiado['datos'];
        }
        
        if (isset($_POST['mensaje'])) {
        ?>
            <div class="ocultarMensaje"> 
                <p class="<?php echo $_POST['clase'];?>"><?php echo $_POST['mensaje'];?></p>  
            </div>
         <?php
        } else {
            //obtengo la deuda, inicializo los campos a mostrar
            $totalDeuda = 0;
            $resDeuda = obtenerMesaEntradaEspecialistasAPagar($listaIdMesaEntrada);
            if ($resDeuda['estado']) {
                //inicializo los totales
                foreach ($resDeuda['datos'] as $row) {
                    $totalDeuda += $row['importe'];
                }
            }
        }
        ?>

    <div class="panel panel-info">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-9">
                    <h4>Cobranza expedientes de especialistas</h4>
                </div>
                <div class="col-md-3 text-left">
                    <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="cajadiaria_especialistas_listado.php?id=<?php echo $idCajaDiaria; ?>">
                        <button type="submit"  class="btn btn-info" >Volver </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="panel-body">
        <div class="row">
            <div class="col-md-2">
                <label>Matr&iacute;cula:&nbsp; </label><?php echo $colegiado['matricula']; ?>
            </div>
            <div class="col-md-4">
                <label>Apellido y Nombres:&nbsp; </label><?php echo $colegiado['apellido'].', '.$colegiado['nombre']; ?>
            </div>
            <div class="col-md-6">&nbsp;</div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center"><h4><b>Generar recibo de pago</b></h4></div>
        </div>
        <form id="datosPlanPagos" autocomplete="off" name="datosRecibo" method="POST" action="datosCajaDiaria\generar_recibo.php">
            <?php
            if ($totalDeuda > 0) {
                //<div class="row">&nbsp;</div>
            ?>
                <div class="row">
                    <div class="form-check col-md-12">
                        <h4><b class="text-center">Expedientes a cobrar &nbsp;</b></h4>
                        <table class="table table-hover" border="true">
                            <thead>
                                <tr>
                                    <th>Expediente</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Especialidad</th>
                                    <th>Tipo</th>
                                    <th>Importe</th>
                                    <th>Abonar</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php
                        foreach ($resDeuda['datos'] as $row) {
                            $idMesaEntrada = $row['idMesaEntrada'];
                            $especialidad = $row['especialidad'];
                            $nombreTipoEspecialidad = $row['nombreTipoEspecialidad'];
                            $importe = $row['importe'];
                            $fecha = cambiarFechaFormatoParaMostrar($row['fechaIngreso']);
                            $incisoArticulo8 = $row['incisoArticulo8'];
                            $numeroExpediente = $row['numeroExpediente'];
                            $anioExpediente = $row['anioExpediente'];
                            /*
                            ?>
                            <div class="col-md-4">
                                <input class="form-check-input" name="lasCuotas[]" type="checkbox" checked="checked" value="<?php echo $idMesaEntrada ?>" 
                                       id="<?php echo $idMesaEntrada ?>">
                                <label class="form-check-label" for="<?php echo $idMesaEntrada ?>">
                                  <?php echo $especialidad.' - '.$nombreTipoEspecialidad.': $'.$importe ?>
                                </label>
                            </div>
                            */
                            ?>
                            <tr>
                                <td><?php echo $numeroExpediente.'/'.$anioExpediente; ?></td>
                                <td><?php echo $fecha; ?></td>
                                <td><?php echo $especialidad; ?></td>
                                <td><?php echo $nombreTipoEspecialidad; if (isset($incisoArticulo8) && $incisoArticulo8 <> "") { echo "Inciso ".$incisoArticulo8;} ?></td>
                                <td><?php echo $importe; ?></td>
                                <td>
                                    <input type="checkbox" name="generarRecibo[]" id="generarRecibo[]" checked="checked" value="<?php echo $idMesaEntrada ?>">&nbsp;
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php 
                }
                ?>
                    <?php
                    include 'cajadiaria_forma_pago.php'; 
                    ?>   
            
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <?php
                    if ($totalDeuda > 0) {
                    ?>
                        <button type="submit" name='confirma' id='confirma' class="btn btn-success" >Confirma Recibo</button>
                        <input type="hidden" name="idColegiado" id="idColegiado" value="<?php echo $idColegiado; ?>" />
                        <input type="hidden" name="listaIdMesaEntrada" id="listaIdMesaEntrada" value="<?php echo $listaIdMesaEntrada; ?>" />
                        <input type="hidden" name="tipoRecibo" id="tipoRecibo" value="ESPECIALISTAS" />
                    <?php 
                    } else {
                    ?>
                        <h4 class="alert alert-warning">No registra deuda para generar Recibo.</h4>
                    <?php
                    }
                    ?>
                </div>
            </div>    
        </form>
            <div class="row">&nbsp;</div>
        </div>
    </div>
    <?php
    }
} else {
?>
    <div class="row">&nbsp;</div>
    <div class="row">
        <div class="alert alert-warning">NO HAY CAJA ABIERTA, DEBE IR A CAJAS DIARIAS Y ABRIR PRIMERO UNA CAJA DEL DIA</div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="row text-center">
        <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="cajadiaria.php">
            <button type="submit"  class="btn btn-info" >Volver a Caja Diaria</button>
        </form>
    </div>
<?php  
}
require_once '../html/footer.php';
