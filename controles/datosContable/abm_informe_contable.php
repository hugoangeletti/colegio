<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/informeContableLogic.php');

$continua = TRUE;
$mensaje = "";

//obtengo a la persona actual para verificar los campos modificados
if (isset($_POST['periodo']) && $_POST['periodo'] <> "") {
    $periodo = $_POST['periodo'];
} else {
    $continua = FALSE;
    $mensaje .= 'Periodo no ingresado - ';
}

if (isset($_POST['mesProcesado']) && $_POST['mesProcesado'] <> "") {
    $mesProcesado = $_POST['mesProcesado'];
} else {
    $continua = FALSE;
    $mensaje .= 'mesProcesado de Matriculacion no ingresado - ';
}

if ($continua){
    $resultado = generarInformeContable($periodo, $mesProcesado);
} else {
    $resultado['mensaje'] = "ERROR EN LOS DATOS INGRESADOS: ".$mensaje;
    $resultado['icono'] = "glyphicon glyphicon-remove";
    $resultado['clase'] = "alert alert-danger";
    $resultado['estado'] = $continua;
}
?>

<body onLoad="document.forms['myForm'].submit()">
    <?php
    if ($resultado['estado']) {
    ?>
        <form name="myForm"  method="POST" action="../informe_contable_lista.php">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
            <input type="hidden"  name="periodo" id="periodo" value="<?php echo $periodo; ?>">
        </form>
    <?php
    } else {
    ?>
        <form name="myForm"  method="POST" action="../informe_contable_form.php">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
            <input type="hidden"  name="periodo" id="periodo" value="<?php echo $periodo; ?>">
            <input type="hidden"  name="mesProcesado" id="mesProcesado" value="<?php echo $mesProcesado;?>">
        </form>
    <?php
    }
    ?>
</body>

