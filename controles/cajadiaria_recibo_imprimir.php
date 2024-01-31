<?php
require_once '../dataAccess/config.php';
permisoLogueado();
require_once '../html/head.php';
require_once '../html/header.php';
require_once '../dataAccess/funcionesConector.php';
require_once '../dataAccess/funcionesPhp.php';
require_once '../dataAccess/cajaDiariaLogic.php';
require_once '../dataAccess/mesaEntradaEspecialistaLogic.php';
require_once '../dataAccess/cursoLogic.php';
require_once '../dataAccess/colegiadoDomicilioLogic.php';

$continuar = true;
if (isset($_GET['id']) && $_GET['id'] <> "") {
    $idCajaDiariaMovimiento = $_GET['id'];
    $resRecibo = obtenerCajaDiariaMovimientoPorId($idCajaDiariaMovimiento);
    if ($resRecibo['estado']) {
        $recibo = $resRecibo['datos']; //$idCajaDiaria, $fechaPago, $horaPago, $monto, $tipo, $numero, $idAsistente, $idColegiado, $usuario, $estado, $apellidoNombre, $matricula
        $idCajaDiaria = $recibo['idCajaDiaria'];
        $fechaPago = $recibo['fechaPago'];
        $totalRecibo = $recibo['monto'];
        $tipoRecibo = $recibo['tipoRecibo'];
        $numeroRecibo = $recibo['numeroRecibo'];
        $idAsistente = $recibo['idAsistente'];
        $idColegiado = $recibo['idColegiado'];
        $usuario = $recibo['usuario'];
        $apellidoNombre = $recibo['apellidoNombre'];
        $matricula = $recibo['matricula'];

        if ($fechaPago == date('Y-m-d')) {
            $link = "cajadiaria.php";
        } else {
            $link = "cajadiaria_movimientos.php?id=".$idCajaDiaria;
        }
        $pathOrigen = '../';
        include 'datosCajaDiaria/generar_pdf.php';        
        if (isset($reciboPDF)) {
        ?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-9">
                            <h4>Imprimir recibo</h4>
                        </div>
                        <div class="col-md-3 text-left">
                            <a href="<?php echo $link; ?>" class="btn btn-info">Volver</a>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-9">
                             <embed src='data:application/pdf;base64,<?php echo $reciboPDF; ?>' height="800px" width='100%' type='application/pdf'> 
                        </div>
                    </div>
                </div>
            </div>
    <?php
        } else {
            echo 'ERROR AL OBTENER EL RECIBO';
        }
    } else {
    ?>
        <div class="col-md-12">
            <h2 class="alert alert-danger">ERROR AL ACCEDER AL RECIBO</h2>
        </div>
        <a href="cajadiaria_movimientos.php?id=<?php echo $idCajaDiaria; ?>" class="btn btn-primary">Volver</a>
    <?php
    }
} else {
?>
    <div class="col-md-12">
        <h2 class="alert alert-danger">ERROR AL INGRESAR</h2>
    </div>
    <a href="cajadiaria_movimientos.php?id=<?php echo $idCajaDiaria; ?>" class="btn btn-primary">Volver</a>
<?php
}
include("../html/footer.php");

