<?php
    require_once 'seguridad.php';
    $_SESSION['uno'] = array();
    $_SESSION['dos'] = array();
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/ordenDiaLogic.php';
    require_once '../dataAccess/funciones.php';
    
    $ordenes = obtenerOrdenes();

?>
<script type="text/javascript">
$(function(){
    $(".editar").click(function(){
        var href = $(this).attr("id");
        $("#page-wrap").load(href); 
    });
});
</script>
<table class='tablaTabs'>
    <tr>
        <td><h4>Nº de Orden</h4></td>
        <td><h4>Fecha de Reunión</h4></td>
        <td><h4>Período</h4></td>
        <td><h4>Editar</h4></td>
        <td><h4>Borrar</h4></td>
        <td><h4>Detalle</h4></td>
    </tr>
        <?php
        if(!$ordenes)
        {
            ?>
        <tr>
            <td colspan="6"><span class="mensajeERROR">Hubo un error en la base de datos.</span></td>
        </tr>
            <?php
        }
        else
        {
            if($ordenes -> num_rows == 0)
            {
                ?>
            <tr>
                <td colspan="6"><span class="mensajeWARNING">No se registran órdenes en el sistema.</span></td>
            </tr>
                <?php
            }
            else
            {
                while($orden = $ordenes -> fetch_assoc())
                {
                ?>
                <tr>
                    <td><?php echo $orden['Numero'] ?></td>
                    <td><?php echo invertirFecha($orden['Fecha']) ?></td>
                    <td><?php echo $orden['Periodo'] ?></td>
                    <td><a class="editar" id='ordenDiaFormOrden.php?action=M&iOrden=<?php echo $orden['Id'] ?>'>Editar</a></td>
                    <td><a class="editar" id='ordenDiaFormOrden.php?action=B&iOrden=<?php echo $orden['Id'] ?>'>Borrar</a></td>
                    <td><a class="editar" id="ordenDiaDetalle.php?iOrden=<?php echo $orden['Id'] ?>">Detalle</a></td>
                <?php
                        }
                ?>
                </tr>
                <?php
            }
        }
        ?>
</table>
<br />
