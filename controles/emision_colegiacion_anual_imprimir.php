<?php
require_once ('../dataAccess/config.php');
//permisoLogueado();
//require_once ('../html/head.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/colegiadoLogic.php');
require_once ('../dataAccess/colegiadoDeudaAnualLogic.php');
require_once ('../dataAccess/colegiadoPlanPagoLogic.php');
require_once ('../dataAccess/colegiadoEspecialistaLogic.php');
require_once ('../dataAccess/colegiadoDomicilioLogic.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);
set_time_limit(0);
ini_set("memory_limit",-1);

require_once('../tcpdf/config/lang/spa.php');
require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF 
{
        //Page header
        public function Header() 
        {
                // Logo
//                $image_file = '../public/images/logo_colmed1_lg.png';
//                $this->Image($image_file, 10, 5, 190, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
//                 // Set font
//                $this->SetFont('helvetica', 'B', 20);
//                // Title
                $this->Cell(0, 15, '', 0, false, 'C', 0, 'Nota', 0, false, 'M', 'M');
        }

        // Page footer
        public function Footer() {
//                // Position at 15 mm from bottom
//                $this->SetY(-15);
//                // Set font
//                $this->SetFont('helvetica', 'I', 8);
//
//                //$this->Cell(0, 10, 'Relaciones con la comunidad', 0, false, 'C', 0, '', 0, false, 'T', 'M');
//                //$this->Ln(3);
//                // Page number
//                $this->Cell(0, 10, 'Pag. '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
}
$pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
if (isset($_POST['colegiados'])) {
    //para la emision anual lo ponemos en hoja Oficio, en PDF_UNIT='mm'
    $width = 216;  
    $height = 330; 
    $pageLayout = array($width, $height); //  or array($height, $width) 
    $pdf = new MYPDF('p', PDF_UNIT, $pageLayout, true, 'UTF-8', false);
}
//fin probando tamaño
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(true);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(0, 0, 0);
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetAutoPageBreak(TRUE, 0);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//style para el codigo de barras
$styleCB = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);

$styleCB = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);

if (isset($_POST['colegiados']) || isset($_GET['idColegiado']) || isset($_GET['emisionTotal'])) {
    $periodoActual = $_SESSION['periodoActual'];
    $envioMail = FALSE;
    if (isset($_POST['colegiados'])) {
        $colegiados = unserialize($_POST['colegiados']);
        $nombreArchivo = $_POST['archivo'].'.pdf';
        $tipoPdf = 'D';
        $mailDestino = NULL;
    } else {
        $datos = array();
        if (isset($_GET['idColegiado'])) {
            $idColegiado = $_GET['idColegiado'];
            if (isset($_POST['tipoPdf'])) {
                $tipoPdf = $_POST['tipoPdf'];
                if ($tipoPdf = "F") {
                    $envioMail = TRUE;
                }
            } else {
                $tipoPdf = 'D';
            }
            if (isset($_POST['mail'])) {
                $mailDestino = $_POST['mail'];
            } else {
                $mailDestino = NULL;
            }            
        } else {
            if (isset($_GET['emisionTotal'])) {
                $emisionTotal = $_GET['emisionTotal'];
                $tipoPdf = 'F';
                $envioMail = FALSE;
                $resColegiados = obtenerColegiadosEmisionAnualTotal($periodoActual);
                if ($resColegiados['estado']) {
                    $datos = $resColegiados['datos'];
                } 
            } else {
                $idColegiado = NULL;
            }
        }
        if (isset($idColegiado)) {
            $row = array($idColegiado);
            array_push($datos, $row);
            $resColegiado = obtenerColegiadoPorId($idColegiado);
            if ($resColegiado['estado'] && $resColegiado['datos']) {
                $colegiado = $resColegiado['datos'];
                $matricula = $colegiado['matricula'];
                $nombreArchivo = 'ColegiacionMatricula_'.$matricula.'.pdf';
            } else {
                $nombreArchivo = 'ColegiacionMatricula_Id'.$idColegiado.'.pdf';
            }
        }
        $colegiados = $datos;
    }

    foreach ($colegiados as $colegiadoImprimir) {
        if (isset($_GET['emisionTotal'])) {
            $pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(true);
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(0, 0, 0);
            //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->SetAutoPageBreak(TRUE, 0);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            $idColegiado = $colegiadoImprimir['idColegiado'];
        } else {
            $idColegiado = $colegiadoImprimir[0];
        }
        $html='';
        $periodoActual = $_SESSION['periodoActual'];

        //$idColegiado = $_GET['idColegiado'];
        $resColegiado = obtenerColegiadoPorId($idColegiado);
        if ($resColegiado['estado'] && $resColegiado['datos']) {
            $colegiado = $resColegiado['datos'];
            $matricula = $colegiado['matricula'];
            $sexo = $colegiado['sexo'];
            if ($sexo == "M") {
                $dr_dra = "Dr. ";
            } else {
                $dr_dra = "Dra. ";
            }
            $dr_dra .= trim($colegiado['apellido']).', '.trim($colegiado['nombre']);

            //obtengo el estado con tesoreria
            $resEstadoTeso = estadoTesoreriaPorColegiado($idColegiado, $periodoActual);
            if ($resEstadoTeso['estado']){
                $codigo = $resEstadoTeso['codigoDeudor'];
                $resEstadoTesoreria = estadoTesoreria($codigo);
                if ($resEstadoTesoreria['estado']){
                    $estadoTesoreria = $resEstadoTesoreria['estadoTesoreria'];
                } else {
                    $estadoTesoreria = $resEstadoTesoreria['mensaje'];
                }
            } else {
                $estadoTesoreria = $resEstadoTeso['mensaje'];
            }
            
            //verifico si tiene especialidades con caducidad menores hacia un año adelante
            $especialidades = "";
            $resEspecialidad = especialidadesConCaducidad($idColegiado);
            if ($resEspecialidad['estado']) {
                foreach ($resEspecialidad['datos'] as $dato) {
                    $especialidades .= $dato['especialidad'].' '.$dato['caducidad'];
                }
            }
            
            //obtengo el domicilio y localidad
            $domicilioCompleto = "";
            $localidad = "";
            $resDomicilio = obtenerColegiadoDomicilioPorIdColegiado($idColegiado);
            if ($resDomicilio['estado']) {
                $domicilio = $resDomicilio['datos'];
                if ($domicilio['calle']) {
                    $domicilioCompleto = $domicilio['calle'];
                    if ($domicilio['numero']) {
                        $domicilioCompleto .= " Nº ".$domicilio['numero'];
                    }
                    if ($domicilio['lateral']) {
                        $domicilioCompleto .= " e/ ".$domicilio['lateral'];
                    }
                    if ($domicilio['piso'] && strtoupper($domicilio['piso']) != "NR") {
                        $domicilioCompleto .= " Piso ".$domicilio['piso'];
                    }
                    if ($domicilio['depto'] && strtoupper($domicilio['depto']) != "NR") {
                        $domicilioCompleto .= " Dto. ".$domicilio['depto'];
                    }
                }
                if ($domicilio['nombreLocalidad']) {
                    $localidad = $domicilio['nombreLocalidad'].' - ('.$domicilio['codigoPostal'].')';
                }
            }
            
            $periodoHasta = $periodoActual;
            $periodoDesde = $periodoActual;
            $resDeuda = obtenerColegiadoDeudaAnualPorIdColegiado($idColegiado, 0, $periodoDesde, $periodoHasta);
            if ($resDeuda['estado']) {
                $deuda = $resDeuda['datos'];
                $totalDeuda = 0;
                
                foreach ($resDeuda['datos'] as $dato) {
                    $idColegiadoDeudaAnual = $dato['id'];
                    $periodo = $dato['periodo'];
                    $importeAnual = $dato['importe'];
                    $cuotas = $dato['cuotas'];
                    $estado = $dato['estado'];
                    
                    if ($estado == "A") {
                        //muestra cuotas del periodo
                        //esta con deuda, muestro las cuotas
                        $resCuotas = obtenerDeudaAnualCuotas($idColegiadoDeudaAnual);
                        if ($resCuotas['estado']) {
                            $cantidadCuotas = sizeof($resCuotas['datos']);
                            if ($cantidadCuotas == 0) { continue; }

                            $cuotasImpresas = 0;
                            $importeAgregarPagoTotal = 0;
                            foreach ($resCuotas['datos'] as $cuotas) {
                                $estado = $cuotas['estado'];
                                if ($estado == 1) {
                                    if ($cuotasImpresas == 0) {
                                        $pdf->SetFont('dejavusans', '', 8);
                                        $pdf->AddPage();
                                        $pdf->Image('../public/images/logoChequera.png', 5, 5, 100, 15, 'PNG');
                                        $pdf->SetXY(110, 5);
                                        $pdf->SetFont('dejavusans', '', 8);
                                        $pdf->MultiCell(100, 10, 'IMPORTANTE: Recuerde que para hacer uso de la matricula colegiada y sus beneficios, debe tener sus pagos al dia', 0, 'L', false, 0, '', '', true);
                                        $pdf->SetFont('dejavusans', 'B', 8);
                                        if ($estadoTesoreria <> 'Al día') {
                                            $pdf->SetXY(110, 12);
                                            $pdf->MultiCell(0, 5, 'Según nuestros registros Ud. se encuentra '.$estadoTesoreria.' Regularice su situación.', 0, 'L', false, 0, '', '', true);
                                        }
                                        if ($especialidades <> "") {
                                            $pdf->SetXY(110, 20);
                                            $pdf->MultiCell(0, 5, 'Su autorización para el uso del título de especialista en: '.$especialidades.'., recertifique la misma.', 0, 'L', false, 0, '', '', true);
                                        }
                                        $pdf->SetFont('dejavusans', '', 8);
                                        $pdf->SetXY(110, 30);
                                        $pdf->MultiCell(0, 5, 'Código Pago Electrónico Red Link / PagoMisCuentas: ', 0, 'L', false, 1, '', '', true);
                                        $pdf->SetXY(185, 30);
                                        $pdf->SetFont('dejavusans', 'B', 9);
                                        $pdf->MultiCell(0, 5, rellenarCeros($matricula, 8), 0, 'L', false, 1, '', '', true);
                                        //$pdf->SetXY(160, 30);
                                        //$pdf->SetFont('dejavusans', 'B', 8);
                                        //$pdf->MultiCell(0, 5, 'Red Link / PagoMisCuentas: ', 0, 'L', false, 0, '', '', true);
                                        $pdf->SetXY(110, 35);
                                        $pdf->SetFont('dejavusans', '', 9);
                                        $pdf->MultiCell(0, 5, 'Lugares de Pago: BaproPago / RapiPagos', 0, 'L', false, 1, '', '', true);

                                        //$pdf->Ln(20);
                                        $pdf->SetXY(5, 20);
                                        $pdf->SetFont('dejavusans', '', 9);
                                        $pdf->MultiCell(0, 5, 'Matrícula: ', 0, 'L', false, 1, '', '', true);
                                        $pdf->SetXY(22, 20);
                                        $pdf->SetFont('dejavusans', 'B', 9);
                                        $pdf->MultiCell(0, 5, $matricula, 0, 'L', false, 1, '', '', true);
                                        $pdf->SetXY(5, 25);
                                        $pdf->MultiCell(0, 5, $dr_dra, 0, 'L', false, 1, '', '', true);
                                        $pdf->SetXY(5, 30);
                                        $pdf->SetFont('dejavusans', '', 9);
                                        $pdf->MultiCell(0, 5, 'Domicilio: ', 0, 'L', false, 1, '', '', true);
                                        $pdf->SetXY(22, 30);
                                        $pdf->SetFont('dejavusans', 'B', 9);
                                        $pdf->MultiCell(0, 5, $domicilioCompleto, 0, 'L', false, 1, '', '', true);
                                        $pdf->SetXY(5, 35);
                                        $pdf->SetFont('dejavusans', '', 9);
                                        $pdf->MultiCell(0, 5, 'Localidad: ', 0, 'L', false, 0, '', '', true);
                                        $pdf->SetXY(22, 35);
                                        $pdf->SetFont('dejavusans', 'B', 9);
                                        $pdf->MultiCell(0, 5, $localidad, 0, 'L', false, 0, '', '', true);
                                        $pdf->SetFont('dejavusans', '', 9);
                                        $pdf->Ln(20);

                                        $posLinea = 40;
                                        if (isset($_POST['colegiados'])) {
                                            //si es emision anual en Oficio, la linea la pongo en 45
                                            $posLinea = 45;
                                        }
                                        $pdf->Line(0, $posLinea, 220, $posLinea, array('width' => 0));
                                    }
                                    //if (isset($_POST['colegiados'])) {
                                        //si es emision anual en Oficio, la linea la pongo en 45
                                    //    $posLinea += 36;
                                    //} else {
                                    //    $posLinea += 32;
                                    //}
                                    $posLinea += 40;
                                    $cuota = intval($cuotas['cuota']);
                                    $idColegiadoDeudaAnualCuota = $cuotas['idColegiadoDeudaAnualCuota'];
                                    $importe = $cuotas['importe'];
                                    $importeActualizado = $cuotas['importeActualizado'];
                                    $fechaVencimiento = $cuotas['vencimiento'];
                                    $estadoPP = $cuotas['estadoPP'];
                                    $idPlanPago = $cuotas['idPlanPago'];
                                    $fechaPago = $cuotas['fechaPago'];
                                    
                                    if ($fechaVencimiento < date('Y-m-d')) {
                                        $fechaVencimiento = sumarRestarSobreFecha(date('Y-m-d'), 7, 'day', '+');
                                    }

                                    //solo para el periodo 2021 se suman las cuotas 1, 2 y 3 con esten abonadas para agregarlas al pago total
                                    if ($periodo == 2021 && ($cuota == 1 || $cuota == 2 || $cuota == 3)) {
                                        $importeAgregarPagoTotal += $importeActualizado;
                                    }
                                    //
                                    $pdf->Image('../public/images/logoChequera.png' , 5, $posLinea-35, 80 , 10,'PNG');                                
                                    $pdf->SetXY(3, $posLinea-25);
                                    $pdf->MultiCell(0, 5, $dr_dra, 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(3, $posLinea-20);
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->MultiCell(0, 5, 'Matrícula: ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(25, $posLinea-20);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, $matricula, 0, 'L', false, 1, '', '', true);
                                    /*
                                    $pdf->SetXY(40, $posLinea-20);
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->MultiCell(0, 5, 'Período: '.$periodo, 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(65, $posLinea-15);
                                    $pdf->MultiCell(0, 5, 'CUOTA '.$cuota, 0, 'L', false, 1, '', '', true);
                                    */
                                    $pdf->SetXY(3, $posLinea-15);
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->MultiCell(0, 5, 'Recibo ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(25, $posLinea-15);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, $idColegiadoDeudaAnualCuota, 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(3, $posLinea-10);
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->MultiCell(0, 5, 'Vencimiento: ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(25, $posLinea-10);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, cambiarFechaFormatoParaMostrar($fechaVencimiento), 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(3, $posLinea-5);
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->MultiCell(0, 5, 'Importe: ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(25, $posLinea-5);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, '$'.number_format($importeActualizado, 2, ',', ''), 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(100, $posLinea-30);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, 'Importe: ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(117, $posLinea-30);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, '$'.number_format($importeActualizado, 2, ',', ''), 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(150, $posLinea-30);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, 'Vencimiento: '.cambiarFechaFormatoParaMostrar($fechaVencimiento), 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(100, $posLinea-25);
                                    $pdf->MultiCell(0, 5, 'Recibo ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(115, $posLinea-25);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, $idColegiadoDeudaAnualCuota, 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(140, $posLinea-25);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, 'Período: '.$periodo, 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(170, $posLinea-25);
                                    $pdf->SetFont('dejavusans', 'B', 14);
                                    $pdf->MultiCell(0, 5, 'CUOTA '.$cuota, 0, 'L', false, 1, '', '', true);
                                    $codigoBarra = obtenerCodigoBarra($idColegiadoDeudaAnualCuota, $importeActualizado, $importeActualizado, $fechaVencimiento, $fechaVencimiento, NULL);
                                    $pdf->SetXY(80, $posLinea-20);
                                    $pdf->write1DBarcode($codigoBarra, 'I25', '', '', '', 18, 0.4, $styleCB, 'N');
                                    $pdf->SetFont('dejavusans', '', 8);
                                    $pdf->SetXY(110, $posLinea-35);
                                    $pdf->MultiCell(0, 5, 'Código Pago Electrónico Red Link / PagoMisCuentas: ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(185, $posLinea-35);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, rellenarCeros($matricula, 8), 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(60, $posLinea-5);
                                    $pdf->SetFont('dejavusans', '', 8);
                                    $pdf->MultiCell(0, 5, 'Esta cuota incluye el Fondo Solidario, para gozar del mismo deberá cancelarla al vencimiento.', 0, 'L', false, 1, '', '', true);
                                    //$pdf->MultiCell(0, 5, $codigoBarra, 0, 'L', false, 1, '', '', true);
                                    
                                    $pdf->Line(0, $posLinea, 220, $posLinea, array('width' => 0));
                                    
                                    $cuotasImpresas++;
                                    if ($cuotasImpresas >= 6) { //$cuotasImpresas >= 8
                                        $cuotasImpresas = 0;
                                    }
                                    
                                }
                            }
                            //termino de imprimir las cuotas, me fijo si esta vigente el pago completo, debo imprimirlo
                            if ($importeAgregarPagoTotal > 0) {
                                //descuenta 10% al importe adeudado
                                $importeAgregarPagoTotal = round(($importeAgregarPagoTotal * 0.90), 0);
                            }

                            //$resPagoTotal = obtenerPagoTotalPorIdDeudaAnual_2021($idColegiadoDeudaAnual, $importeAgregarPagoTotal);
                            $resPagoTotal = obtenerPagoTotalPorIdDeudaAnual($idColegiadoDeudaAnual);
                            if ($resPagoTotal['estado']) {
                                $pagoTotal = $resPagoTotal['datos'];
                                $idPagoTotal = $pagoTotal['idColegiadoDeudaAnualTotal'];
                                $fechaVencimiento = $pagoTotal['fechaVencimiento'];
                                $importeActualizado = $pagoTotal['importe'];
                                $codigoBarra = $pagoTotal['codigoBarra'];

                                if ($fechaVencimiento >= date('Y-m-d') && $importeActualizado > 0) {
                                    if ($cuotasImpresas == 0 || $cuotasImpresas >= 8) {
                                        $pdf->SetFont('dejavusans', '', 8);
                                        $pdf->AddPage();
                                        $posLinea = 40;
                                    } else {
                                        $posLinea += 30;
                                    }

                                    $pdf->Image('../public/images/logoChequera.png' , 5, $posLinea-30, 80 , 10,'PNG');                                
                                    $pdf->SetXY(3, $posLinea-20);
                                    $pdf->MultiCell(0, 5, $dr_dra, 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(3, $posLinea-15);
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->MultiCell(0, 5, 'Matrícula: ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(20, $posLinea-15);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, $matricula, 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(3, $posLinea-10);
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->MultiCell(0, 5, 'Pago total: ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(22, $posLinea-10);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, '$'.number_format($importeActualizado, 2, ',', '').' - '.cambiarFechaFormatoParaMostrar($fechaVencimiento), 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(90, $posLinea-28);
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->MultiCell(0, 5, $dr_dra.' - MP: '.$matricula, 0, 'L', false, 1, '', '', true);
                                    //lugar de pago
                                    $pdf->SetFont('dejavusans', '', 9);
                                    $pdf->SetXY(10, $posLinea);
                                    $pdf->MultiCell(0, 5, 'Lugar de Pago: RAPIPAGOS / PagoFacil', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(80, $posLinea);
                                    $pdf->MultiCell(0, 5, 'Código Pago Electrónico Red Link / PagoMisCuentas: ', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(165, $posLinea);
                                    $pdf->SetFont('dejavusans', 'B', 9);
                                    $pdf->MultiCell(0, 5, rellenarCeros($matricula, 8), 0, 'L', false, 1, '', '', true);
                                    //fin lugar de pago
                                    $pdf->SetXY(100, $posLinea-24);
                                    $pdf->SetFont('dejavusans', 'B', 11);
                                    $pdf->MultiCell(140, 5, 'Pago Total Anual con descuento:', 0, 'L', false, 1, '', '', true);
                                    $pdf->SetXY(90, $posLinea-20);
                                    $pdf->SetFont('dejavusans', 'B', 11);
                                    $pdf->MultiCell(140, 5, 'Importe: $'.number_format($importeActualizado, 2, ',', '').' - Vencimiento: '.cambiarFechaFormatoParaMostrar($fechaVencimiento), 0, 'L', false, 1, '', '', true);
                                    //$pdf->SetXY(90, $posLinea-15);
                                    $pdf->SetXY(70, $posLinea-15);
                                    $pdf->write1DBarcode($codigoBarra, 'I25', '', '', '', 18, 0.4, $styleCB, 'N');
                                }                                
                            }                            
                        }   //fin buscando cuotas
                    }   //fin si la deuda anual no esta activa  
                }   //fin foreach deuda anual
            }   //fin obteniendo deuda anual
            $pdf->lastPage();
            if (isset($_GET['emisionTotal'])) {
                $resColegiado = obtenerColegiadoPorId($idColegiado);
                if ($resColegiado['estado'] && $resColegiado['datos']) {
                    $colegiado = $resColegiado['datos'];
                    $matricula = $colegiado['matricula'];
                    $nombreArchivo = 'ColegiacionMatricula_'.$matricula.'.pdf';
                } else {
                    $nombreArchivo = 'ColegiacionMatricula_Id'.$idColegiado.'.pdf';
                }

                $destination = $tipoPdf; //'F';
                if (!preg_match('/\.pdf$/', $path_to_store_pdf))
                {
                       $path_to_store_pdf .= '.pdf';
                }
                ob_clean();
                $camino = $_SERVER['DOCUMENT_ROOT'];
                $camino .= PATH_PDF;
                //$nombreArchivo = 'Chequeras_'.$matricula.'.pdf';

                $estructura = "../archivos/chequera/".$periodoActual;
                if (!file_exists($estructura)) {
                    mkdir($estructura, 0777, true);
                }
                if (file_exists("../archivos/chequera/".$periodoActual."/".$nombreArchivo)) {
                    unlink("../archivos/chequera/".$periodoActual."/".$nombreArchivo);
                } 

                $pdf->Output($camino.'/archivos/chequera/'.$periodoActual.'/'.$nombreArchivo, $destination);      

                //se marca como emitido en la tabla de colegiadodeudaanual
                $resEmitido = marcarEmitidoColegiadoDeudaAnual($idColegiadoDeudaAnual);
                /*
                echo 'id->'.$idColegiadoDeudaAnual.'<br>';
                var_dump($resEmitido);
                echo '<br>';
                */
            }
        }   //fin buscando al colegiado
    }   //fin foreach listado de colegiados

    if (!isset($_GET['emisionTotal'])) {
        $destination = $tipoPdf; //'F';
        if (!preg_match('/\.pdf$/', $path_to_store_pdf))
        {
               $path_to_store_pdf .= '.pdf';
        }
        ob_clean();
        $camino = $_SERVER['DOCUMENT_ROOT'];
        $camino .= PATH_PDF;
        //$nombreArchivo = 'Chequeras_'.$matricula.'.pdf';

        $estructura = "../archivos/chequera/".$periodoActual;
        if (!file_exists($estructura)) {
            mkdir($estructura, 0777, true);
        }
        if (file_exists("../archivos/chequera/".$periodoActual."/".$nombreArchivo)) {
            unlink("../archivos/chequera/".$periodoActual."/".$nombreArchivo);
        } 

        if ($tipoPdf == 'F') {
            $pdf->Output($camino.'/archivos/chequera/'.$periodoActual.'/'.$nombreArchivo, $destination);        
            $envioMail = TRUE;
        } else {
            $pdf->Output($nombreArchivo, $destination);        
            $envioMail = FALSE;
        }
    }

    if ($envioMail && isset($mailDestino)) {
        //enviamos el pdf por mail si tiene contacto
        $destinatario = $colegiado['apellido'].', '.$colegiado['nombre'];
        require_once '../PHPMailer/class.phpmailer.php';
        require_once '../PHPMailer/class.smtp.php';

        //PARA LA NUEVA VERSION DEL MAILER
        //require '../PHPMailer_v61/vendor/autoload.php';

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "mail.colmed1.org.ar";
        $mail->Port = 465;
        //$mail->Username = "sistemas@colmed1.org.ar";
        //$mail->Password = "@sistemas1";
        $mail->Username = MAIL_MASIVO;
        $mail->Password = MAIL_MASIVO_PASS;

        $mail->From = "mesadeentrada@colmed1.org.ar";
        $mail->FromName = "Colegio de Medicos. Distrito I";
        $mail->Subject = "Chequera de cuotas de colegiacion - Tesoreria del Colegio de Medicos Distrito I";
        $mail->AltBody = "";
        $mail->MsgHTML(utf8_decode("Le enviamos la chequera de las cuotas de colegiacion del Colegio de Medicos Distrito I, correspondientes al período <b>".$periodo."</b>
            <br><br>
            Le informamos que las mismas las puede abonar:
            <br><br>
            <b>&nbsp;&nbsp;*&nbsp;</b> Por homebanking LINK o PagoMisCuentas con el código electrónico: <b>".rellenarCeros($matricula, 8)."</b>
            <br>
            <b>&nbsp;&nbsp;*&nbsp;</b>Con los comprobantes en: <b>BaproPago, RapiPagos, PagoFacil, o con al APP de MercadoPago.</b>
            <br><br>
            También tiene la opción de adherirse al débito automático con tarjeta de crédito VISA o por CBU. Enviendo una foto del frente de la tarjeta o enviando los datos de su cuenta con el CBU a la casilla de correo tesoreria@colmed1.org.ar
            <br><br>
            Saludamos atentamente.
            <br><br>
            La Tesorería"));
        $mail->AddAttachment("../archivos/chequera/".$periodoActual."/".$nombreArchivo);
        $mail->AddAddress($mailDestino, $destinatario);
        $mail->IsHTML(true);
        //echo $mailDestino .' - '. $matricula .' - '. $destinatario;
        /*
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->AddEmbeddedImage('../public/images/turnos.png','encabezado');   
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->Host = 'mail.gob.gba.gov.ar';
                $mail->Port = 587;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->SMTPOptions = array(
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                );
                $mail->SMTPAuth = true;
                //$mail->Username = 'no-responder-partidas';
                $mail->Username = 'no-responder-turnos';
                //$mail->Password = 'KdLTyOGHD1@2';
                $mail->Password = 'DygoPhornA';
                //$mail->setFrom('no-responder-partidas@gob.gba.gov.ar', 'Turnos Web');
                $mail->setFrom('no-responder-turnos@gob.gba.gov.ar', 'Turnos Web');
                $mail->addAddress($email, '');
                $mail->Subject = 'Turno Web - Registro de las Personas';
                $mail->msgHTML(file_get_contents('contents.html'), __DIR__);
                
                $mail->Body =$body;
        
        */
        if($mail->Send()) {
            $mailEnviado = TRUE;
        }else{
            $mailEnviado = FALSE;
        }
    }
    if ($envioMail  && isset($mailDestino)) {
        if ($mailEnviado) {
            require_once ('../html/head.php');
            require_once ('../html/encabezado.php');
        ?>
            <div class="col-md-12">
                <div class="row" style="background-color: #428bca;">
                    <div class="col-md-12"></div>
                </div>
            </div>
            <div class="row">&nbsp;</div>
            <div class="row">
                <div class="col-md-12">
                    <h3>Chequera de cuotas de colegiación solicitada por <?php echo $colegiado['nombre'].' '.$colegiado['apellido']; ?>, de cuotas del período <?php echo $periodoActual; ?></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-success" role="alert">
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                        <span><strong>&nbsp;El mail se envió con éxito al correo: </strong><?php echo $mailDestino; ?></span>
                    </div>        
                </div>
            </div>
        <?php
        } else {
        ?>    
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">
                        <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                        <span><strong>ERROR al enviar el mail al correo: </strong><?php echo $mailDestino; ?><strong>. Vuelva a intentar más tarde.</strong></span>
                    </div>        
                </div>
            </div>
        <?php
        }
        ?>    
        <div class="row">
            <div class="col-md-12 text-center">
                Cierre esta pestaña del navegador.
            </div>
        </div>
        <?php
    }
} else {
?>
    <div class="alert alert-danger" role="alert">
        <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
        <span><strong>ERROR AL INGRESAR</strong></span>
    </div>        
<?php
}