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
            "order": [[ 2, "desc" ], [ 1, "asc"]],
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
<div class="panel-heading"><h4><b>Lotes de Cobranza</b></h4></div>
<div class="panel-body">
    <div class="row">
        <?php
    if (isset($_POST['idLugarPago']) && $_POST['idLugarPago'] != ""){
        $idLugarPago = $_POST['idLugarPago'];
    } else {
        $idLugarPago = '';
    }
    if (isset($_POST['anioCobranza']) && $_POST['anioCobranza'] != ""){
        $anioCobranza = $_POST['anioCobranza'];
    } else {
        $anioCobranza = date('Y');
    }
    ?>
    <div class="row">
        <div class="col-xs-6">
            <form method="POST" action="cobranza_lotes.php">
                <div class="col-xs-3">
                    <select class="form-control" id="anioCobranza" name="anioCobranza" required onChange="this.form.submit()">
                        <option value="0" selected>Todos</option>
                        <?php
                        $anio = date('Y');
                        while ($anio >= 2007) {
                        ?>
                            <option value="<?php echo $anio; ?>" <?php if($anio == $anioCobranza) { echo 'selected'; } ?>><?php echo $anio; ?></option>
                        <?php
                            $anio--;
                        }
                        ?>
                    </select>
                </div>
                <div class="col-xs-6">
                    <select class="form-control" id="idLugarPago" name="idLugarPago" required onChange="this.form.submit()">
                        <option value="" selected>Todos</option>
                        <?php
                        $resLugares = obtenerLugaresDePago();
                        if ($resLugares['estado']) {
                            foreach ($resLugares['datos'] as $lugarPago) {
                        ?>
                            <option value="<?php echo $lugarPago['id']; ?>" <?php if($lugarPago['id'] == $idLugarPago) { echo 'selected'; } ?>><?php echo $lugarPago['nombre']; ?></option>
                        <?php
                            }
                        } 
                        ?>
                    </select>
                </div>
                <div class="col-xs-3">&nbsp;</div>
            </form>    
        </div>
        <div class="col-xs-3"></div>
        <div class="col-xs-3">
            <div class="btn-group">
              <button type="button" class="btn btn-success dropdown-toggle"
                      data-toggle="dropdown">
                Procesar lotes <span class="caret"></span>
              </button>

              <ul class="dropdown-menu" role="menu">
                <?php
                if (verificarRolUsuario($_SESSION['user_id'], 61)){
                ?>
                    <li><a href="cobranza_procesar_form.php?accion=1" >Por lugar de pago</a></li>
                    <li><a href="archivos_lotes_a_procesar.php" >Por archivos decargados</a></li>
                    <li><a href="#" >Por agremiaciones</a></li>
                <?php
                }
                //<li class="divider"></li>
                //<li><a href="#">Acción #4</a></li>
                ?>
              </ul>
            </div>
        </div>
    </div>
    <?php
    $resLotes = obtenerLotes($anioCobranza, $idLugarPago);   
    if ($resLotes['estado']){
    ?>
        <br>
            <table id="tablaOrdenada" class="display">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Lugar de Pago</th>
                        <th>Fecha del lote</th>
                        <th>Importe cobrado</th>
                        <th>Cantidad Comprobantes</th>
                        <th>Archivo</th>
                        <th>Observaciones</th>
                        <th>Estado</th>
                        <th style="width: 30px">Editar</th>
                        <th style="width: 30px; text-align: center;">Ver Pagos</th>
                    </tr>
                </thead>
          <tbody>
              <?php
              foreach ($resLotes['datos'] as $dato) {
                  $idCobranza = $dato['id'];
                  $nombreLugarPago = $dato['nombreLugarPago'];
                  //$fechaApertura = cambiarFechaFormatoParaMostrar($dato['fechaApertura']);
                  $fechaApertura = $dato['fechaApertura'];
                  $totalRecaudacion = $dato['totalRecaudacion'];
                  $cantidadComprobantes = $dato['cantidadComprobantes'];
                  $estadoCobranza = $dato['estado'];
                  $observaciones = $dato['observaciones'];
                  $nombreArchivo = $dato['archivo'];
                  if (isset($observaciones)) {
                    $observaciones = '<a href="cobranza_lotes_novedades.php?id='.$idCobranza.'&idLugarPago='.$idLugarPago.'">'.$observaciones.'</a>';
                  }
                  ?>
                <tr>
            	   <td><?php echo $idCobranza;?></td>
                   <td><?php echo $nombreLugarPago;?></td>
                   <td><?php echo $fechaApertura;?></td>
                   <td><?php echo $totalRecaudacion;?></td>
                   <td><?php echo $cantidadComprobantes;?></td>
                   <td><?php echo $nombreArchivo;?></td>
                   <td><?php echo $observaciones;?></td>
                   <td><?php echo $estadoCobranza;?></td>
                   <td style="text-align: center;">
                    <?php
                    if ($estadoCobranza == 'A') {
                    ?>
                        <a href="cobranza_lotes_form_form.php?id=<?php echo $idCobranza; ?>&accion=3" 
                           class="btn btn-primary glyphicon glyphicon-pencil center-block btn-sm"></a>
                    <?php
                    }
                    ?>
                    </td>
                    <td>
                        <div align="center">
                            <?php 
                            $id = $idCobranza.'_'.$anioCobranza;
                            if (isset($idLugarPago)) {
                                $id = $id.'_'.$idLugarPago;
                            }
                            ?>
                            <a href="cobranza_lotes_detalle.php?id=<?php echo $id; ?>" 
                               class="btn btn-info glyphicon glyphicon-book center-block btn-sm"></a>
                        </div>
                    </td>
                </tr>
              <?php
              }
              ?>              
	       </tbody>
	  </table>
    <?php
} else {
    ?>  <div class="row">&nbsp;</div>
    <div class="<?php echo $resResoluciones['clase']; ?>" role="alert">
        <span class="<?php echo $resResoluciones['icono']; ?>" ></span>
        <span><strong><?php echo $resResoluciones['mensaje']; ?></strong></span>
    </div>
<?php
}    
?>
</div>
</div>
</div>
<?php
require_once '../html/footer.php';