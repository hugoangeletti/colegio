<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../html/head.php');
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/cajaDiariaLogic.php');
require_once ('../../dataAccess/mesaEntradaEspecialistaLogic.php');
require_once ('../../dataAccess/cursoLogic.php');

require_once('../../tcpdf/config/lang/spa.php');
require_once('../../tcpdf/tcpdf.php');

class MYPDF extends TCPDF 
{
        //Page header
        public function Header() 
        {
            /*
                // Logo
                $image_file = '../../public/images/logo_colmed1_lg.png';
                $this->Image($image_file, 10, 5, 170, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
                 // Set font
                $this->SetFont('helvetica', 'B', 20);
                // Title
                $this->Cell(0, 15, '', 0, false, 'C', 0, 'Nota', 0, false, 'M', 'M');

                //MARCA DE AGUA 
                $bMargin = $this->getBreakMargin();
                $auto_page_break = $this->AutoPageBreak;
                $this->SetAutoPageBreak(false, 0);

                $img_file2 = '../../public/images/fondoCertificadoClaro.jpg';
                $this->Image($img_file2, 15, 25, 180, 180, '', '', 'C', false, 300, '', false, false, 0);
                $this->SetAutoPageBreak($auto_page_break, $bMargin);
                $this->setPageMark();
                //FIN MARCA DE AGUA 
        
             * 
             */
        }

        // Page footer
        public function Footer() {
                // Position at 15 mm from bottom
                //$this->SetY(-15);
                // Set font
                //$this->SetFont('helvetica', 'I', 8);

                //$this->Cell(0, 10, 'Relaciones con la comunidad', 0, false, 'C', 0, '', 0, false, 'T', 'M');
                //$this->Ln(3);
                // Page number
                //$this->Cell(0, 5, 'Pag. '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }

}
?>
<?php
$continua = TRUE;
if (isset($_GET['id']) && $_GET['id'] <> "") {
    $idCajaDiariaMovimiento = $_GET['id'];
} else {
    $continua = FALSE;
    $mensaje .= "Falta id. ";
}

if ($continua) {
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
        
        //obtengo detalle del recibo
        $resDetalle = obtenerCajaDiariaMovimientoDetallePorId($idCajaDiariaMovimiento);
        if ($resDetalle['estado'] && $resDetalle['datos']) {
            $cajaDiariaMovimientoDetalle = $resDetalle['datos'];
        } else {
            $continua = FALSE;
            $resultado['mensaje'] = $resColegiado['mensaje'];
        }        
    } else {
        $continua = FALSE;
        $resultado['mensaje'] = $resExpediente['mensaje'];
    }
} else {
    $continua = FALSE;
    $resultado['mensaje'] = 'FALTAN DATOS';
}
if ($continua){
    //armo el html con el certificado
    $pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
    $pdf->SetPrintHeader(true);
    $pdf->SetPrintFooter(true);
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetFooterMargin(0);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //imprimo la planilla
    $image_file = '../../public/images/logo_colmed1_hr.png';
    $image_file = '../../public/images/EscudoRecibo.JPG';

    $i = 1;
    while ($i <= 2) {
        $ori_dup = "ORIGINAL";
        if ($i == 2) {
            $ori_dup = "DUPLICADO";
        }
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();

        $pdf->Line(100, 5, 100, 52, array('width' => 0));
        $pdf->Image($image_file, 5, 5, 25, 25, 'jpeg', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->MultiCell(0, 5, 'Colegio de Médicos', 0, 'L', false, 0, '30', '');
        $pdf->MultiCell(0, 7, 'RECIBO', 0, 'L', false, 1, '105', '');
        $pdf->MultiCell(0, 5, 'de la Provincia', 0, 'L', false, 0, '30', '');
        $pdf->MultiCell(0, 7, 'Nº '.rellenarCeros($numeroRecibo, 8), 0, 'L', false, 1, '105', '');
        //$pdf->MultiCell(0, 7, $ori_dup, 0, 'L', false, 1, '140', '');
        $pdf->MultiCell(0, 5, 'de Buenos Aires', 0, 'L', false, 0, '30', '');
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->MultiCell(0, 7, 'Fecha: '. cambiarFechaFormatoParaMostrar($fechaPago), 0, 'L', false, 1, '105', '');
        $pdf->MultiCell(0, 5, 'Distrito I', 0, 'L', false, 0, '30', '');
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->MultiCell(0, 5, 'CUIT Nº: 30-54078002-8', 0, 'L', false, 1, '105', '');
        $pdf->MultiCell(0, 5, 'Calle 51 Nº723 - Tel. 4256311 / 4232731', 0, 'L', false, 0, '10', '');
        $pdf->MultiCell(0, 5, 'Ingresos Brutos: EXENTO', 0, 'L', false, 1, '105', '');
        $pdf->MultiCell(0, 5, '(1900) La Plata - Pcia. Bs.As ', 0, 'L', false, 0, '10', '');
        $pdf->MultiCell(0, 5, 'Caja Prev. Nº: 30-54078002-8', 0, 'L', false, 1, '105', '');
        $pdf->MultiCell(0, 5, 'tesoreria@colmed1.org.ar ', 0, 'L', false, 0, '10', '');
        $pdf->MultiCell(0, 5, 'IVA EXENTO', 0, 'L', false, 1, '105', '');
        $pdf->MultiCell(0, 5, 'www.colmed1.org.ar ', 0, 'L', false, 0, '10', '');
        $pdf->MultiCell(0, 5, 'Exceptuado Cumplimiento R.G. 1415 Anexo I Ap."A" inc.k', 0, 'L', false, 1, '105', '');
        $pdf->Ln(2);
        //ARMAMOS EL HTML
        $pdf->Line(0, 52, 220, 52, array('width' => 0));
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->MultiCell(0, 5, 'Apellido y Nombre: '.$apellidoNombre, 0, 'L', false, 0, '10', '');
        if (isset($matricula)) {
            $pdf->MultiCell(0, 5, 'Matrícula: '.$matricula, 0, 'L', false, 1, '160', '');        
        } else {
            $pdf->MultiCell(0, 5, '', 0, 'L', false, 1, '10', '');        
        }
        $domicilio = 'calle';
        $pdf->MultiCell(0, 5, 'Domicilio: '.$domicilio, 0, 'L', false, 1, '10', '');
        $pdf->Ln(2);
        $pdf->SetFont('dejavusans', 'B', 8);
        $pdf->Line(0, 63, 220, 63, array('width' => 0));
        $pdf->MultiCell(0, 5, 'Concepto', 0, 'L', false, 0, '10', '');
        $pdf->MultiCell(0, 5, 'Importe', 0, 'L', false, 1, '160', '');
        $pdf->Line(0, 70, 220, 70, array('width' => 0));
        $pdf->Ln(2);
        foreach ($cajaDiariaMovimientoDetalle as $dato){
            $idCajaDiariaMovimientoDetalle = $dato['idCajaDiariaMovimientoDetalle'];
            $indice = $dato['indice'];
            $monto = $dato['monto'];
            $codigoPago = $dato['codigoPago'];
            $tipoPago = $dato['tipoPago'];
            $detalle = "";
            switch ($codigoPago) {
                case '1':
                case '3':
                    if (isset($dato['periodo'])) {
                        $periodo = $dato['periodo'];
                        $detalle .= ' - Período: '.$periodo;
                    }
                    if (isset($dato['cuota'])) {
                        $cuota = $dato['cuota'];
                        if ($cuota > 0) {
                            $detalle .= ' /Cuota: '.$cuota;
                        } else {
                            $detalle .= ' / PAGO TOTAL';
                        }
                    }
                    break;
                 
                case '2':
                    if (isset($dato['cuota'])) {
                        $cuota = $dato['cuota'];
                    } else {
                        $cuota = 'sin discriminar';
                    }
                    $detalle .= ' - Número: '.$indice.' /Cuota: '.$cuota;
                    break;

                case '10':
                    if (isset($dato['cuota'])) {
                        $cuota = $dato['cuota'];
                    } else {
                        $cuota = 'sin discriminar';
                    }
                    $resCurso = obtenerNombreCursoAsistente($idAsistente);
                    if ($resCurso['estado']) {
                        $detalle .= $resCurso['titulo'].' - Cuota: '.$cuota;    
                    } else {
                        $detalle .= ' - Cuota: '.$cuota;
                    }
                    
                    break;

                default:
                    if ($detalle == "") {
                        //si es por especialista, busco el nombre de la especialidad
                        $arrayTipoPago = array('72', '38', '59', '37', '82', '52', '55', '61');
                        if (in_array($codigoPago, $arrayTipoPago)) {
                            $resEspecialidad = obtenerEspecialidadPorIdMesaEntrada($indice);
                            if ($resEspecialidad['estado']) {
                                $detalle = '('.$resEspecialidad['datos']['nombreEspecialidad'].')';
                                if (isset($resEspecialidad['datos']['incisoArticulo8']) && $resEspecialidad['datos']['incisoArticulo8'] <> "") {
                                    $detalle .= $resEspecialidad['datos']['incisoArticulo8'];
                                }
                            } else {
                                $detalle = '';    
                            }
                        }
                    }
                    break;
            }
            
            $pdf->SetFont('dejavusans', '', 8);
            $pdf->MultiCell(0, 5, $codigoPago.'-'.$tipoPago.' '.$detalle, 0, 'L', false, 0, '10', '');
            $pdf->MultiCell(0, 5, $monto, 0, 'L', false, 1, '160', '');
        }
        $pdf->Line(0, 243, 220, 243, array('width' => 0));
        $pdf->SetXY(110, 245);
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->MultiCell(0, 5, 'TOTAL A PAGAR: $'.$totalRecibo, 0, 'L', false, 0, '120', '');
        $pdf->Line(0, 250, 220, 250, array('width' => 0));

        $pdf->SetXY(110, 260);
        $pdf->SetFont('dejavusans', '', 8);
        $pdf->MultiCell(50, 5, 'Realizó: '.$_SESSION['user'], 0, 'L', false, 0, '35', '');
        $pdf->MultiCell(80, 5, 'Emitido el: '.date('d/m/Y H:i:s'), 0, 'L', false, 0, '140', '');
        $pdf->lastPage();

        $i++;
    }
    ob_clean();
    /* Finalmente generamos el PDF */
    $destination = 'I';
    $nombreArchivo = 'Recibo_'.$tipoRecibo.'_'.$numeroRecibo.'.pdf';
    $pdf->Output($nombreArchivo, $destination);        
} else {
    echo "error en los datos ingresados";
}
