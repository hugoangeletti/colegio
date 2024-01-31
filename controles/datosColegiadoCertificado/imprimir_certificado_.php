<?php
require_once ('../../dataAccess/config.php');
permisoLogueado();
require_once ('../../html/head.php');
require_once ('../../dataAccess/funcionesConector.php');
require_once ('../../dataAccess/funcionesPhp.php');
require_once ('../../dataAccess/colegiadoLogic.php');
require_once ('../../dataAccess/colegiadoEspecialistaLogic.php');
require_once ('../../dataAccess/colegiadoCertificadosLogic.php');
require_once ('../../dataAccess/colegiadoContactoLogic.php');
require_once ('../../dataAccess/colegiadoMovimientoLogic.php');
require_once ('../../dataAccess/colegiadoSancionLogic.php');
require_once ('../../dataAccess/colegiadoCargoLogic.php');
require_once ('../../dataAccess/colegiadoContactoLogic.php');
require_once ('../../dataAccess/colegiadoDomicilioLogic.php');
require_once ('../../dataAccess/colegiadoFapLogic.php');
require_once ('../../dataAccess/colegiadoDeudaAnualLogic.php');
require_once ('../../dataAccess/presidenteLogic.php');
require_once ('../../dataAccess/notaCambioDistritoLogic.php');
require_once ('../../dataAccess/tipoCertificadoLogic.php');
require_once ('../../dataAccess/colegiadoArchivoLogic.php');

require_once('../../tcpdf/config/lang/spa.php');
require_once('../../tcpdf/tcpdf.php');

//require_once('../../TCPDF-php8-main/tcpdf.php');


class MYPDF extends TCPDF 
{
        //Page header
        public function Header() 
        {
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
        
        }

        // Page footer
        public function Footer() {
                // Position at 15 mm from bottom
                //$this->SetY(-10);
                $this->SetY(-15);
                // Set font
                $this->SetFont('dejavusans', '', 8);

                $this->MultiCell(180, 0, 'Este certificado fue emitido en forma online desde el sistema del Colegio de Médicos Pcia.de Bs.As – Distrito I. Debe ser recibido por los organismos que lo requieran. Validez del certificado: 30 días a partir de la fecha de la firma. ', 1, 'L', 0, 0, '', '', true, 0, false, true, 40, 'T');
                //$this->Cell(180, 0, 'La fotocopia de éste certificado no tiene validez', 1, false, 'C', 0, '', 0, false, 'T', 'M');
                //$this->Ln(3);
                // Page number
                //$this->Cell(0, 5, 'Pag. '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }

}

?>
<?php
$continua = TRUE;
if (isset($_GET['idCertificado'])) {
    $idCertificado = $_GET['idCertificado'];
    $resCertificado = obtenerCertificadoPorId($idCertificado);
    if ($resCertificado['estado']) {
        $certificado = $resCertificado['datos'];
        
        $idColegiado = $certificado['idColegiado'];
        $idTipoCertificado = $certificado['idTipoCertificado'];

        //si es para especialista, verifico que esté seleccioanda la especialidad
        $idColegiadoEspecialista = NULL;
        if ($idTipoCertificado == 3 && isset($certificado['idColegiadoEspecialista'])) {
            $idColegiadoEspecialista = $certificado['idColegiadoEspecialista'];
        }
    
        //si es para cambio de distrito, verifico que venga seleccioando el distrito y la nota
        $distrito = NULL;
        $idNotaCambioDistrito = NULL;
        if ($idTipoCertificado == 1 && isset($certificado['distrito']) && isset($certificado['idNotaCambioDistrito'])) {
            $distrito = $certificado['distrito'];
            $idNotaCambioDistrito = $certificado['idNotaCambioDistrito'];
        }

        //si envia por mail, verifica que esta cargado el mail
        $enviaMail = $certificado['envioMail'];
        $mailDestino = $certificado['mail'];
        $mail = NULL;
        if ($enviaMail == 'S') {
            //obtengo el mail del colegiado
            $resContacto = obtenerColegiadoContactoPorIdColegiado($idColegiado);
            if ($resContacto['estado']) {
                $contacto = $resContacto['datos'];
                $mail = $contacto['email'];
            }
        }
        
        $presentado = strtoupper($certificado['presentado']);
        $cuotasAdeudadas = $certificado['cuotasAdeudadas'];
        $conFirma = $certificado['conFirma'];
        $conLeyendaTeso = $certificado['conLeyendaTeso'];
        $codigoDeudor = $certificado['estadoConTesoreria'];
        $hash_qr = $certificado['hash_qr'];
        
        //obtengo el estado con tesoreria
        $resEstadoTesoreria = estadoTesoreria($codigoDeudor);
        if ($resEstadoTesoreria['estado']){
            $estadoConTesoreria = $resEstadoTesoreria['estadoTesoreria'];
        } else {
            $continua = FALSE;
            $resultado['mensaje'] = $resEstadoTesoreria['mensaje'];
        }                

        //obtengo el detalle del tipo de certificado
        $resTipoCertificado = obtenerTipoCertificadoPorId($idTipoCertificado);
        if ($resTipoCertificado['estado']) {
            $tipoCertificado = $resTipoCertificado['datos']['detalle'];
        } else {
            $continua = FALSE;
            $resultado['mensaje'] = $resTipoCertificado['mensaje'];
        }
    } else {
        $resultado['mensaje'] = $resCertificado['mensaje'];
        $continua = FALSE;
    }
} else {
    $continua = FALSE;
}

if (!$continua){
    $resultado['mensaje'] = "ERROR AL GENERAR EL CERTIFICADO";
    $resultado['icono'] = "glyphicon glyphicon-remove";
    $resultado['clase'] = "alert alert-error";
}

?>
<!--<body onLoad="document.forms['myForm'].submit()">-->
    <?php
    if ($continua){
        //armo el html con el certificado
        $resColegiado = obtenerColegiadoPorId($idColegiado);
        if ($resColegiado['estado'] && $resColegiado['datos']) {
            $colegiado = $resColegiado['datos'];
            $matricula = $colegiado['matricula'];
            $estadoMatricular = $colegiado['estado'];
            $sexo = $colegiado['sexo'];
            switch ($colegiado['tipoEstado']) {
                case 'A':
                    $tipoEstadoMatricular = 'ACTIVO';
                    $conEstadoTesoreria = TRUE;
                    if ($cuotasAdeudadas>=1 && $cuotasAdeudadas<=5 ) {
                        $conEstadoTesoreria = FALSE;
                    }
                    break;

                case 'I':
                    $tipoEstadoMatricular = 'Inscripto';
                    $conEstadoTesoreria = FALSE;
                    break;

                case 'C':
                    $tipoEstadoMatricular = 'BAJA('.$colegiado['movimientoCompleto'].')';
                    $conEstadoTesoreria = TRUE;
                    break;

                case 'F':
                case 'J':
                    $tipoEstadoMatricular = 'BAJA('.$colegiado['movimientoCompleto'].')';
                    $conEstadoTesoreria = FALSE;
                    break;

                default:
                    $tipoEstadoMatricular = '-';
                    $conEstadoTesoreria = TRUE;
                    break;
            } 
            $libro = $colegiado['tomo'];
            $folio = $colegiado['folio'];
            $tipoDocumento = $colegiado['tipoDocumento'];
            $numeroDocumento = $colegiado['numeroDocumento'];
            $fechaMatriculacion = cambiarFechaFormatoParaMostrar($colegiado['fechaMatriculacion']);
            $fechaNacimiento = cambiarFechaFormatoParaMostrar($colegiado['fechaNacimiento']);
            
            $resColegiadoTitulo = obtenerTitulosPorColegiado($idColegiado);
            if ($resColegiadoTitulo['estado']) {
                $colegiadoTitulo = $resColegiadoTitulo['datos'];
                $fechaTitulo = cambiarFechaFormatoParaMostrar($colegiadoTitulo['fechaTitulo']);
                $tituloColegiado = $colegiadoTitulo['tipoTitulo'];
                $universidad = $colegiadoTitulo['universidad'];
            }
            
            $resArchivos = obtenerColegiadoArchivo($idColegiado, '1');
            if ($resArchivos['estado'] && isset($resArchivos['datos'])){
                $archivos = $resArchivos['datos'];
                $fileFoto = trim($archivos['nombre']);
                // insertamos la foto y firma
                $foto = @fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Fotos/".$fileFoto, "rb");
                if ($foto) {
                    $contents=stream_get_contents($foto);
                    fclose ($foto);

                    $fotoVer = base64_encode($contents);
                    $tieneFotoFirma = TRUE;
                } 
                //$foto = "file://192.168.2.50/colegio/Archivos/Fotos//".$fileFoto;
                //$foto = "../../archivos/foto/".$fileFoto;
                $tieneFotoFirma = TRUE;
            }
            
            //ARMAMOS EL HTML
            $conNota = TRUE;
            switch ($idTipoCertificado) {
                case 1: //cambio de distrito
                    $resPresidenteDistrito = obtenerPresidenteDistrito($distrito);
                    if ($resPresidenteDistrito['estado']) {
                        $presidenteDistrito = $resPresidenteDistrito['datos']['nombre'];
                        $distritoCambio = $resPresidenteDistrito['datos']['romanos'];
                    } else {
                        $presidenteDistrito = '';
                        $distritoCambio = '';
                    }
                    $html = 'Señor<br>'.
                        'Presidente del Consejo Directivo<br>'.
                        'del Colegio de Médicos, Distrito '.$distritoCambio.'<br>'.
                        'Doctor '.$presidenteDistrito.'<br><br>'.
                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                        'Tengo el agrado de dirigirme a usted, a los efectos de remitirle los datos '.
                        'registrados en este Colegio de Médicos Distrito I pertenecientes ';
                    if ($sexo <> 'F') {
                        $html .= 'al profesional médico ';
                    } else {
                        $html .= 'a la profesional médica ';
                    }
                    $html .= '<b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b>  con <b>'.$tipoDocumento.' '.$numeroDocumento.'</b>, con motivo de su inscripción en ese Distrito '.$distritoCambio.'.';
                    
                    /*
                    a pedido del profesional y autorizado por mesa se le agrega nota a la matricula 117528
                    */
                    $notaUnica = "";
                    if ($matricula == 117528) {
                        $notaUnica = '<p style="line-height: 15em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.'Asimismo, <b>se aclara que el profesional médico DR. FERNANDO JAVIER RUIZ POUYTE, presentó nuevo DIPLOMA DE MEDICO con fecha de expedición 10/03/2014 en reemplazo del anterior que perdiera en la inundación de la ciudad de La Plata</b>.-';
                    }
                    if ($matricula == 119153) {
                        $notaUnica = '<p style="line-height: 15em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.'Asimismo, <b>se aclara que el profesional médico DR. ALAN CHRISTIAN CANTO, presentó nuevo DIPLOMA DE MEDICO con fecha de expedición 20/02/2018 en reemplazo del anterior que perdiera en la inundación de la ciudad de La Plata</b>.-';
                    }

                    $conDetalle = TRUE;
                    $conDomicilio = TRUE;
                    $cantidadCertificados = 1;
                    break;

                case 3: //especialista y recertificacion
                    $resEspecialista = obtenerColegiadoEspecialistaPorId($idColegiadoEspecialista);
                    if ($resEspecialista['estado']) {
                        $especialista = $resEspecialista['datos'];
                        $especialistaDistrito = $especialista['distritoOrigen'];
                        $distritoOrigen = $especialista['distritoOrigen'];
                        if ($distritoOrigen == "NACIÓN") {
                            $especialistaDistrito = 1;
                        }
                        $tipoEspecialidad = $especialista['tipoEspecialidad']; 
                        $especialidad = $especialista['nombreEspecialidad'];
                        $fechaEspecialista = $especialista['fechaEspecialista'];
                        $fechaRecertificacion = $especialista['fechaRecertificacion'];
                        $fechaVencimiento = $especialista['fechaVencimiento'];
                        
                        $html = '<br><p style="line-height: 20em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                            'A pedido del interesado y a los efectos que mejor corresponda se deja '.
                            'expresa constancia que el Consejo Directivo del Colegio de Médicos Distrito '.$especialistaDistrito.' '.
                            'ha resuelto autorizar al uso del Título de '.$tipoEspecialidad.' en <b>'.
                            $especialidad.'</b>';
                        if ($sexo <> 'F') {
                            $html .= ' al profesional médico ';
                        } else {
                            $html .= ' a la profesional médica ';
                        }
                        $html .= '<b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b> '.
                                'matrícula Nº <b>'.$matricula.'</b> con fecha <b>'.  cambiarFechaFormatoParaMostrar($fechaEspecialista).'</b>.-</p>';

                        $cantidadCertificados = 1;
                        //si recertificó lo imprimo
                        //if ($fechaEspecialista < $fechaRecertificacion) {
                        //    $html .= '<br><p style="text-align: center;">Recertificación: '.  cambiarFechaFormatoParaMostrar($fechaRecertificacion).'</p>';
                        //}
                            
                        //Fecha de Caducidad, si es del I, verifica que la fecha sea mayor a 27/09/1994 y no sea Consultor
                        if ($distritoOrigen <> "NACIÓN") {
                            $resFechaPorTipo = obtenerFechaJerarquizadoConsultor($idColegiadoEspecialista, JERARQUIZADO);
                            if ($resFechaPorTipo['estado']) {
                                $esJerarquizado = TRUE;
                                /*
                                $htmlJerarquizado = '<br><p style="line-height: 20em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                                    'A pedido del interesado y a los efectos que mejor corresponda se deja '.
                                    'expresa constancia que el Consejo Directivo del Colegio de Médicos Distrito '.$especialistaDistrito.' '.
                                    'ha resuelto autorizar al uso del Título de Especialista Jerarquizado en <b>'.
                                    $especialidad.'</b>';
                                if ($sexo <> 'F') {
                                    $htmlJerarquizado .= 'al profesional médico ';
                                } else {
                                    $htmlJerarquizado .= 'a la profesional médica ';
                                }
                                $htmlJerarquizado .= '<b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b> '.
                                        'matrícula Nº <b>'.$matricula.'</b> con fecha <b>'.  cambiarFechaFormatoParaMostrar($resFechaPorTipo['fecha']).'</b>.-</p>';
                                 * 
                                 */
                                $htmlJerarquizado .= 'Título de Especialista Jerarquizado con fecha <b>'.  cambiarFechaFormatoParaMostrar($resFechaPorTipo['fecha']).'</b>.-</p>';
                                /*
                                $cantidadCertificados = 2;
                                //imprimo el estado matricular
                                $htmlJerarquizado .= '<br>Estado de la matrícula: '.$tipoEstadoMatricular;
                                $htmlJerarquizado .= '<br>Situación con tesorería: '.$estadoConTesoreria;                            
                                 * 
                                 */
                                $cantidadCertificados = 1;
                            } else {
                                $esJerarquizado = FALSE;
                                $htmlJerarquizado = '';
                            }
                            $resFechaPorTipo = obtenerFechaJerarquizadoConsultor($idColegiadoEspecialista, CONSULTOR);
                            if ($resFechaPorTipo['estado']) {
                                $esConsultor = TRUE;
                                /*
                                $htmlConsultor = '<br><p style="line-height: 20em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                                    'A pedido del interesado y a los efectos que mejor corresponda se deja '.
                                    'expresa constancia que el Consejo Directivo del Colegio de Médicos Distrito '.$especialistaDistrito.' '.
                                    'ha resuelto autorizar al uso del Título de especialista Consultor en <b>'.
                                    $especialidad.'</b>';
                                if ($sexo <> 'F') {
                                    $htmlConsultor .= 'al profesional médico ';
                                } else {
                                    $htmlConsultor .= 'a la profesional médica ';
                                }
                                $htmlConsultor .= '<b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b> '.
                                        'matrícula Nº <b>'.$matricula.'</b> con fecha <b>'.  cambiarFechaFormatoParaMostrar($resFechaPorTipo['fecha']).'</b>.-</p>';
                                 * 
                                 */
                                $htmlConsultor .= 'Título de Especialista Consultor con fecha <b>'.  cambiarFechaFormatoParaMostrar($resFechaPorTipo['fecha']).'</b>.-</p>';
                                /*
                                //imprimo el estado matricular
                                $htmlConsultor .= '<br>Estado de la matrícula: '.$tipoEstadoMatricular;
                                $htmlConsultor .= '<br>Situación con tesorería: '.$estadoConTesoreria;
                                $cantidadCertificados = 3;
                                 * 
                                 */
                                $cantidadCertificados = 1;
                            } else {
                                $esConsultor = FALSE;
                                $htmlConsultor = '';
                            }
                        }

                        if ($especialistaDistrito == 1) {
                            if ($esConsultor) {
                                $fechaCaducidad = NULL;
                            } else {
                                if ($fechaEspecialista > '1994-09-27') {
                                    $fechaCaducidad = $fechaVencimiento;
                                } else {
                                    $fechaCaducidad = NULL;
                                }
                            }
                        } else {
                            $fechaCaducidad = $fechaVencimiento;
                        }
                        
                        //si tiene caducidad la imprimo
                        if (isset($fechaCaducidad) && $fechaCaducidad != '0000-00-00') {
                            $fechaActual = date('Y-m-d');
                            if ($fechaActual > $fechaVencimiento) {
                                //verifica si esta dentro de los 2 años permitidos a Recertificar
                                //$dia = date($fechaVencimiento, 'd');
                                //$mes = date($fechaVencimiento, 'm');
                                //$anio = date($fechaVencimiento, 'Y') + 2;
                                //$fechaLimite = $anio.'-'.$mes.'-'.$dia;
                                $fechaLimite = sumarRestarSobreFecha($fechaVencimiento, 2, 'year', '+');
                                if ($fechaActual > $fechaLimite) {
                                    if ($distritoOrigen <> "NACIÓN") {
                                        $caduca = 'Especialidad No Recertificada';
                                    } else {
                                        $caduca = 'Especialidad No Renovada';
                                    }
                                } else {
                                    if ($distritoOrigen <> "NACIÓN") {
                                        $caduca = 'Especialidad con Recertificación en Trámite';
                                    } else {
                                        $caduca = 'Especialidad con Renovación en Trámite';
                                    }
                                }
                            } else {
                                if ($distritoOrigen <> "NACIÓN") {
                                    $caduca = 'Recertificada hasta: ';
                                } else {
                                    $caduca = 'Renovada hasta: ';
                                }
                                $caduca .= cambiarFechaFormatoParaMostrar($fechaVencimiento);
                                //$dia = date($fechaVencimiento, 'd');
                                //$mes = date($fechaVencimiento, 'm');
                                //$anio = date($fechaVencimiento, 'Y') - 5;
                                //$fechaLimite = $anio.'-'.$mes.'-'.$dia;
                                $fechaLimite = sumarRestarSobreFecha($fechaVencimiento, 5, 'year', '-');
                                if ($fechaEspecialista == $fechaLimite) {
                                    $caduca = 'Certificada hasta: '.cambiarFechaFormatoParaMostrar($fechaVencimiento);
                                }
                            }
                            $html .= '<br><p style="text-align: center;">'.$caduca.'</p>';
                        }
                    } 
                    //imprimo jerarquizado y/o consultor si lo tienen
                    if ($esJerarquizado) {
                        $html .= '<br>'.$htmlJerarquizado;
                    }
                    
                    if ($esConsultor) {
                        $html .= '<br>'.$htmlConsultor;
                    }
                    
                    //imprimo el estado matricular
                    $html .= '<br>Estado de la matrícula: '.$tipoEstadoMatricular;
                    $html .= '<br>Situación con tesorería: '.$estadoConTesoreria;
                    $conDetalle = FALSE;

                    break;

                case 5: //Para comisiones
                    $html = '<br><p style="text-align: center"><b>CERTIFICADO PARA COMISIONES</b></p>'
                            . '<br><br><br>'
                            . 'Comisión: '.$presentado.'<br><br>';
                    $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                            'Tengo el agrado de dirigirme a usted, a los efectos de remitirle los datos '.
                            'registrados en este Colegio de Médicos Distrito I pertenecientes ';
                    if ($sexo <> 'F') {
                        $html .= 'al profesional médico ';
                    } else {
                        $html .= 'a la profesional médica ';
                    }
                    $html .= '<b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b>  con <b>'.$tipoDocumento.' '.$numeroDocumento.'</b>, ';
                    $conDetalle = TRUE;
                    $conNota = FALSE;
                    $conDomicilio = TRUE;
                    $cantidadCertificados = 1;
                    break;
                
                case 6: //a todo efecto
                case 10: //Para la CAJA
                case 11: //Para la AMP
                case 13: //Para el Ministerio de Salud
                case 14: //Para la Superintendencia de Seguros
                case 15: //Para RPP
                case 16: //Para la AMBerisso
                case 17: //Para la AMEnsenada
                case 18: //Para Exterior
                case 19: //Para IOMA
                    if ($estadoMatricular == 2 || $estadoMatricular == 3) {
                        $html = '<p style="line-height: 15em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                            'A los efectos de ser presentado ante <b>&nbsp;'.$presentado.'</b> se deja expresa constancia que la <b>M.P. '.$matricula.'</b> '.
                            'perteneciente ';
                        if ($sexo <> 'F') {
                            $html .= 'al profesional médico';
                        } else {
                            $html .= 'a la profesional médica';
                        }
                        $html .= ', <b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b> con <b>'.$tipoDocumento.' '.$numeroDocumento.'</b>, '.
                            'se encuentra dada de <b>BAJA</b> en este Colegio de Médicos de la Pcia. de Bs. As. Distrito I. '.
                            'Registrándose a la fecha los siguientes antecedentes: </p>';
                    } else {
                        $html = '<p style="line-height: 15em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                            'A los efectos de ser presentado ante <b>&nbsp;'.$presentado.'</b> se deja expresa constancia que, ';
                        if ($sexo <> 'F') {
                            $html .= 'del profesional médico';
                        } else {
                            $html .= 'de la profesional médica';
                        }
                        $html .= ', <b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b> con <b>'.$tipoDocumento.' '.$numeroDocumento.'</b>, se registran en este Colegio de Médicos de la Pcia. de Bs. As. Distrito I, a la fecha los siguientes antecedentes: </p>';
                    }

                    $conDetalle = TRUE;
                    if ($idTipoCertificado == 6) {
                        $conDomicilio = FALSE;
                    } else {
                        if ($idTipoCertificado == 5) {
                            //si es para comisiones (5) no se emite la nota al pie
                            $conNota = FALSE;
                        }
                        $conDomicilio = TRUE;
                    }

                    /*
                    a pedido del profesional y autorizado por mesa se le agrega nota a la matricula 117528
                    */
                    $notaUnica = "";
                    if ($matricula == 117528) {
                        $notaUnica = '<p style="line-height: 15em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.'Asimismo, <b>se aclara que el profesional médico DR. FERNANDO JAVIER RUIZ POUYTE, presentó nuevo DIPLOMA DE MEDICO con fecha de expedición 10/03/2014 en reemplazo del anterior que perdiera en la inundación de la ciudad de La Plata</b>.-';
                    }
                    if ($matricula == 119153) {
                        $notaUnica = '<p style="line-height: 15em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.'Asimismo, <b>se aclara que el profesional médico DR. ALAN CHRISTIAN CANTO, presentó nuevo DIPLOMA DE MEDICO con fecha de expedición 20/02/2018 en reemplazo del anterior que perdiera en la inundación de la ciudad de La Plata</b>.-';
                    }
                    
                    $cantidadCertificados = 1;
                    break;

                case 9: //FAP completo
                    $html = '<p style="line-height: 15em;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                        'Certifico que ';
                        if ($sexo <> 'F') {
                            $html .= 'el profesional médico ';
                        } else {
                            $html .= 'la profesional médica ';
                        }
                        $html .= ' <b>'.$colegiado['nombre'].' '.$colegiado['apellido'].'</b> '.
                        'M.P. Nº <b>'.$matricula.'</b>, matriculado en este Distrito I del Colegio de Médicos goza al día de la '.
                        'fecha de los beneficios otorgados por el Articulo 5º inciso 17) del Decreto-Ley 5413/58, en '.
                        'la redacción dada por la Ley 12043, para eventuales acciones judiciales que tengan como '.
                        'causa los efectos del ejercicio profesional y el acto médico personal y particular realizado '.
                        'dentro de las normas y reglamentos que regulan el ejercicio de la medicina y los cánones '.
                        'médicos habituales, en la jurisdicción de la provincia de Buenos Aires.<br>'.
                        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                        'Se deja expresa constancia que este certificado revela la situación ';
                        if ($sexo <> 'F') {
                            $html .= 'del profesional ';
                        } else {
                            $html .= 'de la profesional ';
                        }
                        $html .= ' al '.
                        'día de la fecha para resultar beneficiario del sistema, sin que refleje en forma alguna la '.
                        'situación del mismo con anterioridad o posterioridad a la emisión del presente.<br>'.
                        '<b> "Artículo 56, Código de Ética Res. C.S. Nº 950/18. Es acto contrario a la ética y '.
                        'constituye falta  grave (con  relación '.
                        'a un establecimiento privado, a uno estatal o a una institución de derecho público '.
                        'no estatal), la acción por la cual el Director médico, el médico propietario, el '.
                        'médico que ejerza alguna función jerárquica en la institución o a los médicos '.
                        'directores o gerentes exigieren, reclamaren o, de cualquier forma pidieran la '.
                        'contratación de un seguro de responsabilidad civil profesional a un colega que '.
                        'trabaje o se desempeñe en ese establecimiento o en la órbita de su sistema '.
                        'asistencial, cualquiera sea la forma contractual en que se desarrolle ese trabajo '.
                        'profesional. También se considerará infracción a la misma exigencia cuando se '.
                        'estableciere como requisito para acceder a la relación de trabajo médico o '.
                        'locación de servicio. Las actuaciones Sumariales podrán ser promovidas de '.
                        'oficio por los Consejos Directivos Distritales. (Res. C.S. Nº540/04).</b></p>';

                    $html .= '<br>Para ser presentado ante quien corresponda.';
                    $conDetalle = FALSE;
                    $conNota = FALSE;
                    $cantidadCertificados = 1;
                    
                    break;

                default:
                    break;
            }

            $pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
            $pdf->SetPrintHeader(true);
            $pdf->SetPrintFooter(true);
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetFooterMargin(20);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            
            $i = 1;
            while  ($i <= $cantidadCertificados) {
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->AddPage();

            $alturaLinea = 6;
            $pdf->Ln(5);

            //imprimir QR
            $style = array(
                    'border' => true,
                    'vpadding' => 'auto',
                    'hpadding' => 'auto',
                    'fgcolor' => array(0,0,0),
                    'bgcolor' => false, //array(255,255,255)
                    'module_width' => 1, // width of a single module in points
                    'module_height' => 1 // height of a single module in points
                );
            //$codigoQR = 'http://www.colmed1.com.ar/portal/controls/certificado.php?id='.$idCertificado.'&colegiado='.$idColegiado.'&tipo='.$idTipoCertificado;
            $codigoQR = 'https://www.colmed1.com.ar/verificar/certificado.php?id='.$hash_qr;
            $pdf->write2DBarcode($codigoQR, 'QRCODE,Q', 32,25,25,25, $style, 'N');
            //fin QR

            //imprimo la planilla
            $pdf->SetFont('dejavusans', '', 10);
            /*
            if ($tieneFotoFirma) {
                $pic = 'data://text/plain;base64,' . base64_encode($contents);
                $pdf->Image($pic , 170 ,25, 25 , 25,'JPG');
                $pdf->Ln(10);
            }
            */
            if ($tieneFotoFirma) {
                $pic = 'data:image/jpg;base64,' . base64_encode($contents);
                $img_base64_encoded = $pic;
                $imageContent = file_get_contents($img_base64_encoded);
                $path = tempnam(sys_get_temp_dir(), 'prefix');

                file_put_contents ($path, $imageContent);

                $img = '<img src="' . $path . '"  width="100" height="100">';
                $pdf->SetXY(170, 25);
                $pdf->writeHTMLCell(0, 0, '', '', $img, '', 1, 0, true, 'R', true);            
            }
            
            $pdf->SetFont('dejavusans', '', 10);
            
            $pdf->MultiCell(0, $alturaLinea, 'Nº '.rellenarCeros($idCertificado, 8), 0, 'L', false, 0, '', '');
            $pdf->MultiCell(0, $alturaLinea, 'La Plata, '.date('d').' de '.obtenerMes(date('m')).' de '.date('Y'), 0, 'R', false, 1, '50', '');
            $pdf->Ln(5);

            ////
            $pdf->SetFont('dejavusans', '', 9);
            switch ($i) {
                case 1:
                    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
                    break;

                case 2:
                    $pdf->writeHTMLCell(0, 0, '', '', $htmlJerarquizado, 0, 1, 0, true, 'J', true);
                    break;
                
                case 3:
                    $pdf->writeHTMLCell(0, 0, '', '', $htmlConsultor, 0, 1, 0, true, 'J', true);
                    break;
                
                default:
                    break;
            }
            
            $pdf->Ln(5);
            ////
            
            if ($conDetalle) {
                $pdf->SetFont('dejavusans', '', 9);
                $indice = 1;
                if ($sexo == 'F') {
                    $html = $indice.'. Médica matriculada bajo el Nº <b>'.$matricula.'</b> , registrado en el Libro <b>'.$libro.'</b> Folio <b>'.$folio.'</b>';
                } else {
                    $html = $indice.'. Médico matriculado bajo el Nº <b>'.$matricula.'</b> , registrado en el Libro <b>'.$libro.'</b> Folio <b>'.$folio.'</b>';
                }
                $html .= ' con fecha <b>'.$fechaMatriculacion.'</b>';
                $pdf->writeHTMLCell(0, $alturaLinea, '', '', $html, 0, 1, 0, true, '', true);
                
                /*
                $indice += 1;
                $html = $indice.'. Tipo y Número de Documento: <b>'.$tipoDocumento.'</b> <b>'.$numeroDocumento.'</b>';
                $pdf->writeHTMLCell(0, $alturaLinea, '', '', $html, 0, 1, 0, true, '', true);
                $indice += 1;
                $html = $indice.'. Fecha de Matriculación: <b>'.$fechaMatriculacion.'</b>';
                $pdf->writeHTMLCell(0, $alturaLinea, '', '', $html, 0, 1, 0, true, '', true);
                */
                $indice += 1;
                $html = $indice.'. Fecha de Nacimiento: <b>'.$fechaNacimiento.'</b>';
                $pdf->writeHTMLCell(0, $alturaLinea, '', '', $html, 0, 1, 0, true, '', true);
                //$pdf->MultiCell(0, $alturaLinea, , 0, 'L', false, 1, '', '', true);
                
                //si lleva imprimo los datos del domicilio actual y consultorio
                if ($conDomicilio) {
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
                        
                        $domicilioCompleto .= ' - '.$domicilio['nombreLocalidad'].' ('.$domicilio['codigoPostal'].')';
                        
                        $indice += 1;
                        $html = $indice.'. Domicilio: <b>'.$domicilioCompleto.'</b>';
                        $pdf->writeHTMLCell(0, $alturaLinea, '', '', $html, 0, 1, 0, true, '', true);
                        //$pdf->MultiCell(0, $alturaLinea, , 0, 'L', false, 1, '', '', true);
                    }
                    
                    //datos de contacto
                    $resContacto = obtenerColegiadoContactoPorIdColegiado($idColegiado);
                    if ($resContacto['estado']) {
                        $contacto = $resContacto['datos'];
                        $preFijoNacional = '';
                        if ($idTipoCertificado == 18) {
                            $preFijoNacional = '54 ';
                        }
                        $telefonos = '';
                        if (isset($contacto['telefonoFijo']) && $contacto['telefonoFijo'] != "") {
                            $telefonos = $preFijoNacional.$contacto['telefonoFijo'].' - '.$contacto['telefonoMovil'];
                        }
                        if (isset($contacto['telefonoMovil']) && $contacto['telefonoMovil'] != "") {
                            if ($telefonos != '') {
                                $preFijoNacional = ' - '.$preFijoNacional;
                            }
                            $telefonos .= $preFijoNacional.$contacto['telefonoMovil'];
                        }
                        $mail = $contacto['email'];
                        
                        $indice += 1;
                        $html = $indice.'. Teléfonos: <b>'.$telefonos.'</b>';
                        //$pdf->MultiCell(0, $alturaLinea, , 0, 'L', false, 0, '', '', true);
                        if ($idTipoCertificado == 10) {
                            //para la caja imprimo el mail
                            $html .= ' - Mail: <b>'.$mail.'</b>';
                            //$pdf->MultiCell(0, $alturaLinea, , 0, 'L', false, 1, '100', '', true);
                        } else {
                            //salto la linea
                            //$pdf->MultiCell(0, $alturaLinea, ''.$mail, 0, 'L', false, 1, '', '', true);
                        }
                        $pdf->writeHTMLCell(0, $alturaLinea, '', '', $html, 0, 1, 0, true, '', true);
                    }
                    
                    //datos de consultorio
                    $resConsultorio = obtenerDomicilioProfesional($idColegiado);
                    if ($resConsultorio['estado']) {
                        $consultorio = $resConsultorio['datos'];
                        $consultorios = $consultorio['domicilio'].' - '.$consultorio['nombreLocalidad'];
                        
                        $indice += 1;
                        $html = $indice.'. Domicilio profesional: <b>'.$consultorios.'</b>';
                        $pdf->writeHTMLCell(0, $alturaLinea, '', '', $html, 0, 1, 0, true, '', true);
                        //$pdf->MultiCell(0, $alturaLinea, , 0, 'L', false, 1, '', '', true);
                    }
                }
                
                $indice += 1;
                /*
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->MultiCell(0, $alturaLinea, $indice.'. Título: ', 0, 'L', false, 0, '', '', true);
                $pdf->SetFont('dejavusans', 'B', 9);
                $pdf->MultiCell(0, $alturaLinea, $tituloColegiado, 0, 'L', false, 0, '30', '', true);
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->MultiCell(0, $alturaLinea, ' Otorgó: ', 0, 'L', false, 0, '45', '', true);
                $pdf->SetFont('dejavusans', 'B', 9);
                $pdf->MultiCell(0, $alturaLinea, $universidad, 0, 'L', false, 0, '60', '', true);
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->MultiCell(0, $alturaLinea, ' con fecha: ', 0, 'L', false, 0, '150', '', true);
                $pdf->SetFont('dejavusans', 'B', 9);
                $pdf->MultiCell(0, $alturaLinea, $fechaTitulo, 0, 'L', false, 1, '170', '', true);
                if (isset($bloqueoTitulo)) {
                    $pdf->MultiCell(0, $alturaLinea, 'Con Bloqueo de Título desde el  '.$fechaBloqueoTitulo, 0, 'L', false, 1, '', '', true);
                }
                 * 
                 */
                $html = $indice.'. Título: <b>'.$tituloColegiado.'</b> Otorgó: <b>'.$universidad.'</b> con fecha: <b>'.$fechaTitulo.'</b>';
                if (isset($bloqueoTitulo)) {
                    $html .= ' Con Bloqueo de Título desde el  <b>'.$fechaBloqueoTitulo.'</b>';
                }                
                $pdf->writeHTMLCell(0, $alturaLinea, '', '', $html, 0, 1, 0, true, '', true);

                $pdf->SetFont('dejavusans', 'B', 6);
                $indice += 1;
                $resEspecialidades = obtenerEspecialidadesPorIdColegiado($idColegiado);
                if ($resEspecialidades['estado']) {
                    //verifico si tiene alguna especialidad por convenio unlp que actualiza una existente
                    $conUnlpActualizada = FALSE;
                    foreach ($resEspecialidades['datos'] as $row) {
                        if (isset($row['fechaEspecialistaOrigen']) && $row['fechaEspecialistaOrigen'] <> "") {
                            $conUnlpActualizada = TRUE;
                        }
                    }

                    if ($conUnlpActualizada) {
                        $pdf->SetFont('dejavusans', '', 8);
                    } else {
                        $pdf->SetFont('dejavusans', '', 9);
                    }
                    $pdf->MultiCell(0, $alturaLinea, $indice.'. Especialidad: ', 0, 'L', false, 0, '', '', true);
                    $pdf->MultiCell(0, $alturaLinea, 'Distrito', 0, 'L', false, 0, '70', '', true);
                    $pdf->MultiCell(0, $alturaLinea, 'Con fecha', 0, 'L', false, 0, '83', '', true);

                    if ($conUnlpActualizada) {
                        $pdf->MultiCell(0, $alturaLinea, 'Conv.Universidad', 0, 'L', false, 0, '100', '', true);
                        $pdf->MultiCell(0, $alturaLinea, 'Ult.Recerti.', 0, 'L', false, 0, '125', '', true);
                        $pdf->MultiCell(0, $alturaLinea, 'Jerarquizado', 0, 'L', false, 0, '142', '', true);
                        $pdf->MultiCell(0, $alturaLinea, 'Consultor', 0, 'L', false, 0, '162', '', true);
                        $pdf->MultiCell(0, $alturaLinea, 'Caducidad', 0, 'L', false, 0, '177', '', true); 
                        $pdf->SetFont('dejavusans', 'B', 6);
                    } else {
                        //$pdf->MultiCell(0, $alturaLinea, 'Con fecha', 0, 'L', false, 0, '90', '', true);
                        $pdf->MultiCell(0, $alturaLinea, 'Ult.Recerti.', 0, 'L', false, 0, '108', '', true);
                        $pdf->MultiCell(0, $alturaLinea, 'Jerarquizado', 0, 'L', false, 0, '129', '', true);
                        $pdf->MultiCell(0, $alturaLinea, 'Consultor', 0, 'L', false, 0, '150', '', true);
                        $pdf->MultiCell(0, $alturaLinea, 'Caducidad', 0, 'L', false, 0, '171', '', true);
                        $pdf->SetFont('dejavusans', 'B', 8);
                    }
                    $pdf->Ln(5);
                    
                    foreach ($resEspecialidades['datos'] as $row) {
                        /*
                        if (strlen($row['nombreEspecialidad']) > 34) {
                            $pdf->SetFont('dejavusans', 'B', 6);
                        } else {
                            $pdf->SetFont('dejavusans', 'B', 8);
                        }
                        */
                        $pdf->MultiCell(0, $alturaLinea, $row['nombreEspecialidad'], 0, 'L', false, 0, '', '', true);
                        //$pdf->SetFont('dejavusans', 'B', 8);
                        $estadoEspecialidad = $row['estado'];
                        if (isset($row['fechaEspecialistaOrigen'])) {
                            $fechaEspecialista = $row['fechaEspecialistaOrigen'];
                            $fechaUNLP = $row['fechaEspecialista'];    
                        } else {
                            $fechaEspecialista = $row['fechaEspecialista'];
                            $fechaUNLP = NULL;
                        }
                        
                        $fechaVencimiento = $row['fechaVencimiento'];
                        if (isset($row['fechaRecertificacion']) && $row['fechaRecertificacion'] <> "0000-00-00") {
                            $fechaRecertificacion = $row['fechaRecertificacion'];
                        } else {
                            $fechaRecertificacion = "";
                        }
                        $idEspecialidad = $row['idEspecialidad'];

                        if ($estadoEspecialidad == 'A') {
                            //if ($row['tipoespecialista'] == 8) {
                            //    $otorgadaPor = 'Nación';
                            //} else {
                                $otorgadaPor = substr($row['distritoOrigen'], 0, 3);
                                if ($otorgadaPor == '0') {
                                    $otorgadaPor = 1;
                                }
                            //}
                            $pdf->MultiCell(15, $alturaLinea, $otorgadaPor, 0, 'C', false, 0, '70', '', true);
                            $pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($fechaEspecialista), 0, 'L', false, 0, '83', '', true);
                            $proxima_columna = 21;
                            $columna_fechas = 108;
                            if ($conUnlpActualizada) {
                                if (isset($fechaUNLP)) {
                                    $proxima_columna = 18;
                                    $columna_fechas = 107;
                                    //$pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($fechaUNLP), 0, 'L', false, 0, '177', '', true);
                                    $pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($fechaUNLP), 0, 'L', false, 0, $columna_fechas, '', true);
                                    $columna_fechas += $proxima_columna;
                                }
                            }
                            //$pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($fechaRecertificacion), 0, 'L', false, 0, '107', '', true);
                            $pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($fechaRecertificacion), 0, 'L', false, 0, $columna_fechas, '', true);
                            $columna_fechas += $proxima_columna;
                            $idColegiadoEspecialista = $row['idColegiadoEspecialista'];

                            //verifico si tiene jerarquizado y consultor
                            $verVencimiento = TRUE;
                            if ($otorgadaPor <> "NAC") {
                                //imprimo JER y CON si no es de NACION
                                $resJerarquizado = obtenerFechaJerarquizadoConsultor($idColegiadoEspecialista, JERARQUIZADO);
                                if ($resJerarquizado['estado']) {
                                    //$pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($resJerarquizado['fecha']), 0, 'L', false, 0, '125', '', true);
                                    $pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($resJerarquizado['fecha']), 0, 'L', false, 0, $columna_fechas, '', true);
                                    $columna_fechas += $proxima_columna;

                                    $resConsultor = obtenerFechaJerarquizadoConsultor($idColegiadoEspecialista, CONSULTOR);
                                    if ($resConsultor['estado']) {
                                        //$pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($resConsultor['fecha']), 0, 'L', false, 0, '144', '', true);
                                        $pdf->MultiCell(0, $alturaLinea, cambiarFechaFormatoParaMostrar($resConsultor['fecha']), 0, 'L', false, 0, $columna_fechas, '', true);
                                        $verVencimiento = FALSE;
                                    } else {
                                        //$pdf->MultiCell(0, $alturaLinea, 'No', 0, 'L', false, 0, '155', '', true);
                                        $pdf->MultiCell(0, $alturaLinea, 'No', 0, 'L', false, 0, ($columna_fechas + 5), '', true);
                                    }
                                    $columna_fechas += $proxima_columna;
                                } else {
                                    //$pdf->MultiCell(0, $alturaLinea, 'No', 0, 'L', false, 0, '135', '', true);
                                    //$pdf->MultiCell(0, $alturaLinea, 'No', 0, 'L', false, 0, '155', '', true);
                                    //if (isset($fechaUNLP)) {
                                        $pdf->MultiCell(0, $alturaLinea, 'No', 0, 'L', false, 0, ($columna_fechas + 10), '', true);
                                        $pdf->MultiCell(0, $alturaLinea, 'No', 0, 'L', false, 0, ($columna_fechas + 25), '', true);
                                    //} else {
                                    //    $pdf->MultiCell(0, $alturaLinea, 'No', 0, 'L', false, 0, ($columna_fechas + 10), '', true);
                                    //    $pdf->MultiCell(0, $alturaLinea, 'No', 0, 'L', false, 0, ($columna_fechas + 25), '', true);
                                    //}
                                    $columna_fechas += ($proxima_columna * 2);
                                }
                            }

                            if ($verVencimiento) {
                                if (isset($fechaVencimiento) && $fechaVencimiento != '0000-00-00') {
                                    if (date('Y-m-d') > $fechaVencimiento) {
                                        //verifica si esta dentro de los 2 años permitidos a Recertificar
                                        $fechaLimite = sumarRestarSobreFecha($fechaVencimiento, 2, 'year', '+');
                                        if (date('Y-m-d') > $fechaLimite) {
                                            if ($otorgadaPor <> "NAC") {
                                                $caduca = 'No Recertificada';
                                            } else {
                                                $caduca = 'No Renovada';
                                            }
                                        } else {
                                            if ($otorgadaPor <> "NAC") {
                                                $caduca = 'Recert. en Trámite';
                                            } else {
                                                $caduca = 'Renov. en Trámite';
                                            }
                                        }
                                    } else {
                                        $caduca = "";
                                        /*
                                        if ($otorgadaPor <> "NAC") {
                                            $caduca = 'Recert.hasta: ';
                                        } else {
                                            $caduca = 'Renov.hasta: ';
                                        }
                                        */
                                        $caduca .= cambiarFechaFormatoParaMostrar($fechaVencimiento);
                                        $fechaLimite = sumarRestarSobreFecha($fechaVencimiento, 5, 'year', '-');
                                        if ($fechaEspecialista == $fechaLimite) {
                                            $caduca = "";
                                            /*
                                            if ($otorgadaPor <> "NAC") {
                                                $caduca = 'Certif.hasta: ';
                                            } else {
                                                $caduca = 'Renov.hasta: ';
                                            }
                                            */
                                            $caduca .= cambiarFechaFormatoParaMostrar($fechaVencimiento);
                                        }
                                    }
                                    //$pdf->MultiCell(100, $alturaLinea, $caduca, 0, 'L', false, 0, '160', '', true);
                                    if ($otorgadaPor == 'NAC') {
                                        $columna_fechas  += ($proxima_columna * 2);
                                    }
                                    $pdf->MultiCell(100, $alturaLinea, $caduca, 0, 'L', false, 0, $columna_fechas, '', true);
                                } else {
                                    $caduca = 'No';
                                    //$pdf->MultiCell(100, $alturaLinea, $caduca.$columna_fechas, 0, 'C', false, 0, '130', '', true);
                                    $pdf->MultiCell(100, $alturaLinea, $caduca, 0, 'C', false, 0, $columna_fechas, '', true);
                                }
                            }
                        } else {
                            //busco la resolucion de la baja
                            $resBaja = verBajaEspecialista($idColegiado, $idEspecialidad, $fechaEspecialista);
                            if ($resBaja['estado']) {
                                $baja = $resBaja['datos'];
                                $pdf->SetFont('dejavusans', 'B', 8);
                                $pdf->MultiCell(0, $alturaLinea, 'Baja por Res. '.$baja['numero'].' de fecha '. cambiarFechaFormatoParaMostrar($baja['fecha']), 0, 'L', false, 0, '100', '', true);
                            } else {
                                $resBaja['mensaje'];
                            }
                        }
                        $pdf->Ln(5);
                    }
                } else {
                    $pdf->SetFont('dejavusans', '', 9);
                    $pdf->MultiCell(0, $alturaLinea, $indice.'. Especialidad: ', 0, 'L', false, 0, '', '', true);
                    $pdf->SetFont('dejavusans', 'B', 9);
                    $pdf->MultiCell(0, $alturaLinea, 'NINGUNA.', 0, 'L', false, 1, '42', '', true);
                }

                //imprimir cargos en el colegio
                /*
                $indice += 1;
                $resCargos = obtenerCargosColegioPorColegiado($idColegiado);
                if ($resCargos['estado']) {
                    $pdf->MultiCell(0, $alturaLinea, $indice.'. Cargos desempeñados en el Colegio: ', 0, 'L', false, 0, '', '', true);
                    foreach ($resCargos['datos'] as $row) {
                        $pdf->MultiCell(0, $alturaLinea, $row['nombreCargo'].' desde el'.cambiarFechaFormatoParaMostrar($row['fechaDesde']).' hasta el '.cambiarFechaFormatoParaMostrar($row['fechaHasta']), 0, 'L', false, 1, '', '', true);
                    }
                } else {
                    $pdf->MultiCell(0, $alturaLinea, $indice.'. Cargos desempeñados en el Colegio: NINGUNO.', 0, 'L', false, 0, '', '', true);
                }
                */

                //imprimir sanciones
                $indice += 1;
                $pdf->SetFont('dejavusans', '', 9);
                $pdf->MultiCell(0, $alturaLinea, $indice.'. Sanciones éticas disciplinarias: ', 0, 'L', false, 0, '', '', true);
                $conSancion = FALSE;
                $resSanciones = obtenerSancionesPorIdColegiado($idColegiado);
                if ($resSanciones['estado']) {
                    foreach ($resSanciones['datos'] as $row) {
                        $fechaDesde = $row['fechaDesde'];
                        $fechaHasta = $row['fechaHasta'];
                        $ley = $row['ley'];
                        $articulo = $row['articulo'];
                        $sanciones = NULL;
                        switch ($articulo) {
                            case '52c':
                                // le sumo 10 años a la fecha de la sancion para ver si caducó
                                //$dia = date($fechaDesde, 'd');
                                //$mes = date($fechaDesde, 'm');
                                //$anio = date($fechaDesde, 'Y') + 10;
                                //$fechaMasDiez = $anio.'-'.$mes.'-'.$dia;
                                $fechaLimite = sumarRestarSobreFecha($fechaDesde, 10, 'year', '+');
                                if ($fechaDesde <= $fechaLimite) {
                                    //$sanciones = $ley .' '. cambiarFechaFormatoParaMostrar($fechaDesde) .' al '. cambiarFechaFormatoParaMostrar($fechaHasta) .' Art.:'. $articulo; 
                                    $sanciones = 'Art. 52c Decreto-Ley 5413/58'; 
                                }
                                break;

                            case '40c':
                                $fechaActual = date('Y-m-d'); 
                                if ($fechaDesde <= $fechaActual && $fechaActual <= $fechaHasta) {
                                    //$sanciones = $ley .' '. cambiarFechaFormatoParaMostrar($fechaDesde) .' al '. cambiarFechaFormatoParaMostrar($fechaHasta) .' Art.:'. $articulo; 
                                    $sanciones = 'Art. 40c Decreto-Ley 5413/58'; 
                                }
                                break;

                            default:
                                break;
                        }
                        if (isset($sanciones)) {
                            $pdf->SetFont('dejavusans', 'B', 9);
                            $pdf->MultiCell(0, $alturaLinea, $sanciones, 0, 'L', false, 1, '70', '', true);
                            //$pdf->MultiCell(0, $alturaLinea, 'SI', 0, 'L', false, 1, '70', '', true);
                            $conSancion = TRUE;
                        }
                    }
                } 

                if (!$conSancion) {
                    $pdf->SetFont('dejavusans', 'B', 9);
                    $pdf->MultiCell(0, $alturaLinea, 'NINGUNA', 0, 'L', false, 1, '70', '', true);
                    //$pdf->MultiCell(0, $alturaLinea, $indice.'. Sanciones éticas disciplinarias: NINGUNA', 0, 'L', false, 1, '', '', true);
                }
                $pdf->SetFont('dejavusans', '', 9);

                //imprimir movimientos matriculares
                $indice += 1;
                $resMovimiento = obtenerMovimientosPorIdColegiado($idColegiado);
                if ($resMovimiento['estado']) {
                    $pdf->MultiCell(0, $alturaLinea, $indice.'. Movimientos matriculares:', 0, 'L', false, 1, '', '', true);
                    
                    if (isset($resMovimiento['datos']) && sizeof($resMovimiento['datos'])>1) {
                        
                        if (sizeof($resMovimiento['datos'])>5) {
                            $pdf->SetFont('dejavusans', '', 8);
                            $alturaLineaMov = 5;
                        } else {
                            $pdf->SetFont('dejavusans', '', 9);
                            $alturaLineaMov = 5;
                        }
                        foreach ($resMovimiento['datos'] as $row) {
                            $elDistrito = $row['distritoCambio'];
                            if (isset($row['romanos'])) {
                                $elDistrito = $row['romanos'];
                            }
                                        
                            switch ($row['idTipoMovimietno']) {
                                case 5:
                                    $movimiento = 'Colegiado en Distrito I desde el '.cambiarFechaFormatoParaMostrar($row['fechaDesde'],'d-m-y').' (Baja del Distrito '.$elDistrito.'). ';
                                    break;

                                case 6:
                                    $movimiento = 'Egreso Definitivo del Distrito I desde el '.cambiarFechaFormatoParaMostrar($row['fechaDesde'],'d-m-y').', colegiado en Dist.'.$elDistrito.'. ';
                                    break;

                                case 8:
                                    $movimiento = 'Colegiado en Dist.'.$elDistrito.' Inscripto en Dist.I desde el '.cambiarFechaFormatoParaMostrar($row['fechaDesde'],'d-m-y').'. ';
                                    break;

                                case 9:
                                    $movimiento = 'Cancelación de matrícula por Art.40 inc C Decreto Ley 5413/58. Desde el '.cambiarFechaFormatoParaMostrar($row['fechaDesde'],'d-m-y');
                                    if (isset($row['fechaHasta']) && $row['fechaHasta'] <> '0000-00-00') {
                                        $movimiento .= ' al '.cambiarFechaFormatoParaMostrar($row['fechaHasta'],'d-m-y').'.-';
                                    }
                                    break;

                                case 10:
                                    $movimiento = 'Colegiado en Dist.I, Inscripto en Dist.'.$elDistrito.' desde el '.cambiarFechaFormatoParaMostrar($row['fechaDesde'],'d-m-y');
                                    if (isset($row['fechaHasta']) && $row['fechaHasta'] <> '0000-00-00') {
                                        $movimiento .= ' hasta el '.cambiarFechaFormatoParaMostrar($row['fechaHasta'],'d-m-y').'.-';
                                    }
                                    break;

                                default:
                                    $movimiento = $row['detalleMovimiento'].' desde el '.cambiarFechaFormatoParaMostrar($row['fechaDesde']);
                                    if (isset($row['fechaHasta']) && $row['fechaHasta'] <> '0000-00-00') {
                                        $movimiento .= ' al '.cambiarFechaFormatoParaMostrar($row['fechaHasta']).'.-';
                                    }
                                    break;
                            }
                            $pdf->MultiCell(0, $alturaLineaMov,$movimiento, 0, 'L', false, 1, '20', '', true);
                        }
                    } else {
                        $unMovimiento = $resMovimiento['datos'][0];
                        $elDistrito = $unMovimiento['distritoCambio'];
                        if (isset($unMovimiento['romanos'])) {
                            $elDistrito = $unMovimiento['romanos'];
                        }
                        switch ($unMovimiento['idTipoMovimietno']) {
                            case 5:
                                $movimiento = 'Colegiado en Distrito I desde el '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaDesde'],'d-m-y').' (Baja del Distrito '.$elDistrito.'). ';
                                break;

                            case 6:
                                $movimiento = 'Egreso Definitivo del Distrito I desde el '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaDesde'],'d-m-y').', colegiado en Dist.'.$elDistrito.'. ';
                                break;

                            case 8:
                                $movimiento = 'Colegiado en Dist.'.$elDistrito.' Inscripto en Dist.I desde el '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaDesde'],'d-m-y').'. ';
                                break;

                            case 9:
                                $movimiento = 'Cancelación de matrícula por Art.40 inc C Decreto Ley 5413/58. Desde el '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaDesde'],'d-m-y');
                                if (isset($unMovimiento['fechaHasta']) && $unMovimiento['fechaHasta'] <> '0000-00-00') {
                                    $movimiento .= ' al '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaHasta'],'d-m-y').'.-';
                                }
                                break;

                            case 10:
                                $movimiento = 'Colegiado en Dist.I, Inscripto en Dist.'.$elDistrito.' desde el '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaDesde'],'d-m-y');
                                if (isset($unMovimiento['fechaHasta']) && $unMovimiento['fechaHasta'] <> '0000-00-00') {
                                    $movimiento .= ' hasta el '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaHasta'],'d-m-y').'.-';
                                }
                                break;

                            default:
                                $movimiento = $unMovimiento['detalleMovimiento'].' desde el '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaDesde']);
                                if (isset($unMovimiento['fechaHasta']) && $unMovimiento['fechaHasta'] <> '0000-00-00') {
                                    $movimiento .= ' al '.cambiarFechaFormatoParaMostrar($unMovimiento['fechaHasta']).'.-';
                                }
                                break;
                        }
                        $pdf->MultiCell(0, $alturaLinea,$movimiento, 0, 'L', false, 1, '20', '', true);
                    }
                } else {
                    $pdf->MultiCell(0, $alturaLinea, $indice.'. Movimientos matriculares: NO REGISTRA', 0, 'L', false, 1, '', '', true);
                }

                //Situación con tesorería:
                //    !si es fallecido, jubilado, inscripto -> no se imprime la leyenda de Situacion Con Tesoreria
                if ($conEstadoTesoreria) {
                    $indice += 1;
                    $pdf->Ln(2);
                    $pdf->SetFont('dejavusans', '', 9);
                    $pdf->MultiCell(0, $alturaLinea, $indice.'. Situación con tesorería: ', 0, 'L', false, 0, '', '', true);
                    $pdf->SetFont('dejavusans', 'B', 9);
                    $pdf->MultiCell(0, $alturaLinea, $estadoConTesoreria, 0, 'L', false, 1, '60', '', true);
                }
                
                //si es para comisiones, imprime linea fap
                if ($idTipoCertificado == 5) {
                    //Si tiene antecedentes en fap lo imprime
                    //$dia = date('d');
                    //$mes = date('m');
                    //$anio = date('Y') - 10;
                    //$fechaFap = $anio.'-'.$mes.'-'.$dia;
                    $fechaLimite = sumarRestarSobreFecha(date('Y-m-d'), 10, 'year', '-');
                    $indice += 1;
                    $pdf->SetFont('dejavusans', '', 9);
                    $pdf->MultiCell(0, $alturaLinea, $indice.'. Registra antecedentes en el FAP: ', 0, 'L', false, 0, '', '', true);
                    if (colegiadoTieneFap($idColegiado, $fechaLimite)) {
                        $enFap = 'SI';
                    } else {
                        $enFap = 'NO';
                    }
                    $pdf->SetFont('dejavusans', 'B', 9);
                    $pdf->MultiCell(0, $alturaLinea, $enFap, 0, 'L', false, 1, '82', '', true);                    
                    $pdf->SetFont('dejavusans', '', 9);
                }                
                
            } //fin conDetalle=TRUE

            if ($conNota) {
                //imprimir NOTA
                $pdf->SetFont('dejavusans', '', 8);
                $pdf->Ln(2);
                if ($idTipoCertificado == 1) {
                    $pdf->SetFont('dejavusans', '', 9);
                    $pdf->writeHTMLCell(0, 0, '', '', $notaUnica, 0, 1, 0, true, 'J', true);
                    $pdf->Ln(2);

                    $html = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.
                            'Sin otro particular saludo a usted muy atentamente.-';

                    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
                    $pdf->Ln(3);
                    $resNotaCambio = obtenerNotaCambioDistritoPorId($idNotaCambioDistrito);
                    if ($resNotaCambio['estado']) {
                        $notaCambioDistrito = $resNotaCambio['datos']['nota'];
                        $html = 'NOTA: '.$notaCambioDistrito.'<br>'; 
                    } else {
                        $html = '';
                    }

                } else {
                    if ($idTipoCertificado == 18) {
                        // es para el exterior, se emite nota 
                        $html = '<p style="line-height: 10em;">Asimismo, se aclara que hasta la fecha de 
                                cancelación de su matrícula y durante el período que ';
                        if ($sexo <> 'F') {
                            $html .= 'el profesional médico ';
                        } else {
                            $html .= 'la profesional médica ';
                        }
                        $html .= '<b>'.$colegiado['nombre'].' '.$colegiado['apellido'].'</b>, se encontraba con su 
                                matrícula debidamente habilitada, no se registró sanción disciplinaria, 
                                comunicación y/o inhabilitación judicial que haya impedido el libre 
                                ejercicio de su profesión.</p>';
                    } else {
                        $pdf->SetFont('dejavusans', '', 9);
                        $pdf->writeHTMLCell(0, 0, '', '', $notaUnica, 0, 1, 0, true, 'J', true);
                        $pdf->Ln(2);
                        $html = '<p style="line-height: 10em;"><b>NOTA: El presente certificado tiene validez dentro del ámbito del DISTRITO I y demás jurisdicciones donde el profesional se encuentre debidamente INSCRIPTO.-</b>
                            <br><i>Artículo 7º del Reglamento de Matriculación: "... el médico que ejerza en otro u otros distritos deberá presentarse a los respectivos '.
                            'Colegios para la debida constancia administrativa y el registro correspondiente...".- </i></p>';
                    }
                }
                $pdf->SetFont('dejavusans', '', 8);
                $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
            }
            
            $pdf->Ln(5);
            $pdf->SetFont('dejavusans', '', 8);
            $pdf->MultiCell(0, 1, 'Realizó: '.$_SESSION['user_entidad']['nombreUsuario'], 0, 'L', false, 1, '', '', true);                    
            
            //imprimo sello oval
            $pdf->Ln(15);
            $img = '../../public/images/SELLO.png';
            if ($conFirma == 'S') {
                //imprimo sello y firma
                //1: presidente
                $resFirmante = obtenerFirmaPorCargo(1); 
                if ($resFirmante['estado']) {
                    $firmante = $resFirmante['datos'];
                    $presidente = 'Dr. '. ucfirst($firmante['nombre']) .' '. ucfirst($firmante['apellido']);
//                    $fileFirma = rellenarCeros($firmante['matricula'], 8) .'.bmp';
//                    $firma = fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Firmas/".$fileFirma, "rb");
//                    if (!$firma) {
//                        echo "<p>No puedo abrir el archivo para lectura</p>";
//                        exit;
//                    }
//                    $contents=stream_get_contents($firma);
//                    fclose ($firma);
//                    $firmaVer = base64_encode($contents);
                    $jpgfile1 = '../firma/'.rellenarCeros($firmante['matricula'], 8) .'.jpg';
                        
                    $htmlFirma1 = '<td style="text-align:center;" >
                                    <img src="'.$jpgfile1.'" border="0" height="120" width="" />
                                    <label style="font-size: 10px;">'.$presidente.'</label><br>
                                    <label style="font-size: 8px;">Presidente<br>Colegio de Médicos - Distrito I</label>
                                </td>';
                } else {
                    $htmlFirma2 = '<td>&nbsp;'.$resFirmante['mensaje'].'</td>';
                }
                //2: secretariogeneral
                $resFirmante = obtenerFirmaPorCargo(2); 
                if ($resFirmante['estado']) {
                    $firmante = $resFirmante['datos'];
                    $secretario = 'Dr. '. ucfirst($firmante['nombre']) .' '. ucfirst($firmante['apellido']);
//                    $fileFirma = rellenarCeros($firmante['matricula'], 8) .'.bmp';
//                    $firma = fopen ("ftp://webcolmed:web.2017@192.168.2.50:21/Firmas/".$fileFirma, "rb");
//                    if (!$firma) {
//                        echo "<p>No puedo abrir el archivo para lectura</p>";
//                        exit;
//                    }
//                    $contents=stream_get_contents($firma);
//                    fclose ($firma);
//                    $firmaVer = base64_encode($contents);
                    $jpgfile2 = '../firma/'.rellenarCeros($firmante['matricula'], 8) .'.jpg';
                        
                    $htmlFirma2 = '<td style="text-align:center;" >
                                    <img src="'.$jpgfile2.'" border="0" height="110" width="" />
                                    <label style="font-size: 10px;">'.$secretario.'</label><br>
                                    <label style="font-size: 8px;">Secretario General<br>Colegio de Médicos - Distrito I</label>
                                </td>';
                } else {
                    $htmlFirma2 = '<td>&nbsp;'.$resFirmante['mensaje'].'</td>';
                }
            } else {
                //$pdf->Ln(75);
                $htmlFirma2 = '';
                $htmlFirma1 = '';
            }
            $html = '<table>
                    <tr>'
                        .$htmlFirma2.
                        '<td style="text-align:center;" >
                            <img src="'.$img.'" border="0" height="140" width="" />
                        </td>'
                        .$htmlFirma1.
                    '</tr>
                    </table';
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
            
            /*
            $html = '<table cellspacing="1" cellpadding="1" border="1" style="text-align: center; ">
                        <tr><td>La fotocopia de éste certificado no tiene validez</td></tr>
                    </table>';
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
            */

                $i++;
            }
            
            //si es para ioma genera una declaracion jurada
            /* SE elimina esta impresion el 16/7/2020 a pedido de moreno

            if ($idTipoCertificado == 19) {
                $pdf->AddPage();
                $pdf->Ln(10);
                $pdf->SetFont('dejavusans', 'B', 20);
                $pdf->MultiCell(0, 10, 'DECLARACIÓN JURADA', 0, 'C', false, 0, '', '');
                
                $pdf->SetFont('dejavusans', '', 10);
                if ($sexo <> 'F') {
                    $html = 'Yo el profesional médico <b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b> con Matrícula Provincial Nº <b>'.$matricula.'</b>';
                } else {
                    $html = 'Yo la profesional médica <b>'.$colegiado['nombre'].' '.$colegiado['apellido'].' </b> con Matrícula Provincial Nº <b>'.$matricula.'</b>';
                }
                $html .= ' solicito un Certificado de Colegiación para ser presentado ante IOMA, donde realizaré la actividad de: <br><br>'.
                    '_________________________________________________________________________________________________<br><br>'.
                    '_________________________________________________________________________________________________<br><br>'.
                    '__________________________________________________________________________________________________<br><br>';
                
                $pdf->Ln(20);
                $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
                
                $pdf->Ln(20);
                $html = 'Firma: _______________________________';
                $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
                
                $pdf->Ln(10);
                $html = 'Aclaración: _______________________________';
                $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
                
                $pdf->Ln(10);
                $html = 'DNI: _______________________________';
                $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);
            }
            */
            
            if ($enviaMail == 'S' && $conFirma == 'S') {
                $tipoPdf = 'F';
            } else {
                $tipoPdf = 'I';
            }
            
            $destination = $tipoPdf; //'F';
            if (!preg_match('/\.pdf$/', $path_to_store_pdf))
            {
                $path_to_store_pdf .= '.pdf';
            }
            ob_clean();

            $camino = $_SERVER['DOCUMENT_ROOT'];
            $camino .= PATH_PDF;
            $nombreArchivo = 'Certificado_'.$matricula.'_'.date('Ymd').date('his').'.pdf';
            $nombreArchivoJpg = 'Certificado_'.$matricula.'_'.date('Ymd').date('his').'.jpg';
            $periodoActual = $_SESSION['periodoActual'];
                    
            $estructura = "../../archivos/certificados/".$periodoActual;
            if (!file_exists($estructura)) {
                mkdir($estructura, 0777, true);
            }
            if (file_exists("../../archivos/certificados/".$periodoActual."/".$nombreArchivo)) {
                unlink("../../archivos/certificados/".$periodoActual."/".$nombreArchivo);
            } 
    
            if ($tipoPdf == 'F') {
//                $pdfName = $camino.'/archivos/certificados/'.$periodoActual.'/'.$nombreArchivo;
//                
                $pdf->Output($camino.'/archivos/certificados/'.$periodoActual.'/'.$nombreArchivo, $destination);        
//                exec('convert "'.$pdfName.'" -colorspace RGB -resize 800 "'.$nombreArchivoJpg.'"', $output, $return_var);
//                
//                // create Imagick object
//                $imagick = new Imagick();
//                // Reads image from PDF
//                $imagick->readImage($pdfName);
//                // Writes an image
//                $imagick->writeImages($nombreArchivoJpg);
//                
//                echo $nombreArchivoJpg; exit;
                
                $envioMail = TRUE;
            } else {
                $pdf->Output($nombreArchivo, $destination);        
                $envioMail = FALSE;
            }
            if ($envioMail && isset($mail)) {
                //enviamos el pdf por mail si tiene contacto
                $destinatario = $colegiado['apellido'].', '.$colegiado['nombre'];
                //$mailDestino = $mail;
                require_once '../../PHPMailer/class.phpmailer.php';
                require_once '../../PHPMailer/class.smtp.php';

                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = "ssl";
                $mail->Host = "mail.colmed1.org.ar";
                $mail->Port = 465;
                //$mail->Username = "sistemas@colmed1.org.ar";
                //$mail->Password = "@sistem@s_1965";
                $mail->Username = 'mesadeentrada@colmed1.org.ar';
                $mail->Password = 'certificado';
                $mail->Username = MAIL_MASIVO;
                $mail->Password = MAIL_MASIVO_PASS;


                $mail->From = "mesadeentrada@colmed1.org.ar";
                $mail->FromName = "Colegio de Medicos. Distrito I";
                $mail->Subject = "Certificado solicitado";
                $mail->AltBody = "";
                $mail->MsgHTML("Envio Certificado Solicitado");
                $mail->AddAttachment("../../archivos/certificados/".$periodoActual."/".$nombreArchivo);
                $mail->AddAddress($mailDestino, $destinatario);
                $mail->IsHTML(true);
                
                
                if($mail->Send()) {
                    $mailEnviado = TRUE;
                }else{
                    $mailEnviado = FALSE;
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                }
            }
            if ($envioMail) {
                require_once ('../../html/head.php');
                require_once ('../../html/encabezado.php');
                if ($mailEnviado) {
                ?>
                    <div class="col-md-12">
                        <div class="row" style="background-color: #428bca;">
                            <div class="col-md-12"></div>
                        </div>
                    </div>
                    <div class="row">&nbsp;</div>
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Certificado solicitado por <?php echo $colegiado['nombre'].' '.$colegiado['apellido']; ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-success" role="alert">
                                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                <span><strong>&nbsp;El mail se envió con éxito al 
                                correo: </strong><?php echo $mailDestino; ?></span>
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
            }
            
        } else {
        ?>
            <div id="pagina">
                <h2>Se produjo un error al buscar al colegiado</h2>
            </div>
        <?php
        }
        ?>
        <!--BOTON VOLVER -->
        <div class="row">
            <div class="col-md-3" id="volver">
                <h3>Cerrar esta pestaña del navegador.</h3>
            </div>
        </div>
    <?php
    } else {
        require_once ('../../html/head.php');
        require_once ('../../html/encabezado.php');
    ?>
        <div class="row">
            <div class="col-md-12 alert alert-danger">
                <h3><?php echo $resultado['mensaje']; ?></h3>
            </div>
            <div class="row">&nbsp;</div>
            <div class="col-md-12">
                <h3>Cerrar esta pestaña del navegador, el mail fue enviado con éxito.</h3>
            </div>
        </div>
    <?php
    }
    ?>
<!--</body>-->

