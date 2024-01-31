<?php
include_once 'head_config.php';
include_once '../dataAccess/funciones.php';
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
    <?php
    include_once 'encabezado.php';

    if (isset($_POST['fecha'])) {
        if ($_POST['fecha'] != '') {
            $fechaMuestra = $_POST['fecha'];
            $fecha = invertirFecha($fechaMuestra);
        } else {
            $fechaMuestra = date("d-m-Y");
            $fecha = date("Y-m-d");
        }
    } else {
        if (isset($_GET['fecha'])) {
            if ($_GET['fecha'] != "") {
                $fechaMuestra = $_GET['fecha'];
                $fecha = invertirFecha($fechaMuestra);
            }
        } else {
            $fechaMuestra = date("d-m-Y");
            $fecha = date("Y-m-d");
        }
    }
    ?>
    <div id="page-wrap" style="height: 680px">
        <div id="titulo">
            <h3>Listado de Mesa de Entrada - Fecha: <?php echo $fechaMuestra ?></h3>
        </div>
        <br>
        <div>
            <form id='formFecha' action="listaMesaEntrada.php" method="post" onsubmit="return verif_fecha('fecha');">
                Fecha: <input id='fecha' name="fecha" type="text" maxlength="10" minlength="10" value="<?php echo $fechaMuestra ?>" />
                <input type="submit" value="Buscar" /> Debe Ingresar la Fecha con este formato(dd-mm-aaaa)
            </form>
        </div>
        <br/><br/>
        <div id="tabs">
            <ul>
                <li><a href="mostrarListadoMesaEntrada.php?st=1&fecha=<?php echo $fecha ?>">Ver Todos</a></li>
                <li><a href="mostrarListadoMesaEntrada.php?st=2&fecha=<?php echo $fecha ?>">Movimientos Matriculares</a></li>
                <li><a href="mostrarListadoMesaEntrada.php?st=3&fecha=<?php echo $fecha ?>">Especialidades</a></li>
                <li><a href="mostrarListadoMesaEntrada.php?st=4&fecha=<?php echo $fecha ?>">Notas y Oficios</a></li>
                <li><a href="mostrarListadoMesaEntrada.php?st=5&fecha=<?php echo $fecha ?>">Habilitaciones de Consultorio</a></li>
                <li><a href="mostrarListadoMesaEntrada.php?st=6&fecha=<?php echo $fecha ?>">Matrícula J</a></li>
                <li><a href="mostrarListadoMesaEntrada.php?st=7&fecha=<?php echo $fecha ?>">Autoprescripción</a></li>
                <!--<li><a href="mostrarListadoMesaEntrada.php?st=9&fecha=<?php echo $fecha ?>">Denuncias</a></li>-->
            </ul>
        </div>
    </div>
<?php
include_once '../html/pie.html';
?>
</body>
</html>