<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoCargoLogic.php');
?>
        <script>
            $(document).ready(function () {
                $('#tablaOrdenada').DataTable({
                    "iDisplayLength":50,
                    "language": {
                        "url": "../public/lang/esp.lang"
                    },
                    dom: 'T<"clear">lfrtip',
                    tableTools: {
                       "sSwfPath": "../public/swf/copy_csv_xls_pdf.swf", 
                       "aButtons": [
                            {
                                "sExtends": "pdf",
                                "mColumns" : [0, 1, 2, 3],
//                                "oSelectorOpts": {
//                                    page: 'current'
//                                }
                                "sTitle": "Listado de consejero",
                                "sPdfOrientation": "portrait",
                                "sFileName": "listado_de_consejeros.pdf"
//                              "sPdfOrientation": "landscape",
//                              "sPdfSize": "letter",  ('A[3-4]', 'letter', 'legal' or 'tabloid')
                            }
                            
                    ]
                    }
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
<div class="panel-heading"><h4><b>Listado de Consejero</b></h4></div>
<div class="panel-body">
    <div class="row">
        <div class="col-xs-6">
            <form id="formImprimir" name="formImprimir" method="POST" onSubmit="" action="consejero_imprimir.php">
                <button type="submit"  class="btn btn-info " >Imprimir lista</button>
            </form>
        </div>
        <div class="col-xs-3"></div>
        <div class="col-xs-3">
            <form method="POST" action="consejero_form.php">
                <div align="right">
                <button type="submit" class="btn btn-success btn-lg">Nuevo Consejero</button>
                <input type="hidden" id="accion" name="accion" value="1">
                </div>
            </form>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <?php
    $resConsejeros = obtenerConsejeros();
    if ($resConsejeros['estado']){
    ?>
        <table id="tablaOrdenada" class="display">
            <thead>
                <tr>
                    <th>Apellido y Nombres</th>
                    <th>Cargo</th>
                    <th>Fecha Desde</th>
                    <th>Fecha Hasta</th>
                    <th>
                        Editar
                    </th>
                    <th>
                        Baja
                    </th>
                </tr>
            </thead>
            <tbody>
              <?php
                  foreach ($resConsejeros['datos'] as $dato) 
                  {
                      $idColegiado = $dato['idColegiado'];
                      $idColegiadoCargo = $dato['idColegiadoCargo'];
                      $apellido = $dato['apellido'];
                      $nombre = $dato['nombre'];
                      $nombreCargo = $dato['nombreCargo'];
                      $fechaDesde = $dato['fechaDesde'];
                      $fechaHasta = $dato['fechaHasta'];
                  ?>
                    <tr>
                        <td><?php echo $apellido.' '.$nombre;?></td>
                        <td><?php echo $nombreCargo;?></td>
                        <td><?php echo cambiarFechaFormatoParaMostrar($fechaHasta);?></td>
                        <td><?php echo cambiarFechaFormatoParaMostrar($fechaHasta);?></td>
                        <td>
                            <div align="center">
                                <form method="POST" action="consejero_form.php?accion=3&idColegiadoCargo=<?php echo $idColegiadoCargo; ?>">
                                    <button type="submit" class="btn btn-info glyphicon glyphicon-erase center-block btn-sm"></button>
                                </form>
                            </div>    
                        </td>
                        <td>
                            <div align="center">
                                <form method="POST" action="consejero_form.php?accion=2&idColegiadoCargo=<?php echo $idColegiadoCargo; ?>">
                                    <button type="submit" class="btn btn-danger glyphicon glyphicon-erase center-block btn-sm"></button>
                                </form>
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
      ?>
        <div class="<?php echo $resConsejeros['clase']; ?>" role="alert">
            <span class="<?php echo $resConsejeros['icono']; ?>" aria-hidden="true"></span>
            <span><strong><?php echo $resConsejeros['mensaje']; ?></strong></span>
        </div>
    <?php    
    }    
?>
</div>
</div>
<?php
require_once '../html/footer.php';