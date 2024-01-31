<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/habilitacionConsultorioLogic.php');

$continua = TRUE;
if (isset($_POST['idInspectorHabilitacion'])){
    $idInspectorHabilitacion = $_POST['idInspectorHabilitacion'];
} else {
    $continua = FALSE;
    $tipoMensaje = 'alert alert-danger';
    $mensaje = 'MAL INGRESO';
}

if ($continua){
    $resultado = desasignarInspectorAHabilitacion($idInspectorHabilitacion);
    if($resultado['estado']) {
        $tipoMensaje = 'alert alert-success';
    } else {
        $tipoMensaje = 'alert alert-danger';
    }
    $mensaje = $resultado['mensaje'];
}

?>


<body onLoad="document.forms['myForm'].submit()">
    <?php
    if ($resultado['estado']) {
    ?>
    <form name="myForm"  method="POST" action="../habilitaciones_asignadas_lista.php">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $mensaje; ?>">
            <input type="hidden"  name="tipomensaje" id="tipomensaje" value="<?php echo $tipoMensaje;?>">
        </form>
    <?php
    } else {
    ?>
        <form name="myForm"  method="POST" action="../habilitaciones_desasigna_form.php?id=<?php echo $idInspectorHabilitacion ?>">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $mensaje; ?>">
            <input type="hidden"  name="tipomensaje" id="tipomensaje" value="<?php echo $tipoMensaje;?>">
            <input type="hidden" id="idMesaEntrada" name="idMesaEntrada" value="<?php echo $idMesaEntrada; ?>">
        </form>
    <?php
    }
    ?>
</body>

