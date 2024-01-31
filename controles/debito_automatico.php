<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/debitoAutomaticoLogic.php');
?>
<script>
    $(document).ready(function () {
        $('#tablaOrdenada').DataTable({
            "iDisplayLength":25,
            "language": {
                "url": "../public/lang/esp.lang"
            },
            "order": [[ 0, "desc" ]],
            dom: 'T<"clear">lfrtip',
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
<div class="panel panel-info">
<div class="panel-heading"><h4>Listado de Envios generados</h4></div>
<div class="panel-body">
    <div class="row">
        <?php
        if (isset($_POST['tipoDebito']) && $_POST['tipoDebito'] != ""){
            $tipoDebito = $_POST['tipoDebito'];
        } else {
            $tipoDebito = '';
        }
        if (isset($_POST['anio']) && $_POST['anio'] != ""){
            $anio = $_POST['anio'];
        } else {
            $anio = date('Y');
        }
        ?>
        <div class="row">
            <div class="col-xs-6">
                <form method="POST" action="debito_automatico.php">
                    <div class="col-xs-3">
                        <select class="form-control" id="anio" name="anio" required onChange="this.form.submit()">
                            <option value="0" selected>Todos</option>
                            <?php
                            $anioDebito = date('Y');
                            while ($anioDebito >= 2007) {
                            ?>
                                <option value="<?php echo $anioDebito; ?>" <?php if($anioDebito == $anio) { echo 'selected'; } ?>><?php echo $anioDebito; ?></option>
                            <?php
                                $anioDebito--;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <select class="form-control" id="tipoDebito" name="tipoDebito" required onChange="this.form.submit()">
                            <option value="" selected>Seleccion tipo de débito</option>
                            <option value="D" <?php if("D" == $tipoDebito) { echo 'selected'; } ?>>Tarjeta de Débito</option>
                            <option value="C" <?php if("C" == $tipoDebito) { echo 'selected'; } ?>>Tarjeta de Crédito</option>
                            <option value="H" <?php if("H" == $tipoDebito) { echo 'selected'; } ?>>CBU</option>
                        </select>
                    </div>
                    <div class="col-xs-3">&nbsp;</div>
                </form>    
            </div>
            <div class="col-xs-3"></div>
            <div class="col-xs-3">
                <?php 
                //si hay lote abierto no dejo generar uno nuevo
                if (!hayLoteDebitoAbierto($tipoDebito)) {
                ?>
                    <a href="debito_automatico_genera_archivo_form.php?tipo=<?php echo $tipoDebito; ?>" class="btn btn-primary">Generar lote de débito automático</a>
                <?php 
                }
                ?>
            </div>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <?php
    $resEnvios = obtenerDebitoGenerado($tipoDebito, $anio);
    if ($resEnvios['estado']){
    ?>
        <table id="tablaOrdenada" class="display">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Fecha generación</th>
                    <th>Fecha debito</th>
                    <th>Archivo</th>
                    <th>Carpeta</th>
                    <th>Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
              <?php
                  foreach ($resEnvios['datos'] as $dato) 
                  {
                      $idEnvioDebito = $dato['idEnvioDebito'];
                      $fechaEnvio = $dato['fechaEnvio'];
                      $fechaDebito = $dato['fechaDebito'];
                      $nombreArchivo = $dato['nombreArchivo'];
                      $pathArchivo = $dato['pathArchivo'];
                      $estado = $dato['estado'];
                      switch ($estado) {
                          case 'A':
                              $nombreEstado = 'Abierto';
                              break;
                          
                          case 'C':
                              $nombreEstado = 'Cerrado';
                              break;
                          
                          default:
                              $nombreEstado = 'Sin dato';
                              break;
                      }
                      $nombreEstado = $dato['nombreEstado'];
                  ?>
                    <tr>
                        <td><?php echo $idEnvioDebito;?></td>
                        <td><?php echo $fechaEnvio;?></td>
                        <td><?php echo cambiarFechaFormatoParaMostrar($fechaDebito);?></td>
                        <td><?php echo $nombreArchivo;?></td>
                        <td><?php echo $pathArchivo;?></td>
                        <td>
                            <?php 
                            if (isset($nombreArchivo) && $nombreArchivo <> "") {
                            ?>
                                <a href="<?php echo $pathArchivo.$nombreArchivo; ?>" class="btn btn-info btn-sm">Descargar</a>
                                <a href="debito_automatico_cerrar_archivo_form.php?tipo=<?php echo $tipoDebito; ?>" class="btn btn-default">Cerrar lote</a>
                                <a href="debito_automatico_borrar_archivo_form.php?tipo=<?php echo $tipoDebito; ?>" class="btn btn-default">Borrar lote</a>
                            <?php 
                            }
                            ?>
                        </td>
                   </tr>
                  <?php
                  }
              ?>
	   </tbody>
	  </table>
    <?php
    } else {
      ?>
        <div class="<?php echo $resEnvios['clase']; ?>" role="alert">
            <span class="<?php echo $resEnvios['icono']; ?>" aria-hidden="true"></span>
            <span><strong><?php echo $resEnvios['mensaje']; ?></strong></span>
        </div>
    <?php    
    }    
?>
</div>
</div>
<?php
require_once '../html/footer.php';