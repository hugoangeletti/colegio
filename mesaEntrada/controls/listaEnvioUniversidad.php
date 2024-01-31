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
        <h3>Listado de Envios Universidad</h3>
    </div>
    <br /><br />
<script type="text/javascript" src="../js/jqFuncs.js"></script>
<script type="text/javascript">
$(function(){
    $(".generarEnvioUniversidad").click(function(){
        $.ajax({
            url: "formEnvioUniversidad.php",
            success: function(msg) {
                $("#modalGenerarEnvioUniversidad").html(msg);
            }
        });
        $( "#modalGenerarEnvioUniversidad" ).dialog({
            closeText: "cerrar",
            modal: true,
            width:900,
            maxHeight: 400,
            maxWidth:1000,
            resizable: true,
            title: "Envío de Nuevos Matriculados a Universidades"
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

$(function () {
        $(".verDetalle").click(function () {
            var href = $(this).attr("id");
            $.ajax({
                url: href,
                success: function (msg) {
                    $("#modalVerDetalle").html(msg);
                }
            });
            $("#modalVerDetalle").dialog({
                closeText: "cerrar",
                modal: true,
                minWidth: 680,
                minHeight: 100,
                width: 880,
                maxHeight: 450,
                maxWidth: 1000,
                resizable: true,
                title: "Ver Detalle"
            });
        });
    });
    
    $(function () {
        console.log($(".selectUniversidades").change(function(){
            $(".selectUniversidades option:selected").each(function () {
                var id = $(this).val();
            
                location.replace("listaEnvioUniversidad.php?universidades="+id);

            });
        }));
    });
    
    $(document).ready(function() {
        $('.paginar').click(function() {
            var page = $(this).attr('data');
            var univ = $(".selectUniversidades option:selected").val();
            
            location.replace("listaEnvioUniversidad.php?unversidades="+univ+"&page="+page);
        });
    });
</script>
<style>
    .loading
    {
        position: fixed; 
        top: 30%; 
        left: 40%; 
        z-index: 5000; 
        width: 422px; 
        height: 120px;
        text-align: center; 
        background: white;
        border: 1px solid #000;
        //vertical-align: middle;
        padding-top: 5%;
    }
    
/*
----------------------- P A G I N A C I O N --------------------------------
*/
                                        
.paginacion {
    height: 36px;
    margin: 15px 0;
	
}
.paginacion p {
    border-radius: 3px 3px 3px 3px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    display: inline-block;
    margin-bottom: 10px;
	//margin-left:92px;
	box-shadow: 0 2px 5px #666666;
}
.paginacion p {
    display: inline;
}
.paginacion a {
    -moz-border-bottom-colors: none;
    -moz-border-image: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    border-color: #DDDDDD;
    border-style: solid;
    border-width: 1px 1px 1px 0;
    float: left;
    line-height: 34px;
    padding: 0 14px;
    text-decoration: none;
	cursor:pointer;
}
.paginacion a:hover, .paginacion .activado a {
    color:#2d2d28;
	outline:0;
	text-decoration:none;
	font-weight: bold;
}
.paginacion .activado a {
    color: #999999;
    cursor: default;
}
.paginacion .disabled a, .paginacion .disabled a:hover {
    background-color: transparent;
    color: #999999;
    cursor: default;
}
.paginacion p:first-child a {
    border-left-width: 1px;
    border-radius: 3px 0 0 3px;
}
.pagination p:last-child a {
    border-radius: 0 3px 3px 0;
}


</style>
<div style="float: left;">
    <a class="generarEnvioUniversidad">Generar Nuevo Envío</a>
</div>
<?php
    $univs = obtenerUniversidades();
    
    if(isset($_GET['universidades']) && (!in_array($_GET['universidades'], ['', '-'])))
    {
        $universidad = $_GET['universidades'];
    }
    else
    {
        $universidad = '-';
    }

?>
<div style="float: right;">
    <select name="universidades" class="selectUniversidades">
        <option value="-">TODAS LAS UNIVERSIDADES</option>
        <?php
            if(($univs) && ($univs->num_rows != 0))
            {
                while ($row = $univs -> fetch_assoc())
                {
        ?>
        <option value="<?php echo $row['Id'] ?>" <?php if($row['Id'] == $universidad){echo "selected";} ?>><?php echo utf8_encode($row['Nombre']) ?></option>
        <?php
                }
            }
        ?>
    </select>
</div>
    
    
    <br><br>
    <?php
    //numero de registros por página
    $porPagina = 20;

    //por defecto mostramos la página 1
    $numPagina = 1;
    
    if (isset($_GET['page']) && ($_GET['page'] != "1")) {
        $textPage = "&page=" . $_GET['page'];
        $numPagina = $_GET['page'];
    } else {
        $textPage = "";
    }
    
    //contando el desplazamiento
    $offset = ($numPagina - 1) * $porPagina;
    
    $envios_universidad = obtenerEnviosUniversidad($universidad, $offset, $porPagina);
    
    if(!$envios_universidad)
    {
        ?>
    <br>
    <span class="mensajeERROR">Hubo un error. Vuelva a intentar.</span>
    <br>
        <?php
    }
    else
    {
        if($envios_universidad -> num_rows == 0)
        {
            ?>
    <br>
            <p class="mensajeWARNING">No se encontraron envíos para esta/s universidad/es.<br>
                Para agregar uno nuevo oprima el botón Generar Nuevo Envío.</p>
    <br>
            <?php
        }
        else
        {
             ?>

        <table id="tablaEstadisticas" class='tablaCentrada'>
            <tr>
                <td><h4>Id</h4></td>
                <td class='izquierda'><h4>Universidad</h4></td>
                <td><h4>Fecha Desde</h4></td>
                <td><h4>Fecha Hasta</h4></td>
                <td><h4>Envío</h4></td>
                <td><h4>PDF</h4></td>
                <td><h4>Colegiados</h4></td>
            </tr>
                <?php
                while ($row = $envios_universidad -> fetch_assoc())
                {
                    ?>
                <tr>
                    <td><p><?php echo $row['Id'] ?></p></td>
                    <td class='izquierda'><p><?php echo utf8_encode($row['NombreUniversidad']) ?></p></td>
                    <td><p><?php echo invertirFecha($row['FechaDesde']) ?></p></td>
                    <td><p><?php echo invertirFecha($row['FechaHasta']) ?></p></td>
                    <td><p><?php if($row['Envio'] == 'S'){echo invertirFecha($row['FechaCarga']);}elseif($row['Envio'] == 'N'){echo "NO";} ?></p></td>
                    <td>
                    <?php
                        if(!is_null($row['Pdf']) && ($row['Pdf'] != ""))
                        {
                    ?>
                        <a target="_blank" href="../../envio-universidad/<?php echo $row['Pdf'] ?>">Descargar PDF</a>
                    <?php
                        }
                    ?>
                    </td>
                    <td><a class="verDetalle" id="listaEnvioUniversidadColegiados.php?idEU=<?php echo $row['Id']?>">Ver colegiados</a></td>
                </tr>
                    <?php

                }
                ?>
        </table>
        <?php
        
            $totEnviosUniversidad = obtenerEnviosUniversidad($universidad);
            
            if($totEnviosUniversidad)
            {
                $cantPaginas = ceil($totEnviosUniversidad -> num_rows / $porPagina);
            }
            else
            {
                $cantPaginas = 0;
            }
            
            if ($cantPaginas > 1) {
                ?>
                <div class="paginacion">
                    <?php
                    if ($numPagina != 1) {
                        ?>
                        <p><a class="paginar" data="<?php echo ($numPagina - 1); ?>">Anterior</a></p>
                        <?php
                    }
                    for ($i = 1; $i <= $cantPaginas; $i++) {
                        if ($numPagina == $i) {
                            //si muestro el índice de la página actual, no coloco enlace
                            ?>
                            <p class="activado"><a><?php echo $i; ?></a></p>
                            <?php
                        } else {
                            //si el índice no corresponde con la página mostrada actualmente,
                            //coloco el enlace para ir a esa página
                            ?>
                            <p><a class="paginar" data="<?php echo $i; ?>"><?php echo $i; ?></a></p>
                            <?php
                        }
                    }
                    if ($numPagina != $cantPaginas) {
                        ?>
                        <p><a class="paginar" data="<?php echo ($numPagina + 1); ?>">Siguiente</a></p>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        ?>
    

<?php
        }
    }
?>

</div>
    <div id="modalGenerarEnvioUniversidad" style="display:none"></div>
    <div class="loading" style="display: none;">
        <img src="../images/ajax-loader.gif">
    </div>
    <div id="modalVerDetalle" style="display: none;"></div>
</div>
<?php 
include_once '../html/pie.html';
?>
</body>
</html>
