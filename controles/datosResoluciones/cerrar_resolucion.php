<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/resolucionesLogic.php');
require_once ('../../dataAccess/colegiadoEspecialistaLogic.php');

$continua = TRUE;
$mensaje = 'OK';
if (isset($_GET['idResolucion'])) {
    $idResolucion = $_GET['idResolucion'];

    $resDetalle = obtenerDetalleResolucionPorId($idResolucion);
    if ($resDetalle['estado']) {
        $detalle = $resDetalle['datos'];
        if (isset($detalle)) {
            $cantidad = sizeof($detalle);
        } else {
            $cantidad = 0;
        }
        foreach ($detalle as $resolucionDetalle) {
            $idResolucionDetalle = $resolucionDetalle['idResolucionDetalle'];
            $idColegiado = $resolucionDetalle['idColegiado'];
            $idEspecialidad = $resolucionDetalle['idEspecialidad'];
            $idEstadoResolucionDetalle = $resolucionDetalle['idEstadoResolucionDetalle'];
            $fechaAprobacion = $resolucionDetalle['fechaAprobacion'];
            $fechaRecertificacion = $resolucionDetalle['fechaRecertificacion'];
            $fechaNacimiento = $resolucionDetalle['fechaNacimiento'];
            $idTipoEspecialista = $resolucionDetalle['idTipoEspecialista'];
            $distrito = $resolucionDetalle['distrito'];
            $idColegiadoEspecialista = $resolucionDetalle['idColegiadoEspecialista'];
            $idColegiadoEspecialistaTipo = $resolucionDetalle['idColegiadoEspecialistaTipo'];
            $incisoArticulo8 = $resolucionDetalle['incisoArticulo8'];

            $agregoEspecialista = FALSE;
            //si esta aprobada, cargo al especialista o la recertificacion
            if ($idEstadoResolucionDetalle <= 1) {
                switch ($idTipoEspecialista) {
                    case EXAMEN_COLEGIO:
                    case EXCEPTUADO_ART_8:
                    case CALIFICACION_AGREGADA:
                    case RECONOCIMIENTO_NACION:
                    case OTRO_DISTRITO:
                    case CONVENIO_UNLP:
                        //verifica que no exista
                        if (!isset($idColegiadoEspecialista) || $idColegiadoEspecialista == "" || $idColegiadoEspecialista == 0) {
                            $fechaVencimiento = sumarRestarSobreFecha($fechaAprobacion, 5, 'year', '+');
                            if (!isset($distrito) || $distrito == "" || $distrito == " " || $distrito == "0") {
                                $distrito = '1';
                            }
                            $resultado = agregarEspecialista($idEspecialidad, $fechaAprobacion, $distrito, $fechaVencimiento, $idColegiado, $idTipoEspecialista, $idResolucionDetalle, $incisoArticulo8);
                            $agregoEspecialista = $resultado['estado'];
                        }
                        break;

                    case JERARQUIZADO:
                    case CONSULTOR:
                        if (!isset($idColegiadoEspecialistaTipo) || $idColegiadoEspecialistaTipo == "" || $idColegiadoEspecialistaTipo == 0) {
                            $resultado = agregarEspecialistaTipo($idColegiadoEspecialista, $idTipoEspecialista, $fechaAprobacion, $distrito, $idResolucionDetalle);
                            $agregoEspecialista = $resultado['estado'];
                        }
                        break;
                    case RECERTIFICACION:
                        if (isset($idColegiadoEspecialista) && $idColegiadoEspecialista <> "" && $idColegiadoEspecialista <> 0) {
                            $fechaVencimiento = sumarRestarSobreFecha($fechaRecertificacion, 5, 'year', '+');
                            $resultado = agregarRecertificacion($idColegiadoEspecialista, $fechaRecertificacion, $fechaVencimiento, $idResolucionDetalle);
                            $agregoEspecialista = $resultado['estado'];
                        }
                        break;
                    default:
                        break;
                }

                //si salio bien entonces marco como aplicado el detalle de la resolucion
                if ($agregoEspecialista) {
                    $resActualiza = cambiarEstadoResolucionDetalle($idResolucionDetalle, 1);
                    $cantidad -= 1;
                } 
            }
        }
        
        //if ($cantidad == 0) {
            $resResolucion = cambiarEstadoResolucion($idResolucion, 'E', 'C');
        //}
    } else {
        $continua = FALSE;
        $tipoMensaje = $resDetalle['clase'];
        $mensaje = $resDetalle['mensaje'];
    }
} else {
    $continua = FALSE;
    $tipoMensaje = 'alert alert-danger';
    $mensaje = "Faltan datos en el formulario, verifique.";
}

echo $mensaje;
var_dump($resultado);
exit;

?>

<body onLoad="document.forms['myForm'].submit()">
    <form name="myForm"  method="POST" action="../especialidades_resoluciones_matriculas.php?idResolucion=<?php echo $idResolucion; ?>">
        <input type="hidden"  name="mensaje" id="mensaje" value="<?php echo $mensaje; ?>">
        <input type="hidden"  name="tipomensaje" id="tipomensaje" value="<?php echo $tipoMensaje;?>">
    </form>
</body>

