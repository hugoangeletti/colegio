<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
require_once ('../html/head.php');
require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/cursosLogic.php');
require_once ('../dataAccess/funcionesPhp.php');
?>
        <script>
            $(document).ready(function () {
                $('#tablaOrdenada').DataTable({
                    "iDisplayLength":25,
                    "language": {
                        "url": "../public/lang/esp.lang"
                    },
                    dom: 'T<"clear">lfrtip',
                    tableTools: {
                       "sSwfPath": "../public/swf/copy_csv_xls_pdf.swf", 
                       "aButtons": [
                            {
                                "sExtends": "pdf",
                                "mColumns" : [0, 1],
//                                "oSelectorOpts": {
//                                    page: 'current'
//                                }
                                "sTitle": "Listado de cursos",
                                "sPdfOrientation": "portrait",
                                "sFileName": "listado_de_cursos.pdf"
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
<div class="panel-heading"><h4><b>Cursos</b></h4></div>
<div class="panel-body">
    <?php
    if (isset($_POST['estadoCursos']) && $_POST['estadoCursos'] != ""){
        $estadoCursos = $_POST['estadoCursos'];
    } else {
        $estadoCursos = 'A';
    }
    ?>
    <div class="row">
        <div class="col-xs-6">
            <form method="POST" action="cursos_lista.php">
                <div class="col-xs-6">
                    <select class="form-control" id="estadoCursos" name="estadoCursos" required onChange="this.form.submit()">
                        <option value="A" <?php if($estadoCursos == "A") { echo 'selected'; } ?>>Activos</option>
                        <option value="F" <?php if($estadoCursos == "F") { echo 'selected'; } ?>>Finalizados</option>
                    </select>
                </div>
            </form>    
        </div>
        <div class="col-xs-3"></div>
        <div class="col-xs-3">
            <form method="POST" action="cursos_form.php">
                <div align="right">
                <button type="submit" class="btn btn-success btn-lg">Nuevo Curso</button>
                <input type="hidden" id="accion" name="accion" value="1">
                <input type="hidden" id="estadoCursos" name="estadoCursos" value="<?php echo $estadoCursos; ?>">
                </div>
            </form>
        </div>
    </div>
    <?php
    $resCursos = obtenerCursosPorEstado($estadoCursos);
    //var_dump($facturas);
    if ($resCursos['estado']){
    ?>
        <br>
        <?php
        if (sizeof($resCursos['datos'])>0){
        ?>    
            <table id="tablaOrdenada" class="display">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>T&iacute;tulo</th>
                        <th style="width: 30px">Editar</th>
                        <th style="width: 30px">Cuotas</th>
                        <th style="width: 30px">Asistentes</th>
                    </tr>
                </thead>
          <tbody>
              <?php
                  foreach ($resCursos['datos'] as $dato) 
                  {
                      $idCurso = $dato['idCurso'];
                      $titulo = ($dato['titulo']);
                      
                  ?>
                    <tr>
                	<td><?php echo $idCurso;?></td>
			<td><?php echo $titulo;?></td>
                        <td>
                            <div align="center">
                                <form method="POST" action="cursos_form.php">
                                    <button type="submit" class="btn btn-primary glyphicon glyphicon-pencil center-block btn-sm"></button>
                                    <input type="hidden" id="accion" name="accion" value="3">
                                    <input type="hidden" id="idCurso" name="idCurso" value="<?php echo $idCurso; ?>">
                                    <input type="hidden" id="estadoCursos" name="estadoCursos" value="<?php echo $estadoCursos; ?>">
                                </form>
                            </div>    
                        </td>
                        <td>
                            <div align="center">
                                <form method="POST" action="cursosCuotas_lista.php">
                                    <button type="submit" class="btn btn-danger glyphicon glyphicon-book center-block btn-sm"></button>
                                    <input type="hidden" id="idCurso" name="idCurso" value="<?php echo $idCurso; ?>">
                                    <input type="hidden" id="estadoCursos" name="estadoCursos" value="<?php echo $estadoCursos; ?>">
                                </form>
                            </div>    
                        </td>
                        <td>
                            <div align="center">
                                <form method="POST" action="cursosAsistentes_lista.php">
                                    <button type="submit" class="btn btn-danger glyphicon glyphicon-book center-block btn-sm"></button>
                                    <input type="hidden" id="idCurso" name="idCurso" value="<?php echo $idCurso; ?>">
                                    <input type="hidden" id="estadoCursos" name="estadoCursos" value="<?php echo $estadoCursos; ?>">
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
        <div class="<?php echo $resCursos['clase']; ?>" role="alert">
            <span class="<?php echo $resCursos['icono']; ?>" aria-hidden="true"></span>
            <span><strong><?php echo $resCursos['mensaje']; ?></strong></span>
        </div>
    <?php    
    }    
} else {
?>
    <div class="<?php echo $resCursos['clase']; ?>" role="alert">
        <span class="<?php echo $resCursos['icono']; ?>" aria-hidden="true"></span>
        <span><strong><?php echo $resCursos['mensaje']; ?></strong></span>
    </div>
<?php
}    
?>
</div>
</div>
<?php
require_once '../html/footer.php';