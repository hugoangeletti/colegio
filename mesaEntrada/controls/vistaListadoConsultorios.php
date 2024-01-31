<?php
include_once 'head_config.php';
include_once '../dataAccess/funciones.php';
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
    <?php
    include_once 'encabezado.php';

    if (isset($_GET['calle']) && ($_GET['calle'] != "")) {
        $calle = $_GET['calle'];
    } else {
        $calle = "";
    }
    ?>
    <div id="page-wrap" style="height: 680px">
        <div id="titulo">
            <h3>Listado de Consultorios</h3>
        </div>
        <br>
        <div>
            <form action="vistaListadoConsultorios.php" method="get">
                Calle: <input name="calle" type="text" value="<?php echo $calle ?>" />
                <input type="submit" value="Buscar" />
                <a style="margin-left: 50px;" href="vistaListadoConsultorios.php?calle=">Limpiar Búsqueda</a>
            </form>
        </div>
        <?php
        if (isset($_GET['page']) && ($_GET['page'] != "1")) {
            $textPage = "&page=" . $_GET['page'];
        } else {
            $textPage = "";
        }
        ?>
        <br/><br/>
        <div id="tabs">
            <ul>
                <li><a href="vistaListadoConsultoriosMuestra.php?tipo=T<?php echo $textPage ?>&calle=<?php echo $calle ?>">Ver Todos</a></li>
                <li><a href="vistaListadoConsultoriosMuestra.php?tipo=U<?php echo $textPage ?>&calle=<?php echo $calle ?>">Únicos</a></li>
                <li><a href="vistaListadoConsultoriosMuestra.php?tipo=P<?php echo $textPage ?>&calle=<?php echo $calle ?>">Policonsultorios</a></li>
                <li><a href="vistaListadoConsultoriosMuestra.php?tipo=I<?php echo $textPage ?>&calle=<?php echo $calle ?>">Instituciones</a></li>
            </ul>
        </div>
    </div>
    <?php
    include_once '../html/pie.html';
    ?>
</body>
</html>