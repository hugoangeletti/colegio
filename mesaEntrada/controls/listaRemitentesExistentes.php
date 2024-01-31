<?php
    include_once 'head_config.php';
    require_once 'seguridad.php';
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/colegiadoLogic.php';
    require_once '../dataAccess/tipoMovimientoLogic.php';
    require_once '../dataAccess/estadoTesoreriaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
<?php 
include_once 'encabezado.php';
?>
<div id="page-wrap">
    <br/>
    <div id="titulo">
        <h3>Listado de Remitentes</h3>
    </div>
    <br /><br />
<script type="text/javascript" src="../js/jqFuncs.js"></script>
<script type="text/javascript">
$(function(){
    $(".generarRemitente").click(function(){
        $.ajax({
            url: "formRemitente.php",
            success: function(msg) {
                $("#modalGenerarRemitente").html(msg);
            }
        });
        $( "#modalGenerarRemitente" ).dialog({
            closeText: "cerrar",
            modal: true,
            width:900,
            maxHeight: 400,
            maxWidth:1000,
            resizable: true,
            title: "Alta de Remitente"
        });
    });
    
    $(".borrar").click(function(){
        if(confirm("¿Está seguro que desea dar de baja a este remitente?"))
        {
            var dataPost = $(this).attr("id");
            $.post("borrarRemitente.php", { idRemitente: dataPost }, function(data){
                alert(data);
                location.reload();
            });    
        }
    });
});
</script>
    <a class="generarRemitente">Generar Nuevo Remitente</a>
    <br><br>
    <?php
    $remitentes = obtenerRemitentes();
    
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

        <table id="tablaEstadisticas" class='tablaCentrada'>
            <tr>
                <td><h4>Id</h4></td>
                <td class='izquierda'><h4>Remitente</h4></td>
                <!--<td>Borrar</td>-->
            </tr>
                <?php
                while ($row = $remitentes -> fetch_assoc())
                {
                    ?>
                <tr>
                    <td><p><?php echo $row['id'] ?></p></td>
                    <td class='izquierda'><p><?php echo utf8_encode($row['Nombre']) ?></p></td>
                    <!--<td><a class="borrar" id='<?php echo $row['id'] ?>'>Borrar</a></td>-->
                </tr>
                    <?php

                }
                ?>
        </table>
    
    

<?php
        }
    }
?>

</div>
    <div id="modalGenerarRemitente" style="display:none"></div>
    
</div>
<?php 
include_once '../html/pie.html';
?>
</body>
</html>
