<?php
if (!isset($localhost)) {
    require_once ('../../dataAccess/config.php');
}
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/usuarioLogic.php');
require_once ('../../dataAccess/funcionesPhp.php');

$idUsuario = $_POST['idUsuario'];
$cantidadRoles = $_POST['cantidadRoles'];
$arrayIdRoles = array();

for($i = 1; $i < $cantidadRoles; $i++)
{
    if(isset($_POST['rol'.$i]))
    {
        $hayCheckSeleccionado=true;
        array_push($arrayIdRoles,$_POST['rol'.$i]);
    }        
}

if($hayCheckSeleccionado)
{
    $cargaDatos = TRUE;
    $resultado = actualizarUsuarioRol($idUsuario, $arrayIdRoles);
    if($resultado['estado'])
    {
        $tipoMensaje='alert alert-success';
        $resultado['mensaje'] = 'Rol registrado con exito.';
    }
    else
    {
        $tipoMensaje='alert alert-danger';
        $resultado['mensaje'] = 'Rol NO se registro.';
    }
} else {
    $cargaDatos = FALSE;
    $resultado['mensaje'] = 'Debe seleccionar al menos un rol.';
}
?>

<body onLoad="document.forms['myForm'].submit()">
        <form name="myForm"  method="POST" action="../usuariorol_form.php">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $resultado['mensaje']; ?>">
            <input type="hidden"  name="tipomensaje" id="tipomensaje" value="<?php echo $tipoMensaje;?>">
            <input type="hidden" name="idUsuario" id="idUsuario" value="<?php echo $idUsuario; ?>" />
        </form>
</body>

