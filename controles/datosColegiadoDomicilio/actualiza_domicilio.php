<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/colegiadoDomicilioLogic.php');

$idColegiado = $_POST['idColegiado'];
$continua = TRUE;

if (isset($_POST['calle']) && isset($_POST['lateral']) && isset($_POST['localidad_buscar']) && isset($_POST['idLocalidad'])) {
    $calle = $_POST['calle'];
    $numero = $_POST['numero'];
    $lateral = $_POST['lateral'];
    $piso = $_POST['piso'];
    $depto = $_POST['depto'];
    $localidad_buscar = $_POST['localidad_buscar'];
    $idLocalidad = $_POST['idLocalidad'];
    $codigoPostal = $_POST['codigoPostal'];
    $origenForm = $_POST['origenForm'];
} else {
    $continua = FALSE;
    $tipoMensaje = 'alert alert-danger';
    $mensaje = "Faltan datos en el expediente, verifique.";
}

if ($continua){
    $accion = 'modificar';
    $resultado = agregarColegiadoDomicilio($idColegiado, $calle, $numero, $lateral, $piso, $depto, $idLocalidad, $codigoPostal, $accion);
} else {
    $resultado['mensaje'] = "ERROR EN LOS DATOS INGRESADOS";
    $resultado['icono'] = "glyphicon glyphicon-remove";
    $resultado['clase'] = "alert alert-error";
}

?>

<body onLoad="document.forms['myForm'].submit()">
    <?php
    if ($resultado['estado']) {
        if ($origenForm == 'consulta') {
            $formulario = "colegiado_consulta.php?idColegiado=".$idColegiado;
        } else {
            $formulario = "colegiado_domicilio.php?idColegiado=".$idColegiado;
        }
    ?>
        <form name="myForm"  method="POST" action="../<?php echo $formulario; ?>">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="<?php echo $resultado['icono']; ?>">
            <input type="hidden"  name="clase" id="clase" value="<?php echo $resultado['clase']; ?>">
        </form>
    <?php
    } else {
    ?>
        <form name="myForm"  method="POST" action="../domicilio_actualizar.php?idColegiado=<?php echo $idColegiado; ?>&ori=<?php echo $origenForm; ?>">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="icono" id="icono" value="glyphicon glyphicon-exclamation-sign">
            <input type="hidden"  name="clase" id="clase" value="alert alert-info">
            <input type="hidden"  name="calle" id="calle" value="<?php echo $calle;?>">
            <input type="hidden"  name="idLocalidad" id="idLocalidad" value="<?php echo $idLocalidda;?>">
            <input type="hidden"  name="localidad_buscar" id="localidad_buscar" value="<?php echo $localidad_buscar;?>">
            <input type="hidden"  name="codigoPostal" id="codigoPostal" value="<?php echo $codigoPostal;?>">
            <input type="hidden"  name="numero" id="numero" value="<?php echo $numero;?>">
            <input type="hidden"  name="lateral" id="lateral" value="<?php echo $lateral;?>">
            <input type="hidden"  name="piso" id="piso" value="<?php echo $piso;?>">
            <input type="hidden"  name="depto" id="depto" value="<?php echo $depto;?>">
        </form>
    <?php
    }
    ?>
</body>

