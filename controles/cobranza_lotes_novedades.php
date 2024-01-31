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
                    "order": [[ 0, "desc" ], [ 1, "asc"]],
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
if (isset($_GET['id']) && $_GET['id'] <> "" && isset($_GET['idLugarPago'])) {
    $idCobranza = $_GET['id'];
    $idLugarPago = $_GET['idLugarPago'];
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
        $resPagos = obtenerNovedadesLote($idCobranza);
        if ($resPagos['estado']){
        ?>
            <br>
            <table id="tablaOrdenada" class="display">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Matrícula</th>
                        <th>Apellido y Nombres</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($resPagos['datos'] as $dato) {
                        $idCobranzaNovedades = $dato['idCobranzaNovedades'];
                        $apellidoNombre = trim($dato['apellido']).' '.trim($dato['nombre']);
                        $matricula = $dato['matricula'];
                        $detalle = $dato['detalle'];
                      ?>
                    <tr>
                	   <td><?php echo $idCobranzaNovedades;?></td>
                       <td><?php echo $matricula;?></td>
                       <td><?php echo $apellidoNombre;?></td>
                       <td><?php echo $detalle;?></td>
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
        <input type="hidden" name="idLugarPago" id="idLugarPago" value="<?php echo $idLugarPago; ?>">
    </form>
</div>
<div class="row">&nbsp;</div>
<?php
require_once '../html/footer.php';