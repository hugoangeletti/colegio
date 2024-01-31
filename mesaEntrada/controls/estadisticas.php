<?php
include_once 'head_config.php';
include_once '../dataAccess/funciones.php';

?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
<?php 
include_once 'encabezado.php';

if(isset($_POST['fechaDesde']))
{
    if($_POST['fechaDesde'] != '')
    {
        $fechaMuestraDesde = $_POST['fechaDesde'];
        $fechaMuestraHasta = $_POST['fechaHasta'];
        $fechaDesde = invertirFecha($fechaMuestraDesde);
        $fechaHasta = invertirFecha($fechaMuestraHasta);
    }
    else
    {
        $fechaMuestraDesde = date("d-m-Y");
        $fechaDesde = date("Y-m-d");
        $fechaMuestraHasta = date("d-m-Y");
        $fechaHasta = date("Y-m-d");
    }
    
}
else
{
    if(isset($_GET['fechaDesde']))
    {
        if($_GET['fechaDesde'] != "")
        {
            $fechaMuestraDesde = $_GET['fechaDesde'];
            $fechaMuestraHasta = $_GET['fechaHasta'];
            $fechaDesde = invertirFecha($fechaMuestraDesde);
            $fechaHasta = invertirFecha($fechaMuestraHasta);
        }
    }
    else
    {
        $fechaMuestraDesde = date("d-m-Y");
        $fechaDesde = date("Y-m-d");
        $fechaMuestraHasta = date("d-m-Y");
        $fechaHasta = date("Y-m-d");
    }
}
?>
<div id="page-wrap">
    <div id="titulo">
        <h3>Estadísticas de Mesa de Entrada - Fechas: <?php echo $fechaMuestraDesde." - ".$fechaMuestraHasta ?></h3>
    </div>
    <br>
    <div>
        <form id='formFecha' action="estadisticas.php" method="post" onsubmit="return verif_desde_hasta('fechaDesde', 'fechaHasta');">
            Fecha Desde: <input id='fechaDesde' name="fechaDesde" type="text" maxlength="10" minlength="10" value="<?php echo $fechaMuestraDesde ?>" />
            Fecha Hasta: <input id='fechaHasta' name="fechaHasta" type="text" maxlength="10" minlength="10" value="<?php echo $fechaMuestraHasta ?>" />
            <input type="submit" value="Buscar" /> Debe Ingresar la Fecha con este formato(dd-mm-aaaa)
        </form>
    </div>
    <br/><br/>
    <?php
    require_once 'tablaEstadisticas.php';
    ?>
</div>
<?php 
include_once '../html/pie.html';
?>
</body>
</html>