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
                    "iDisplayLength":100,
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

if (isset($_GET['consulta']) && $_GET['consulta'] == 'ok') {
    //ingresa por secretaria
    $soloConsulta = FALSE;
} else {
    $soloConsulta = TRUE;
}
?> 
<div class="panel panel-default">
<div class="panel-heading"><h4><b>Listado de Consejero</b></h4></div>
<div class="panel-body">
    <?php 
    if (!$soloConsulta) {
    ?>
    <div class="row">
        <div class="col-xs-2">
            <form id="formImprimir" name="formImprimir" method="POST" target="_BLANK" action="secretaria_consejeros_imprimir.php">
                <button type="submit"  class="btn btn-info " >Imprimir lista</button>
            </form>
        </div>
        <div class="col-xs-2">
            <form id="formImprimir" name="formImprimir" method="POST" target="_BLANK" action="secretaria_consejeros_imprimir_foto.php">
                <button type="submit"  class="btn btn-info " >Imprimir lista con foto</button>
            </form>
        </div>
        <div class="col-xs-3"></div>
        <div class="col-xs-3">
            <form method="POST" action="secretaria_consejeros_form.php">
                <div align="right">
                <button type="submit" class="btn btn-success btn-lg">Nuevo Consejero</button>
                <input type="hidden" id="accion" name="accion" value="1">
                </div>
            </form>
        </div>
    </div>
    <?php 
    }
    ?>
    <div class="row">&nbsp;</div>
    <?php
    $resConsejeros = obtenerConsejerosVigentes();
    if ($resConsejeros['estado']){
    ?>
        <table id="tablaOrdenada" class="display">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Apellido y Nombres</th>
                    <th>Domicilio</th>
                    <th>Localidad</th>
                    <th>Teléfonos</th>
                    <th>Email</th>
                    <th>Cargo</th>
                    <th>Fecha Desde</th>
                    <th>Fecha Hasta</th>
                    <?php 
                    if (!$soloConsulta) {
                    ?>
                    <th>Editar</th>
                    <?php
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $numeroOrden = 0;
                foreach ($resConsejeros['datos'] as $dato) {
                    $idColegiado = $dato['idColegiado'];
                    $idColegiadoCargo = $dato['idColegiadoCargo'];
                    $apellido = $dato['apellido'];
                    $nombre = $dato['nombre'];
                    $nombreCargo = $dato['nombreCargo'];
                    $fechaDesde = $dato['fechaDesde'];
                    $fechaHasta = $dato['fechaHasta'];
                    $domicilioCompleto = $dato['domicilioCompleto'];
                    $localidad = $dato['localidad'];
                    $telefonos = $dato['telefonos'];
                    $mail = $dato['mail'];
                    $numeroOrden++;
                    ?>
                    <tr>
                        <td><?php echo $numeroOrden;?></td>
                        <td><?php echo $apellido.' '.$nombre;?></td>
                        <td><?php echo $domicilioCompleto; ?></td>
                        <td><?php echo $localidad; ?></td>
                        <td><?php echo $telefonos; ?></td>
                        <td><?php echo $mail; ?></td>
                        <td><?php echo $nombreCargo;?></td>
                        <td><?php echo cambiarFechaFormatoParaMostrar($fechaDesde);?></td>
                        <td><?php echo cambiarFechaFormatoParaMostrar($fechaHasta);?></td>
                        <?php 
                        if (!$soloConsulta) {
                        ?>
                        <td>
                            <div align="center">
                                <form method="POST" action="secretaria_consejeros_form.php">
                                    <button type="submit" class="btn btn-info glyphicon glyphicon-erase center-block btn-sm"></button>
                                    <input type="hidden" id="idColegiadoCargo" name="idColegiadoCargo" value="<?php echo $idColegiadoCargo; ?>">
                                    <input type="hidden" id="accion" name="accion" value="3">
                                </form>
                            </div>    
                        </td>
                        <?php 
                        }
                        ?>
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