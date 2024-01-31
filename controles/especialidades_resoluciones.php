<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/resolucionesLogic.php');
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
<div class="panel-heading"><h4><b>Resoluciones de Especialistas</b></h4></div>
<div class="panel-body">
    <div class="row">
        <?php
    if (isset($_POST['estadoResoluciones']) && $_POST['estadoResoluciones'] != ""){
        $estadoResoluciones = $_POST['estadoResoluciones'];
    } else {
        $estadoResoluciones = 'A';
    }
    if (isset($_POST['anioResoluciones']) && $_POST['anioResoluciones'] != ""){
        $anioResoluciones = $_POST['anioResoluciones'];
    } else {
        $anioResoluciones = date('Y');
    }
    ?>
    <div class="row">
        <div class="col-xs-6">
            <form method="POST" action="especialidades_resoluciones.php">
                <div class="col-xs-3">
                    <select class="form-control" id="anioResoluciones" name="anioResoluciones" required onChange="this.form.submit()">
                        <?php
                        $anio = date('Y');
                        while ($anio >= 2007) {
                        ?>
                            <option value="<?php echo $anio; ?>" <?php if($anio == $anioResoluciones) { echo 'selected'; } ?>><?php echo $anio; ?></option>
                        <?php
                            $anio--;
                        }
                        ?>
                    </select>
                </div>
                <div class="col-xs-6">
                    <select class="form-control" id="estadoResoluciones" name="estadoResoluciones" required onChange="this.form.submit()">
                        <option value="A" <?php if($estadoResoluciones == "A") { echo 'selected'; } ?>>Abiertas</option>
                        <option value="E" <?php if($estadoResoluciones == "E") { echo 'selected'; } ?>>Enviadas a Consejo</option>
                        <option value="C" <?php if($estadoResoluciones == "C") { echo 'selected'; } ?>>Cerradas</option>
                    </select>
                </div>
                <div class="col-xs-3">&nbsp;</div>
            </form>    
        </div>
        <div class="col-xs-3"></div>
        <div class="col-xs-3">
            <?php
            if ($estadoResoluciones == 'A') {
            ?>
                <a href="especialidades_resoluciones_form.php?estado=<?php echo $estadoResoluciones; ?>&accion=1&anio=<?php echo $anioResoluciones ?>" class="btn btn-success btn-lg">
                    Nueva Resolución</a>
            <?php
            }
            ?>
        </div>
    </div>
    <?php
    $resResoluciones = obtenerResolucionesPorEstado($estadoResoluciones, $anioResoluciones);   
    if ($resResoluciones['estado']){
    ?>
        <br>
            <table id="tablaOrdenada" class="display">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Detalle</th>
                        <th>Tipo</th>
                        <th style="width: 30px">Editar</th>
                        <th style="width: 30px; text-align: center;">Ver Matrículas</th>
                        <th style="width: 30px; text-align: center;">Imprimir</th>
                        <th style="width: 30px; text-align: center;">Anexo</th>
                    </tr>
                </thead>
          <tbody>
              <?php
                  foreach ($resResoluciones['datos'] as $dato) 
                  {
                      $idResolucion = $dato['idResolucion'];
                      $detalle = $dato['detalle'];
                      $numero = $dato['numero'];
                      $fecha = cambiarFechaFormatoParaMostrar($dato['fecha']);
                      $tipo = $dato['detalleTipo'];
                  ?>
                    <tr>
                	<td><?php echo $idResolucion;?></td>
			         <td><?php echo $numero;?></td>
                        <td><?php echo $fecha;?></td>
                        <td><?php echo $detalle;?></td>
                        <td><?php echo $tipo;?></td>
                        <td style="text-align: center;">
                            <?php
                            if ($estadoResoluciones == 'A') {
                            ?>
                                <a href="especialidades_resoluciones_form.php?idResolucion=<?php echo $idResolucion; ?>&accion=3" 
                                   class="btn btn-primary glyphicon glyphicon-pencil center-block btn-sm"></a>
                            <?php
                            }
                            ?>
                        </td>
                        <td>
                            <div align="center">
                                <a href="especialidades_resoluciones_matriculas.php?idResolucion=<?php echo $idResolucion; ?>" 
                                   class="btn btn-info glyphicon glyphicon-book center-block btn-sm"></a>
                            </div>    
                        </td>
                        <td>
                            <?php
                            if ($estadoResoluciones <> "A") {
                            ?>
                            <div align="center">
                                <a href="especialidades_resoluciones_imprimir.php?idResolucion=<?php echo $idResolucion; ?>" target="_BLANK"
                                   class="btn btn-warning glyphicon glyphicon-print center-block btn-sm"></a>
                            </div>    
                            <?php
                            } else {
                            ?>
                            <div align="center">
                                <a href="especialidades_resoluciones_preview.php?idResolucion=<?php echo $idResolucion; ?>" target="_BLANK"
                                   class="btn btn-warning glyphicon glyphicon-print center-block btn-sm"></a>
                            </div>    
                            <?php
                            }
                            ?>
                        </td>
                        <td>
                            <div align="center">
                                <a href="especialidades_resoluciones_anexos.php?idResolucion=<?php echo $idResolucion; ?>&estado=<?php echo $estadoResoluciones; ?>&accion=1&anio=<?php echo $anioResoluciones ?>"
                                   class="btn btn-default glyphicon glyphicon-file center-block btn-sm"></a>
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