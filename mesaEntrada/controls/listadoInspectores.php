<?php
    // Se encarga de mostrar todo el listado de remitentes que se encuentran
    // en la BD, para que el usuario elija desde el popup.

    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';
    
    if(isset($_POST))
    {
        $okey = false;
        if($_POST['matricula'] != "")
        {
            $matricula = $_POST['matricula'];
            $lH = $_POST['lH'];
            if ($lH == 'M') {
                $matriculado = obtenerColegiadoPorMatricula($matricula);
            } else {
                $inspectores = obtenerInspectoresParteMatricula($matricula);
            }
            $okey = true;
        }else
        {
            ?>
                <br>
                <span class="mensajeERROR">Se olvidó de cargar la matrícula.</span>
                <br>
            <?php
        }
        if($okey)
            {
            if ($lH != 'M') {
                if(!$inspectores)
                {
                    ?>
                <br>
                <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
                <br>
                    <?php
                }
                else
                {
                    if($inspectores -> num_rows == 0)
                    {
                        ?>
                        <br>
                            <p class="mensajeWARNING">No se encontraron inspectores con esa matrícula.<br>
                                Para agregar uno nuevo oprima el botón Generar Nuevo Inspector.</p>
                        <br>
                        <?php
                    }
                    else
                    {
                        ?>

                    <table>
                        <tr>
                            <td><b>Matrícula</b></td>
                            <td><b>Apellido y Nombre</b></td>
                            <?php
                            while ($row = $inspectores -> fetch_assoc())
                              {
                                echo "<tr class='dbl_inspector' id='".$row['IdInspector']."'>";
                                echo "<td><p>".$row['Matricula']."</p></td>";
                                echo "<td><p>".utf8_encode($row['Apellido'])." ".utf8_encode($row['Nombres'])."</p></td>";
                                echo "</tr>";
                              }
                            ?>
                            
                        </tr>
                    </table>
<hr>
<br>
Si no encontró el inspector deseado, proceda a agregar uno nuevo.<br>
Cierre la ventana y oprima el botón Generar Nuevo Inspector.
<script type="text/javascript">
$(function(){
    $(".dbl_inspector").dblclick(function(){
        var id = $(this).attr("id");
        var post_url = "listadoHabilitaciones.php?idIns="+id+"&lH=<?php echo $_POST['lH'] ?>";
        $.ajax({
            type: 'GET',
            url: post_url,
            success: function(msg) {
                $("#page-wrap").fadeOut(1, function(){
                    $("#page-wrap").html(msg).fadeIn(2);
                });
            }
        });
        $("#modal").dialog("close");
    });
});
</script>
                        <?php
                    }
                }
            }
    }
    }
?>
       