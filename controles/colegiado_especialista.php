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
?>
<script>
$(document).ready(
    function () {
                $('#tablaEspecialista').DataTable({
                    "iDisplayLength":8,
                     "order": [[ 0, "desc" ], [ 1, "asc"]],
                    "language": {
                        "url": "../public/lang/esp.lang"
                    },
                    "bLengthChange": false,
                    "bFilter": false,
                });
    }
);
</script>
<?php
if (isset($_GET['idColegiado'])) {
    $_SESSION['menuColegiado'] = "Especialista";
    $periodoActual = $_SESSION['periodoActual'];
    $idColegiado = $_GET['idColegiado'];
    $resColegiado = obtenerColegiadoPorId($idColegiado);
    if ($resColegiado['estado'] && $resColegiado['datos']) {
        $colegiado = $resColegiado['datos'];
        $muestraMenuCompleto = TRUE;
        include 'menuColegiado.php';
        ?>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-md-6">
                <p>Apellido y Nombres:&nbsp; <b><?php echo $colegiado['apellido'].', '.$colegiado['nombre']; ?></b> - Matr&iacute;cula:&nbsp; <b><?php echo $colegiado['matricula']; ?></b></p>
            </div>
            <div class="col-md-3"><h4><b>Especialidades</b></h4></div>
            <div class="col-md-3">
                <?php
                $fechaHasta = date('Y-m-d');
                $fechaDesde = sumarRestarSobreFecha($fechaHasta, 4, 'month', '-');

                $resPagos = tienePagosPotTituloEspecialista($idColegiado, $fechaDesde, $fechaHasta);
                if ($resPagos['estado']) {
                    ?>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#pagosModal">Tiene pagos para título especialista</button>                    
                <?php
                } else {
                ?>
                    &nbsp;
                <?php
                }
                ?>
            </div>
        </div>
        <?php
        //busco las especialidades y la cantidad que tenga por nombre de especialidad
        //en caso de tener mas de una por diferente origen, debo mostrar las fechas de especialista por cada tipo
        $resEspecialista = obtenerEspecialidadesPorIdColegiado($idColegiado);
        if ($resEspecialista['estado']) {
            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-group" id="accordion">
                        <?php 
                        $idEspecialidadAnterior = NULL;
                        foreach ($resEspecialista['datos'] as $especialista) {
                            if ($especialista['idEspecialidad'] <> $idEspecialidadAnterior) {
                                if (isset($idEspecialidadAnterior)) {
                                ?>
                                    </div>
                                <?php 
                                }
                                $idEspecialidad = $especialista['idEspecialidad'];
                                $nombreEspecialidad = $especialista['nombreEspecialidad'];
                                $codigoEspecialidad = $especialista['codigoEspecialidad'];
                                $codigoEspecialidad = substr($codigoEspecialidad, 0, 2).'.'.substr($codigoEspecialidad, 2, 2).'.'.substr($codigoEspecialidad, 4, 2);
                                $idEspecialidadAnterior = $idEspecialidad;
                                ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $idEspecialidad ?>"><?php echo $nombreEspecialidad.' - '.$codigoEspecialidad; ?></a>
                                        </h4>
                                    </div>
                            <?php 
                            }
                            $idColegiadoEspecialista = $especialista['idColegiadoEspecialista'];
                            $fechaCarga= cambiarFechaFormatoParaMostrar($especialista['fechaCarga']);
                            $fechaEspecialista = cambiarFechaFormatoParaMostrar($especialista['fechaEspecialista']);
                            $fechaRecertificacion = cambiarFechaFormatoParaMostrar($especialista['fechaRecertificacion']);
                            $distritoOrigen = $especialista['distritoOrigen'];
                            $fechaVencimiento = cambiarFechaFormatoParaMostrar($especialista['fechaVencimiento']);
                            $tipoespecialista = $especialista['tipoespecialista'];
                            //obtengo la fecha de jerarquizado
                            if ($distritoOrigen <> "NACIÓN") {
                                $resJerarquizado = obtenerFechaJerarquizadoConsultor($idColegiadoEspecialista, JERARQUIZADO);
                                if ($resJerarquizado['estado']){
                                    $fechaJerarquizado = cambiarFechaFormatoParaMostrar($resJerarquizado['fecha']);
                                } else {
                                    $fechaJerarquizado = NULL;
                                }
                                //obtengo la fecha de consultor
                                $resConsultor = obtenerFechaJerarquizadoConsultor($idColegiadoEspecialista, CONSULTOR);
                                if ($resConsultor['estado']){
                                    $fechaConsultor = cambiarFechaFormatoParaMostrar($resConsultor['fecha']);
                                    $fechaVencimiento = NULL;
                                } else {
                                    $fechaConsultor = NULL;
                                }
                            } else {
                                $fechaJerarquizado = NULL;
                                $fechaConsultor = NULL;
                            }

                            $origen = $especialista['origen'];
                            /*
                            $especialistaInciso = $especialista['especialistaInciso'];
                            if (isset($especialistaInciso) && $especialistaInciso <> "") {
                                $especialistaInciso = 'Inc.'.$especialistaInciso;
                            }
                            */
                            ?>
                            <div id="collapse<?php echo $idEspecialidad; ?>" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <div class="col-md-2">Especialista: <br><b><?php echo $fechaEspecialista;?></b></div>
                                    <div class="col-md-1">Recertificación: <br><b><?php echo $fechaRecertificacion;?></b></div>
                                    <div class="col-md-1">Distrito: <br><b><?php echo $distritoOrigen;?></b></div>
                                    <div class="col-md-1">Jerarquizado: <br><b><?php echo $fechaJerarquizado;?></b></div>
                                    <div class="col-md-1">Consultor: <br><b><?php echo $fechaConsultor;?></b></div>
                                    <div class="col-md-1">Vencimiento: <br><b><?php echo $fechaVencimiento;?></b></div>
                                    <div class="col-md-3">Origen: <br><b><?php echo $origen;?></b></div>
                                    <?php 
                                    if (verificarRolUsuario($_SESSION['user_id'], 81)) {
                                    ?>
                                        <div class="col-md-1"><a href="colegiado_especialista_editar.php?id=<?php echo $idColegiadoEspecialista; ?>" class="btn btn-primary">Editar origen</a></div>
                                    <?php 
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php 
                        }
                        ?>
                          </div>
                    </div>
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="<?php echo $resEspecialista['clase']; ?>" role="alert">
                <span class="<?php echo $resEspecialista['icono']; ?>" aria-hidden="true"></span>
                <span><strong><?php echo $resEspecialista['mensaje']; ?></strong></span>
            </div>        
        <?php        
        }
    } else {
    ?>
        <div class="<?php echo $resColegiado['clase']; ?>" role="alert">
            <span class="<?php echo $resColegiado['icono']; ?>" aria-hidden="true"></span>
            <span><strong><?php echo $resColegiado['mensaje']; ?></strong></span>
        </div>        
    <?php        
    }
}
require_once '../html/footer.php';
?>
        <!-- Modal -->
<div id="pagosModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header alert alert-info">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Pagos registrados entre el <?php echo cambiarFechaFormatoParaMostrar($fechaDesde) ?> y el <?php echo cambiarFechaFormatoParaMostrar($fechaHasta); ?> </h4>
      </div>
      <div class="modal-body">
          <p>
              <?php 
            if ($resPagos['estado'] && isset($resPagos['datos']) && sizeof($resPagos['datos']) > 0){
              ?>
                <table width="100%" id="" class="display">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Detalle</th>
                            <th style="text-align: center;">Fecha de Pago</th>
                            <th style="text-align: center;">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($resPagos['datos'] as $pagos) {
                            ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $pagos['detalle']; ?></td>
                                <td style="text-align: center;"><?php echo cambiarFechaFormatoParaMostrar($pagos['fechaPago']); ?></td>
                                <td style="text-align: center;"><?php echo $pagos['monto']; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
              }
              ?>
          </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>        

        
        