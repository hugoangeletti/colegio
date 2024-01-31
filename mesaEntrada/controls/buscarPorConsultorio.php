<?php
include_once 'head_config.php';
include_once '../dataAccess/funciones.php';

?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
<?php 
include_once 'encabezado.php';

if(isset($_POST['consultorio']))
{
    if($_POST['consultorio'] != '')
    {
        $consultorio = $_POST['consultorio'];
    }
}
else
{
    if(isset($_GET['consultorio']))
    {
        if($_GET['consultorio'] != "")
        {
            $consultorio = $_GET['consultorio'];
        }
    }
}
?>
<div id="page-wrap">
    <div id="titulo">
        <h3>Búsqueda por Consultorio <?php if(isset($consultorio)){echo "- Matrícula: ".$consultorio;} ?></h3>
    </div>
    <br>
    <div>
        <?php 
        if(isset($_GET["BoM"]))
        {
            require_once 'mostrarConsultorio.php';
        }
        else
        {
            $_GET['Bus'] = "ok";
            require_once 'buscarConsultorio.php';
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
        if(isset($_GET['idConsultorio']))
        {
            require_once 'mostrarListadoPorConsultorio.php';
        }
    ?>
</div>
<?php 
include_once '../html/pie.html';
?>
</body>
</html>