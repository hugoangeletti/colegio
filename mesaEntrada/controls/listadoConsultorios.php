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
        if($_POST['tipoConsultorio'] != "")
        {
            $tc = $_POST['tipoConsultorio'];
        if((isset($_POST['consultorio'])))
        {
            $okey = false;
            if($_POST['consultorio'] != "")
            {
                $nombreConsultorio = $_POST['consultorio'];
                $consultorios = obtenerConsultoriosPorCallePorTipo(trim($nombreConsultorio), $tc);
                $okey = true;
            }else
            {
                ?>
                <br>
                <span class="mensajeERROR">Se olvidó de cargar el nombre.</span>
                <br>
                <?php
            }
            if($okey)
            {
                if(!$consultorios)
                {
                    ?>
                    <br>
                    <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
                    <br>
                    <?php
                }
                else
                {
                    if($consultorios -> num_rows == 0)
                    {
                        ?>
                        <br>
                        <span class="mensajeWARNING">No se encontraron consultorios con ese nombre.</span>
                        <br>
                        <?php
                    }
                    else
                    {
                        ?>

                    <table>
                        <tr>
                            <td><b>Calle</b></td>
                            <td><b>Número</b></td>
                            <td><b>Nombre</b></td>
                            
                            <?php
                            while ($row = $consultorios -> fetch_assoc())
                              {
                                echo "<tr class='dbl_consultorio' id='".$row['IdConsultorio']."'>";
                                echo "<td><p>".utf8_encode($row['Calle'])."</p></td>";
                                echo "<td><p>".$row['Numero']."</p></td>";
                                echo "<td><p>".$row['Nombre']."</p></td>";
                                echo "</tr>";
                              }
                            ?>
                            
                        </tr>
                    </table>
<?php
    if(isset($_GET['mC']))
    {
        if($_GET['mC'] == "bus")
        {
            $param = "";
        }
        else
        {
            $param = "&action=A&matricula=".$_GET['mC'];
        }
    }
    else
    {
        $param = "";
    }
?>
<script type="text/javascript">
$(function(){
    $(".dbl_consultorio").dblclick(function(){
        //var post_url = "mesaEntradaFormConsultorio.php?idConsultorio="+$(this).attr("id")+"<?php echo $param ?>";
        <?php
            if(isset($_GET['mC'])&&($_GET['mC'] == "bus"))
            {
        ?>
        var post_url = "buscarPorConsultorio.php?idConsultorio="+$(this).attr("id")+"&BoM=ok";
        <?php
            }
            else
            {
        ?>
        var post_url = "filtroHabilitacion.php?idConsultorio="+$(this).attr("id")+"<?php echo $param ?>";
        <?php
            }
        ?>
        $.ajax({
            type: 'GET',
            url: post_url,
            success: function(msg) {
                <?php
                    if(isset($_GET['mC'])&&($_GET['mC'] == "bus"))
                    {
                ?>
                window.location.replace(post_url);
                <?php
                    }
                    else
                    {
                ?>
                                    
                $("#page-wrap").fadeOut(1, function(){
                    $("#page-wrap").html(msg).fadeIn(2);
                });
                <?php
                    }
                ?>
            }
        });
        $("#modalBuscarConsultorio").dialog("close");
    });
});
</script>
                        <?php
                    }
                }
            }
            
        }
    }
    }
?>
       