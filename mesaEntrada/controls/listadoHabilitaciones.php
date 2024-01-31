<?php
require_once 'seguridad.php';

require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    /*
     * S -> Habilitaciones Solicitadas
     * A -> Habilitaciones Asignadas
     * C -> Habilitaciones Confirmadas
     */
    if(isset($_GET['idIns']))
    {
        if(isset($_GET['lH']))
        {
            switch ($_GET['lH'])
            {
                case "S":
                        $titulo = "Asignar Inspecciones";
                    break;
                case "A":
                        $titulo = "Habilitaciones Asignadas";
                    break;
                case "C":
                        $titulo = "Habilitaciones Confirmadas";
                    break;
            }
        }
    
        $consultaColegiado = obtenerColegiadoPorIdInspector($_GET['idIns']);
        $colegiado = $consultaColegiado -> fetch_assoc();
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
<!-- <script type="text/javascript">
$(function() {
    $( "#inspector" ).autocomplete({ minLength: 3 });
    $( "#inspector" ).autocomplete({
        source: "buscarAutorizados.php",
        select: function(event, ui){
            $("#ocultoInspector").load("colegiadoAutorizado.php?matricula="+ui.item.value);
            <?php
            /*
            if(isset($_GET['lH']))
            {
                switch ($_GET['lH'])
                {
                    case "S":
                        ?>
                            $(".tablaHabilitaciones").load("tablaHabilitaciones.php?lH=S&matricula="+ui.item.value);
                        <?php
                        break;
                    case "A":
                        ?>
                            $(".tablaHabilitaciones").load("tablaHabilitaciones.php?lH=A&matricula="+ui.item.value);
                        <?php
                        break;
                    case "C":
                        ?>
                            $(".tablaHabilitaciones").load("tablaHabilitaciones.php?lH=C&matricula="+ui.item.value);
                        <?php
                        break;
                }
            }
             * 
             */
            ?>
            $(".volver").hide();
        }
    });
  });
</script> -->
    <br/>
    <div id="titulo">
        <h3><?php echo $titulo ?></h3>
    </div>
    <br /><br />
    <table>
        <tr>
            <td>Matrícula del Inspector Asignado: <input id="inspector" name="inspector" type="text" value="<?php echo $colegiado['Matricula'] ?>" readonly="readonly"/></td>
            <td><?php echo utf8_encode($colegiado['Apellido'])." ".utf8_encode($colegiado['Nombres']) ?></td>
        </tr>
    </table>
    <br />
    <?php
                require_once 'tablaHabilitaciones.php';
    ?>
    <!--<div class="tablaHabilitaciones"></div>
    <br><br>
    <div class="volver">
        <input type="button" onclick="location='administracion.php'" value="Cancelar" />
    </div>-->
</div>
<?php 
    }
?>