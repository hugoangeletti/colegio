<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/mesaEntradaEspecialistaLogic.php');
require_once ('../../dataAccess/tipoPagoLogic.php');
require_once ('../../dataAccess/resolucionesLogic.php');

$accion = $_GET['accion'];
$continua = TRUE;
$mensaje = "";
if (isset($_POST['idTipoEspecialista']) && $_POST['idTipoEspecialista'] <> "") {
    //da de alta la nueva solicitud de especialista
    $idTipoEspecialista = $_POST['idTipoEspecialista'];
    if ($accion <> 1) {
        if (isset($_POST['id']) && $_POST['id'] <> "") {
            $idMesaEntradaEspecialidad = $_POST['id'];
        } else {
            $continua = FALSE;
            $mensaje .= "FALTA SELECCIONAR IDMESAENTRADAESPECIALIDAD";
        }
    } 
    $idColegiado = $_POST['idColegiado'];
    $idEstadoMatricular = $_POST['idEstadoMatricular'];
    $estadoTesoreria = $_POST['estadoTesoreria'];
    if ($idTipoEspecialista == EXCEPTUADO_ART_8 && (!isset($_POST['inciso']) || $_POST['inciso'] == "")) {
        $continua = FALSE;
        $mensaje .= "FALTA SELECCIONAR INCISO";
    } else {
        $inciso = $_POST['inciso'];
    }
    if ($idTipoEspecialista == CALIFICACION_AGREGADA) {
        if (!isset($_POST['especialidadCalificacion']) || $_POST['especialidadCalificacion'] == "") {
            $continua = FALSE;
            $mensaje .= "FALTA SELECCIONAR CALIFICACION AGREGADA";
        } else {
            $idEspecialidad = $_POST['especialidadCalificacion'];
        }
    } else {
        if (!isset($_POST['especialidad']) || $_POST['especialidad'] == "") {
            $continua = FALSE;
            $mensaje .= "FALTA SELECCIONAR ESPECIALIDAD";
        } else {
            $idEspecialidad = $_POST['especialidad'];
        }
    }
    if ($idTipoEspecialista == OTRO_DISTRITO && (!isset($_POST['distrito']) || $_POST['distrito'] == "")) {
        $continua = FALSE;
        $mensaje .= "FALTA SELECCIONAR DISTRITO ORIGEN";
    } else {
        $distrito = $_POST['distrito'];
    }
} else {
    $continua = FALSE;
    $tipoMensaje = 'alert alert-danger';
    $mensaje .= "Faltan datos, verifique.";
}

if ($continua) {
    switch ($accion) {
        case 1:
            //continuar con el alta                                
            $resultado = realizarAltaMesaEntrada($idColegiado, $idTipoEspecialista, $idEspecialidad, $idEstadoMatricular, $estadoTesoreria, $distrito, $inciso);
            if ($resultado['estado']) {
                $expediente = $resultado['datos'];
                $numeroExpediente = $expediente['numeroExpediente'];
                $anioExpediente = $expediente['anioExpediente'];
                $idMesaEntradaEspecialidad = $expediente['idMesaEntradaEspecialidad'];
            } else {
                $continua = FALSE;
            }
            break;

        case 3:
            //continuar con modificacion                               
            $resultado = realizarModificacionMesaEntrada($idMesaEntradaEspecialidad, $idTipoEspecialista, $idEspecialidad, $distrito, $inciso);
            if (!$resultado['estado']) {
                $continua = FALSE;
            }
            break;

        default:
            $continua = FALSE;
            break;
    }

    if($resultado['estado']) {
        $tipoMensaje = 'alert alert-success';
    } else {
        $tipoMensaje = 'alert alert-danger';
    }
    $mensaje = $resultado['mensaje'];
} else {
    $resultado['mensaje'] = $mensaje;
}
/*
var_dump($_POST);
echo '<br>';
var_dump($resultado);
exit;
*/
?>

<body onLoad="document.forms['myForm'].submit()">
    <?php
    if ($resultado['estado']) {
    ?>        
        <form name="myForm"  method="POST" action="../especialidades_expedientes_nuevo.php?idColegiado=<?php echo $idColegiado; ?>.&id=<?php echo $idMesaEntradaEspecialidad; ?>">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $mensaje; ?>">
            <input type="hidden"  name="tipomensaje" id="tipomensaje" value="<?php echo $tipoMensaje;?>">
            <input type="hidden"  name="estadoSancion" id="estadoSancion" value="<?php echo $estadoSancion;?>">
        </form>
    <?php
    } else {
    ?>
        <form name="myForm"  method="POST" action="../especialidades_expedientes_alta.php?accion=<?php echo $accion; ?>">
            <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $mensaje; ?>">
            <input type="hidden"  name="tipomensaje" id="tipomensaje" value="<?php echo $tipoMensaje;?>">
            <input type="hidden"  name="observaciones" id="observaciones" value="<?php echo $observaciones;?>">
            <input type="hidden"  name="accion" id="accion" value="<?php echo $accion;?>">
        </form>
    <?php
    }
    ?>
</body>

