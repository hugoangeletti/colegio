<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/cobranzaLogic.php');
require_once ('../dataAccess/lugarPagoLogic.php');
?>
        <script>
            $(document).ready(function () {
                $('#tablaOrdenada').DataTable({
                    "iDisplayLength":10,
                    "order": [[ 0, "asc"]],
                    "language": {
                        "url": "../public/lang/esp.lang"
                    },
                    "bLengthChange": true,
                    "bFilter": true,
                    dom: 'T<"clear">lfrtip'
                });
            });
            
   
</script>

<?php
$continua = TRUE;
if (isset($_GET['id']) && $_GET['id'] <> "") {
    $id = explode("_", $_GET['id']);
    $idCobranza = $id[0];
    if (isset($id[1]) && $id[1] <> "") {
        $anio = $id[1];
    } else {
        $anio = NULL;
    }
    if (isset($id[2]) && $id[2] <> "") {
        $idLugarPago = $id[2];
    } else {
        $idLugarPago = NULL;
    }
    $resLote = obtenerLotePorId($idCobranza);
    if ($resLote['estado']) {
        $lote = $resLote['datos'];
        $fechaApertura = $lote['fechaApertura'];
        $lugarPago = $lote['lugarPago'];
        $cantidadComprobantes = $lote['cantidadComprobantes'];
        $totalRecaudacion = $lote['totalRecaudacion'];
    } else {
        $continua = FALSE;
    }
} else {
    $continua = FALSE;
}

if ($continua) {
    if (isset($_POST['mensaje']))
    {
     ?>
       <div class="ocultarMensaje"> 
       <p class="<?php echo $_POST['tipomensaje'];?>"><?php echo $_POST['mensaje'];?></p>  
       </div>
     <?php    
    }   
    ?> 
    <div class="panel panel-default">
    <div class="panel-heading"><h5>Lote de Cobranza N° <b><?php echo $idCobranza ?></b> de fecha <b><?php echo cambiarFechaFormatoParaMostrar($fechaApertura); ?></b>. Lugar de Pago: <b><?php echo $lugarPago; ?></b> - Recaudación: <b><?php echo number_format($totalRecaudacion, 2, ',', '.') ?></b> - Cantidad Comprobante: <b><?php echo $cantidadComprobantes; ?></b></h5></div>
    <div class="panel-body">
        <div class="row">
        <?php
        $resPagos = obtenerDetalleLote($idCobranza);
        if ($resPagos['estado']){
        ?>
            <br>
            <table id="tablaOrdenada" class="display">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Matrícula / Asistente</th>
                        <th>Apellido y Nombres</th>
                        <th>Cuota</th>
                        <th>Fecha de Pago</th>
                        <th>Importe cobrado</th>
                        <th>Recargo cobrado</th>
                        <th>Recibo</th>
                        <th>Tipo de Pago</th>
                        <!--<th>Editar</th>-->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($resPagos['datos'] as $dato) {
                        $idCobranzaDetalle = $dato['idCobranzaDetalle'];
                        $referencia = NULL;
                        $apellidoNombre = NULL;
                        if (isset($dato['idColegiado'])) {
                            $apellidoNombre = trim($dato['apellido']).' '.trim($dato['nombre']);
                            $referencia = $dato['matricula'];
                        } 
                        if (isset($dato['idAsistente'])) {
                            $apellidoNombre = trim($dato['asistente']);
                            $referencia = $dato['idAsistente'];
                        } 
                        $fechaPago = cambiarFechaFormatoParaMostrar($dato['fechaPago']);
                        $importe = $dato['importe'];
                        $recargo = $dato['recargo'];
                        $recibo = $dato['recibo'];
                        $tipoPago = $dato['tipoPago'].' ('.obtenrTipoPago($dato['detalleTipoPago']).')';
                        $cuotaAbonada = "";
                        if (isset($dato['periodo']) && $dato['periodo'] > 0) {
                            $cuotaAbonada = trim($dato['periodo']);
                        }
                        if (isset($dato['cuota']) && $dato['cuota'] >= 0) {
                            if ($cuotaAbonada <> "") {
                                $cuotaAbonada .= '/';
                            }
                            $cuotaAbonada .= rellenarCeros($dato['cuota'], 2);
                        }
                        //$detalleTipoPago = $dato['detalleTipoPago'];

                      ?>
                    <tr>
                	   <td><?php echo $idCobranzaDetalle;?></td>
                       <td><?php echo $referencia;?></td>
                       <td><?php echo $apellidoNombre;?></td>
                       <td><?php echo $cuotaAbonada;?></td>
                       <td><?php echo $fechaPago;?></td>
                       <td><?php echo $importe;?></td>
                       <td><?php echo $recargo;?></td>
                       <td><?php echo $recibo;?></td>
                       <td><?php echo $tipoPago;?></td>
                       <!--<td style="text-align: center;">
                            <a href="#" 
                               class="btn btn-primary glyphicon glyphicon-pencil center-block btn-sm"></a>
                        </td>-->
                    </tr>
                  <?php
                  }
                  ?>              
    	       </tbody>
    	  </table>
        <?php
        } else {
            ?>  
            <div class="row">&nbsp;</div>
            <div class="<?php echo $resPagos['clase']; ?>" role="alert">
                <span class="<?php echo $resPagos['icono']; ?>" ></span>
                <span><strong><?php echo $resPagos['mensaje']; ?></strong></span>
            </div>
        <?php
        }    
        ?>
    </div>
    </div>
    </div>
    <?php
} else {
    ?>  
    <div class="row">&nbsp;</div>
    <div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" ></span>
        <span><strong>Ingreso incorrecto</strong></span>
    </div>
<?php
}
?>
<div class="col-md-3">
    <form id="formColegiado" name="formColegiado" method="POST" onSubmit="" action="cobranza_lotes.php">
        <button type="submit"  class="btn btn-info" >Volver a Lotes</button>
        <?php
        if (isset($idLugarPago)) { ?>
            <input type="hidden" name="idLugarPago" id="idLugarPago" value="<?php echo $idLugarPago; ?>">
        <?php
        }
        if (isset($anio)) { ?>
            <input type="hidden" name="anioCobranza" id="anioCobranza" value="<?php echo $anio; ?>">
        <?php
        }
        ?>
    </form>
</div>
<div class="row">&nbsp;</div>
<?php
require_once '../html/footer.php';