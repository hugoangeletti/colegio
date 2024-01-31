<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/eleccionesLogic.php');
require_once ('../dataAccess/eleccionesLocalidadesLogic.php');
?>
        <script>
            $(document).ready(function () {
                $('#tablaOrdenada').DataTable({
                    "iDisplayLength":10,
                    "language": {
                        "url": "../public/lang/esp.lang"
                    },
                    dom: 'T<"clear">lfrtip',
                    tableTools: {
                       "sSwfPath": "../public/swf/copy_csv_xls_pdf.swf", 
                       "aButtons": [
                            {
                                "sExtends": "pdf",
                                "mColumns" : [0, 1, 2, 3, 4],
//                                "oSelectorOpts": {
//                                    page: 'current'
//                                }
                                "sTitle": "Listado de Elecciones",
                                "sPdfOrientation": "portrait",
                                "sFileName": "listado_de_elecciones.pdf"
//                              "sPdfOrientation": "landscape",
//                              "sPdfSize": "letter",  ('A[3-4]', 'letter', 'legal' or 'tabloid')
                            }
                            
                    ]
                    }
                });
            });
            
   
</script>

<?php
$continua = TRUE;
$estadoElecciones = $_POST['estadoElecciones'];
if (isset($_POST['idElecciones'])){
    $idElecciones = $_POST['idElecciones'];
    $resElecciones = obtenerEleccionesPorId($idElecciones);
    if ($resElecciones['estado']) {
        $elecciones = $resElecciones['datos'];
        $tituloElecciones = $elecciones['detalle'];
    }
} else {
    $continua = FALSE;
}

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
<div class="panel-heading"><h4><b><?php echo $tituloElecciones; ?> - Localidades</b></h4></div>
<div class="panel-body">
    <div class="row">
        <div class="col-xs-9">&nbsp;</div>
        <div class="col-xs-3">
            <form method="POST" action="elecciones_localidades_form.php">
                <div align="right">
                    <button type="submit" class="btn btn-success">Nueva Localidad</button>
                    <input type="hidden" id="estadoElecciones" name="estadoElecciones" value="<?php echo $estadoElecciones; ?>">
                    <input type="hidden" id="idElecciones" name="idElecciones" value="<?php echo $idElecciones; ?>">
                    <input type="hidden" id="accion" name="accion" value="1">
                </div>
            </form>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <?php
    $resElecciones = obtenerLocalidadesPorIdElecciones($idElecciones);   
    //var_dump($facturas);
    if ($resElecciones['estado']){
    ?>
            <table id="tablaOrdenada" class="display">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Codigo Localidad</th>
                        <th>Localidad</th>
                        <th>Delegados</th>
                        <th>Electores</th>
                        <th>Válidos</th>
                        <th>Anulados</th>
                        <th>En Blanco</th>
                        <th>Cociente</th>
                        <th style="width: 30px">Editar</th>
                    </tr>
                </thead>
          <tbody>
              <?php
                  foreach ($resElecciones['datos'] as $dato) 
                  {
                      $idEleccionesLocalidad = $dato['idEleccionesLocalidad'];
                      $codLocalidad = $dato['codigoLocalidad'];
                      $cantDelegados = $dato['cantDelegados'];
                      $cantElectores = $dato['cantElectores'];
                      $cantValidos = $dato['cantValidos'];
                      $cantAnulados = $dato['cantAnulados'];
                      $cantEnBlanco = $dato['cantEnBlanco'];
                      $cociente = $dato['cociente'];
                      $localidadDetalle = $dato['localidadDetalle'];
                  ?>
                    <tr>
                	<td><?php echo $idEleccionesLocalidad;?></td>
			<td><?php echo $codLocalidad;?></td>
			<td><?php echo $localidadDetalle;?></td>
			<td><?php echo $cantDelegados;?></td>
			<td><?php echo $cantElectores;?></td>
			<td><?php echo $cantValidos;?></td>
			<td><?php echo $cantAnulados;?></td>
			<td><?php echo $cantEnBlanco;?></td>
			<td><?php echo $cociente;?></td>
                        <td>
                            <div align="center">
                                <form method="POST" action="elecciones_localidades_form.php">
                                    <button type="submit" class="btn btn-primary glyphicon glyphicon-pencil center-block btn-sm"></button>
                                    <input type="hidden" id="accion" name="accion" value="3">
                                    <input type="hidden" id="idEleccionesLocalidad" name="idEleccionesLocalidad" value="<?php echo $idEleccionesLocalidad; ?>">
                                    <input type="hidden" id="idElecciones" name="idElecciones" value="<?php echo $idElecciones; ?>">
                                    <input type="hidden" id="estadoElecciones" name="estadoElecciones" value="<?php echo $estadoElecciones; ?>">
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
    <div class="<?php echo $resElecciones['clase']; ?>" role="alert">
        <span class="<?php echo $resElecciones['icono']; ?>" ></span>
        <span><strong><?php echo $resElecciones['mensaje']; ?></strong></span>
    </div>
<?php
}    
?>
</div>
</div>
<div class="col-md-12">            
    <form id="formVolver" name="formVolver" method="POST" onSubmit="" action="elecciones_lista.php">
        <button type="submit"  class="btn btn-success" >Volver </button>
        <input type="hidden" id="estadoElecciones" name="estadoElecciones" value="<?php echo $estadoElecciones; ?>">
    </form>
</div>
<div class="row">&nbsp;</div>
<?php
require_once '../html/footer.php';