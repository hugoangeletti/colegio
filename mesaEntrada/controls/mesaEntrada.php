<?php
include_once 'head_config.php';
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/tipoTramiteLogic.php';
?>
<script type="text/javascript" src="../js/jqFuncs.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        if($("#colegiado").is(':checked')){
           $("#consulta").load("buscarColegiado.php?me=<?php echo $_GET['me'] ?>");
        }
    });
</script>

</head>
<body>
<?php 
include_once 'encabezado.php';
$consultaTipoME = obtenerTipoTramitePorId($_GET['me']);
$titulo = "";
if($consultaTipoME)
{
    if($consultaTipoME -> num_rows != 0)
    {
        $tipoME = $consultaTipoME -> fetch_assoc();
        $titulo = utf8_encode($tipoME['Nombre']);
    }
}
?>
<div id="titulo" class='tituloWrap'>
    <h3>Mesa de Entrada</h3>
    <h4>Solicitud de <?php echo $titulo ?></h4>
</div>
<div id="page-wrap" style="height: 680px">
    
    
</div>

<?php 
include_once '../html/pie.html';
?>
</body>
</html>
