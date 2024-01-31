<?php
    // Se encarga de mostrar todo el listado de remitentes que se encuentran
    // en la BD, para que el usuario elija desde el popup.

    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
           
    if(isset($_POST))
    {
        if((isset($_POST['nombre'])))
        {
            $okey = false;
            if($_POST['nombre'] != "")
            {
                $nombre = $_POST['nombre'];
                $remitentes = obtenerRemitentesPorNombre($nombre);
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
                if(!$remitentes)
                {
                    ?>
                <br>
                <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
                <br>
                    <?php
                }
                else
                {
                    if($remitentes -> num_rows == 0)
                    {
                        ?>
                        <br>
                                <p class="mensajeWARNING">No se encontraron remitentes.<br>
                                    Para agregar uno nuevo oprima el botón Generar Nuevo Remitente.</p>
                        <br>
                        <?php
                    }
                    else
                    {
                        ?>

                    <table>
                        <tr>
                            <td><b>ID</b></td>
                            <td><b>Nombre</b></td>
                            
                            <?php
                            while ($row = $remitentes -> fetch_assoc())
                              {
                                echo "<tr class='dbl_remitente' id='".$row['id']."'>";
                                echo "<td><p>".$row['id']."</p></td>";
                                echo "<td><p>".utf8_encode($row['Nombre'])."</p></td>";
                                echo "</tr>";
                              }
                            ?>
                            
                        </tr>
                    </table>
<br><br>
<hr>
<br>
Si no encontró el remitente deseado, proceda a agregar uno nuevo.<br>
Cierre la ventana y oprima el botón Generar Nuevo Remitente.
<script type="text/javascript">
$(function(){
    $(".dbl_remitente").dblclick(function(){
        <?php
            if(isset($_GET['Bus']))
            {
        ?>
        var post_url = "buscarPorRemitente.php?BoM=ok&remitente="+$(this).attr("id");
        <?php
            }
            else
            {
        ?>
        var post_url = "mesaEntradaFormNota.php?action=A&remitente="+$(this).attr("id");
        <?php
            }
        ?>
        $.ajax({
            type: 'GET',
            url: post_url,
            success: function(msg) {
                <?php
                    if(isset($_GET['Bus']))
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
       