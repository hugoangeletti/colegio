<?php
include_once 'head_config.php';
include_once '../dataAccess/funciones.php';

?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
</head>
<body>
<?php 
include_once 'encabezado.php';

if(isset($_POST['remitente']))
{
    if($_POST['remitente'] != '')
    {
        $remitente = $_POST['remitente'];
    }
}
else
{
    if(isset($_GET['remitente']))
    {
        if($_GET['remitente'] != "")
        {
            $remitente = $_GET['remitente'];
        }
    }
}
?>
<div id="page-wrap">
    <div id="titulo">
        <h3>Búsqueda por Remitente</h3>
    </div>
    <br>
    <div>
        <?php 
        if(isset($_GET["BoM"]))
        {
            require_once 'mostrarRemitente.php';
        }
        else
        {
            $_GET['bus'] = "ok";
            require_once 'buscarRemitente.php';
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
    <br/><br/>
    
    <?php
        if(isset($remitente))
        {
    ?>
    <div id="tabs">
        <ul>
            <li><a href="mostrarListadoPorRemitente.php?st=4&remitente=<?php echo $remitente['id']; ?>">Notas y Oficios</a></li>
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