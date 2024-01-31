<?php
require_once ('../dataAccess/config.php');
permisoLogueado();
//require_once ('../html/head.php');
//require_once ('../html/header.php');
require_once ('../dataAccess/funcionesConector.php');
require_once ('../dataAccess/funcionesPhp.php');
require_once ('../dataAccess/cajaDiariaLogic.php');
require_once ('../tcpdf/config/lang/spa.php');
require_once ('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF 
{
        //Page header
        public function Header() 
        {
            //global $title;
                // Logo
                //$image_file = '../public/images/logo_colmed1_lg.png';
                //$this->Image($image_file, 10, 5, 170, 20, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
                 // Set font
                $this->SetFont('helvetica', 'B', 10);
                // Title
                $this->Ln(4);
                $this->Cell(0, 15, TITULO_CAJA, 0, false, 'L', 0, '', 0, false, 'M', 'M');              
                $this->Ln(5);

                $this->SetFont('helvetica', 'B', 10);
                $this->Line(0, 14, 220, 14, array('width' => 0));
                $this->MultiCell(0, 7, 'Código', 0, 'L', false, 0, '10', '');
                $this->MultiCell(0, 7, 'Concepto', 0, 'L', false, 0, '30', '');    
                $this->MultiCell(50, 7, 'Importe', 0, 'R', false, 0, '80', '');
                $this->MultiCell(0, 7, 'Cuenta', 0, 'L', false, 0, '180', '');
                $this->Line(0, 19, 220, 19, array('width' => 0));

                /*
                //MARCA DE AGUA 
                $bMargin = $this->getBreakMargin();
                $auto_page_break = $this->AutoPageBreak;
                $this->SetAutoPageBreak(false, 0);

                $img_file2 = '../../public/images/fondoCertificadoClaro.jpg';
                $this->Image($img_file2, 15, 25, 180, 180, '', '', 'C', false, 300, '', false, false, 0);
                $this->SetAutoPageBreak($auto_page_break, $bMargin);
                $this->setPageMark();
                //FIN MARCA DE AGUA 
                */
        }

        // Page footer
        public function Footer() {
                // Position at 15 mm from bottom
                $this->SetY(-15);
                // Set font
                $this->SetFont('helvetica', 'I', 8);

                //$this->Cell(0, 10, 'Relaciones con la comunidad', 0, false, 'C', 0, '', 0, false, 'T', 'M');
                //$this->Ln(3);
                // Page number
                $this->Cell(0, 5, 'Pag. '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }

}

$continua = TRUE;
$mensaje = "";
if (isset($_GET['id']) && $_GET['id'] <> "") {
    $idCajaDiaria = $_GET['id'];
    $resCajaDiaria = obtenerCajaDiariaPorId($idCajaDiaria);
    if ($resCajaDiaria['estado']) {
        $cajaDiaria = $resCajaDiaria['datos'];
        $fechaCaja = $cajaDiaria['fechaApertura'];
        define('TITULO_CAJA', 'Totales por concepto - Caja diaria N° '.$idCajaDiaria.' del día '.cambiarFechaFormatoParaMostrar($fechaCaja));
    } else {
        $continua = FALSE;
        $mensaje .= $resRecibo['mensaje'];
    }
} else {
    $continua = FALSE;
    $mensaje .= "Falta idCajaDiariaMovimiento";
}

if ($continua) {
    $resMovimientosCaja = obtenerCajaDiariaResumenCuenta($idCajaDiaria);
    if ($resMovimientosCaja['estado']) {
        $pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(true);
        $pdf->SetPrintFooter(true);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-12, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //imprimo la planilla
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->AddPage();

        //$pdf->MultiCell(0, 7, 'Caja del día '.cambiarFechaFormatoParaMostrar($fechaCaja), 0, 'L', false, 0, '30', '');
        //$pdf->MultiCell(0, 7, 'Número de Caja '.$idCajaDiaria, 0, 'L', false, 1, '100', '');
        //$pdf->Ln(2);
        $pdf->Ln(4);
        $pdf->SetFont('dejavusans', '', 10);
        $totalRecaudacion = 0;
        foreach ($resMovimientosCaja['datos'] as $dato){
            $concepto = $dato['concepto'];
            $codigoPago = $dato['codigoPago'];
            $cuentaContable = $dato['cuentaContable'];
            $totalConcepto = $dato['totalConcepto'];
            $totalRecaudacion += $totalConcepto;
            $pdf->MultiCell(0, 5, $tipo.' '.rellenarCeros($codigoPago, 3), 0, 'L', false, 0, '10', '');
            $pdf->MultiCell(0, 5, $concepto, 0, 'L', false, 0, '30', '');    
            $pdf->MultiCell(50, 5, number_format($totalConcepto, 2, '.', ','), 0, 'R', false, 0, '80', '');
            $pdf->MultiCell(0, 5, $cuentaContable, 0, 'L', false, 1, '180', '');
            //$pdf->Ln(5);
        }
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->MultiCell(0, 7, 'Total de Recaudacion: '.number_format($totalRecaudacion, 2, '.', ','), 0, 'L', false, 0, '10', '');
        $pdf->SetFont('dejavusans', '', 10);
        ob_clean();
        //echo 'guardar recibo -> '.$nombreArchivo;
        $pdf->Output('CajaDiaria_Conceptos_'.$idCajaDiaria, 'I');       
    } else {
        echo $resMovimientosCaja['mensaje'];
    }
    ?>
<?php
} else {
    echo 'error de Acceso';
}

