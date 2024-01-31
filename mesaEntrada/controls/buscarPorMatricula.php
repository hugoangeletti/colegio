<?php
include_once 'head_config.php';
include_once '../dataAccess/funciones.php';

?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
<?php 
include_once 'encabezado.php';

if(isset($_POST['matricula']))
{
    if($_POST['matricula'] != '')
    {
        $matricula = $_POST['matricula'];
    }
}
else
{
    if(isset($_GET['matricula']))
    {
        if($_GET['matricula'] != "")
        {
            $matricula = $_GET['matricula'];
        }
    }
}
?>
<div id="page-wrap" >
    <div id="titulo">
        <h3>Búsqueda por Matrícula <?php if(isset($matricula)){echo "- Matrícula: ".$matricula;} ?></h3>
    </div>
    <br>
    <div>
        <?php 
        if(isset($_GET["BoM"]))
        {
            require_once 'mostrarColegiado.php';
        }
        else
        {
            $_GET['me'] = 6;
            require_once 'buscarColegiado.php';
        }
        ?>
        <!--
        <form id='formFecha' action="buscarPorMatricula.php" method="post" onsubmit="return verif_num('matricula');">
            Matrícula: <input id='matricula' name="matricula" type="text" value="<?php if(isset($matricula)){echo $matricula;} ?>" />
            <input type="submit" value="Buscar" />
        </form>
        -->
    </div>
    <br/><br/>
    <?php
        if(isset($error) && ($error)){
            $matricula = 0;
        }
        else
        {
    ?>
    <div id="tabs">
        <ul>
            <li><a href="mostrarListadoPorMatricula.php?st=1&matricula=<?php if(isset($matricula)){echo $matricula;} ?>">Ver Todos</a></li>
            <li><a href="mostrarListadoPorMatricula.php?st=2&matricula=<?php if(isset($matricula)){echo $matricula;} ?>">Movimientos Matriculares</a></li>
            <li><a href="mostrarListadoPorMatricula.php?st=3&matricula=<?php if(isset($matricula)){echo $matricula;} ?>">Especialidades</a></li>
            <li><a href="mostrarListadoPorMatricula.php?st=4&matricula=<?php if(isset($matricula)){echo $matricula;} ?>">Notas y Oficios</a></li>
            <li><a href="mostrarListadoPorMatricula.php?st=5&matricula=<?php if(isset($matricula)){echo $matricula;} ?>">Habilitaciones de Consultorio</a></li>
            <li><a href="mostrarListadoPorMatricula.php?st=9&matricula=<?php if(isset($matricula)){echo $matricula;} ?>">Denuncias</a></li>
        </ul>
    </div>
    <?php
        }
    ?>
</div>
<?php 
include_once '../html/pie.html';
?>
</body>
</html>