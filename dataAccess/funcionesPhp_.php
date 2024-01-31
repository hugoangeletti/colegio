<?php
/*
 * Función de sanearDatos por posibles ataques XSS
 */

function sanearDatos($tags) {
    $tags = strip_tags($tags);
    $tags = stripslashes($tags);
    $tags = htmlentities($tags);
    return $tags;
}

/*
 * Función de encriptación aleatoria, cada vez que se ejecuta.
 */

function blow_crypt($input, $rounds = 7) {
    $salt = "";
    $salt_chars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
    for ($i = 0; $i < 22; $i++) {
        $salt .= $salt_chars[array_rand($salt_chars)];
    }
    return crypt($input, sprintf('$2a$%02d$', $rounds) . $salt);
}

/*
 * Función de triple encriptación para contraseñas y usuarios seguros.
 */

function hashData($string) {
    //$string = hash("haval224,4", md5(crypt($string, "$2a$%02d$")));
    $string = sha1($string);
    return $string;
}

/*
 * Función para validación correcta de fecha, en base al un formato
 * pasado por parámetro o por defecto Y-m-d
 */

function validateDate($date, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function urlAmigable($string, $replacement = '-', $map = array()) {
    if (is_array($replacement)) {
        $map = $replacement;
        $replacement = '+';
    }
    $quotedReplacement = preg_quote($replacement, '/');

    $merge = array(
        '/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
        '/\\s+/' => $replacement,
        sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
    );

    $_transliteration = array(
        '/ä|æ|ǽ/' => 'ae',
        '/ö|œ/' => 'oe',
        '/ü/' => 'ue',
        '/Ä/' => 'Ae',
        '/Ü/' => 'Ue',
        '/Ö/' => 'Oe',
        '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
        '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
        '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
        '/ç|ć|ĉ|ċ|č/' => 'c',
        '/Ð|Ď|Đ/' => 'D',
        '/ð|ď|đ/' => 'd',
        '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
        '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
        '/Ĝ|Ğ|Ġ|Ģ/' => 'G',
        '/ĝ|ğ|ġ|ģ/' => 'g',
        '/Ĥ|Ħ/' => 'H',
        '/ĥ|ħ/' => 'h',
        '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
        '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
        '/Ĵ/' => 'J',
        '/ĵ/' => 'j',
        '/Ķ/' => 'K',
        '/ķ/' => 'k',
        '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
        '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
        '/Ñ|Ń|Ņ|Ň/' => 'N',
        '/ñ|ń|ņ|ň|ŉ/' => 'n',
        '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
        '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
        '/Ŕ|Ŗ|Ř/' => 'R',
        '/ŕ|ŗ|ř/' => 'r',
        '/Ś|Ŝ|Ş|Š/' => 'S',
        '/ś|ŝ|ş|š|ſ/' => 's',
        '/Ţ|Ť|Ŧ/' => 'T',
        '/ţ|ť|ŧ/' => 't',
        '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
        '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
        '/Ý|Ÿ|Ŷ/' => 'Y',
        '/ý|ÿ|ŷ/' => 'y',
        '/Ŵ/' => 'W',
        '/ŵ/' => 'w',
        '/Ź|Ż|Ž/' => 'Z',
        '/ź|ż|ž/' => 'z',
        '/Æ|Ǽ/' => 'AE',
        '/ß/' => 'ss',
        '/Ĳ/' => 'IJ',
        '/ĳ/' => 'ij',
        '/Œ/' => 'OE',
        '/ƒ/' => 'f'
    );

    $map = $map + $_transliteration + $merge;
    return preg_replace(array_keys($map), array_values($map), $string);
}

function invertirFecha($fecha) {
    // Invierte las que vienen con el formato dd-mm-aaaa
    $fechaInvertir = explode("-", $fecha);
    $fechaInvertida = $fechaInvertir[2] . "-" . $fechaInvertir[1] . "-" . $fechaInvertir[0];
    return $fechaInvertida;
}

function dateadd($date, $dd = 0, $mm = 0, $yy = 0, $hh = 0, $mn = 0, $ss = 0) {
    $date_r = getdate(strtotime($date));

    $date_result = date("Y-m-d h:i:s", mktime(($date_r["hours"] + $hh), ($date_r["minutes"] + $mn), ($date_r["seconds"] + $ss), ($date_r["mon"] + $mm), ($date_r["mday"] + $dd), ($date_r["year"] + $yy)));

    return $date_result;
}

function sumarRestarSobreFecha($fecha, $cant, $tipo = 'day', $accion = '+') {
    $nuevafecha = strtotime($accion . $cant . ' ' . $tipo, strtotime($fecha));
    $nuevafecha = date('Y-m-d', $nuevafecha);

    return $nuevafecha;
}

function encriptarPass($pass) 
{

    $pass_encriptada1 = md5($pass); //Encriptacion nivel 1
    $pass_encriptada2 = crc32($pass_encriptada1); //Encriptacion nivel 1
    $pass_encriptada3 = crypt($pass_encriptada2, "xtemp"); //Encriptacion nivel 2
    $pass_encriptada4 = sha1("xtemp" . $pass_encriptada3); //Encriptacion nivel 3
    return $pass_encriptada4;
}

function validar_clave($clave, &$error_clave) {
    if (strlen($clave) < 8) {
        $error_clave = "La clave debe tener al menos 8 caracteres.";
        return false;
    }
    if (strlen($clave) > 16) {
        $error_clave = "La clave no puede tener más de 16 caracteres.";
        return false;
    }
    if (preg_match('`[\?\¿\¡\'\"\!\ª\º\%\|\@\#\·\$\~\€\¬\&\/\(\)\=\[\]\}\{\.\:\,\;\<\>\^\`\+\*\¨\ç\Ç]`', $clave)) {
        $error_clave = "La clave que está queriendo ingresar contiene caracteres que no son permitidos.";
        return false;
    }
    if (!preg_match('`[a-z]`', $clave)) {
        $error_clave = "La clave debe tener al menos una letra minúscula.";
        return false;
    }
    if (!preg_match('`[A-Z]`', $clave)) {
        $error_clave = "La clave debe tener al menos una letra mayúscula.";
        return false;
    }
    if (!preg_match('`[0-9]`', $clave)) {
        $error_clave = "La clave debe tener al menos un caracter numérico.";
        return false;
    }
    if (!preg_match('`[\-\_]`', $clave)) {
        $error_clave = "La clave debe tener al menos un caracter especial.";
        return false;
    }

    $error_clave = "";
    return true;
}

function rellenarCeros($entero, $largo) {
    // Limpiamos por si se encontraran errores de tipo en las variables
    $entero = (int) $entero;
    $largo = (int) $largo;

    $relleno = '';

    /**
     * Determinamos la cantidad de caracteres utilizados por $entero
     * Si este valor es mayor o igual que $largo, devolvemos el $entero
     * De lo contrario, rellenamos con ceros a la izquierda del número
     * */
    if (strlen($entero) < $largo) {
        $relleno = str_pad((int) $entero, $largo, "0", STR_PAD_LEFT);
        return $relleno;
    }
    return $relleno . $entero;
}

function cambiarFechaaformatoBD($fecha)
{ // pasa de dd/mm/AAAA a AAAA-mm-dd

    if ($fecha!="")
    {
        $fechaaux=explode('/',$fecha);
        $fecha=$fechaaux[2]."-".$fechaaux[1]."-".$fechaaux[0];

    }
    else
    {
        $fecha='0000-00-00';
    }
    return $fecha;
}

function cambiarFechaFormatoParaMostrar($fecha)
{ // pasa del AAAA-mm-dd a dd/mm/AAAA

if ($fecha!="" && $fecha !="0000-00-00")
{
$fechaaux=explode('-',$fecha);
$fecha=$fechaaux[2]."/".$fechaaux[1]."/".$fechaaux[0];

}
else
{
    $fecha="";
}
return $fecha;
}

function cambiarFechaCortaFormatoParaMostrar($fecha)
{ 
    // pasa del AAAA-mm-dd a dd/mm/AAAA
    if ($fecha!="" && $fecha !="0000-00-00")
    {
    $fechaaux=explode('-',$fecha);
    $fecha=$fechaaux[2]."/".$fechaaux[1]."/".substr($fechaaux[0], 2, 3);
    } else {
        $fecha="";
    }
    return $fecha;
}

function rand_str($length =10, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
{
    // Length of character list
    $chars_length = (strlen($chars) - 1);

    // Start our string
    $string = $chars{rand(0, $chars_length)};
    
    // Generate random string
    for ($i = 1; $i < $length; $i = strlen($string))
    {
        // Grab a random character from our list
        $r = $chars{rand(0, $chars_length)};
        
        // Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
    }
    
    // Return the string
    return $string;
}

function NombreDeLaSemana($diaSemana){
    switch ($diaSemana) {
        case 0:
            $diaSemanaName = 'Domingo';
            break;
        case 1:
            $diaSemanaName = 'Lunes';
            break;
        case 2:
            $diaSemanaName = 'Martes';
            break;
        case 3:
            $diaSemanaName = 'Miércoles';
            break;
        case 4:
            $diaSemanaName = 'Jueves';
            break;
        case 5:
            $diaSemanaName = 'Viernes';
            break;
        case 6:
            $diaSemanaName = 'Sábado';
            break;

        default:
            $diaSemanaName = $diaSemana;
            break;
        
    }
    
    return $diaSemanaName;
}


function calcular_edad($fecha_nac)
{
    if ($fecha_nac!="")
    {    
    
	$dia=date("j");
	$mes=date("n");
	$anno=date("Y");

	//descomponer fecha de nacimiento
	$dia_nac=substr($fecha_nac, 8, 2);
	$mes_nac=substr($fecha_nac, 5, 2);
	$anno_nac=substr($fecha_nac, 0, 4);

	if($mes_nac>$mes){
		$calc_edad= $anno-$anno_nac-1;
	}else{
		if($mes==$mes_nac AND $dia_nac>$dia){
			$calc_edad= $anno-$anno_nac-1;  
		}else{
			$calc_edad= $anno-$anno_nac;
		}
	}
	return $calc_edad. " A&ntilde;os";
    }
    else {
     return "";   
    }
}

function calcular_antiguedad($fechaCalculo, $fechaBase)
{
    if ($fechaCalculo!="")
    {    
	$dia=substr($fechaBase, 8, 2);
	$mes=substr($fechaBase, 5, 2);
	$anno=substr($fechaBase, 0, 4);

	//descomponer fecha 
	$dia_nac=substr($fechaCalculo, 8, 2);
	$mes_nac=substr($fechaCalculo, 5, 2);
	$anno_nac=substr($fechaCalculo, 0, 4);

	if($mes_nac>$mes){
            $calc_edad = $anno-$anno_nac-1;
	}else{
            if($mes==$mes_nac AND $dia_nac>$dia){
                $calc_edad = $anno-$anno_nac-1;  
            }else{
                $calc_edad = $anno-$anno_nac;
            }
	}
        if ($calc_edad < 5) {
            $antiguedad = 1;
        } else {
            $antiguedad = 2;
        }
	return $antiguedad;
    }
    else {
        return NULL;   
    }
}

function obtenerMes($mes)
{
    switch ($mes) {
        case 1:
            $mesRes = 'Enero';
            break;
        case 2:
            $mesRes = 'Febrero';
            break;
        case 3:
            $mesRes = 'Marzo';
            break;
        case 4:
            $mesRes = 'Abril';
            break;
        case 5:
            $mesRes = 'Mayo';
            break;
        case 6:
            $mesRes = 'Junio';
            break;
        case 7:
            $mesRes = 'Julio';
            break;
        case 8:
            $mesRes = 'Agosto';
            break;
        case 9:
            $mesRes = 'Septiembre';
            break;
        case 10:
            $mesRes = 'Octubre';
            break;
        case 11:
            $mesRes = 'Noviembre';
            break;
        case 12:
            $mesRes = 'Diciembre';
            break;

        default:
            $mesRes = '';
            break;
    }

    return $mesRes;
    }

function obtenerEstadoFap($estado)
{
    switch ($estado) {
        case 'C':
            $estadoRes = 'Consulta';
            break;
        case 'A':
            $estadoRes = 'Aprobado FAP';
            break;
        case 'D':
            $estadoRes = 'Desaprobado';
            break;
        case 'E':
            $estadoRes = 'En sistema';
            break;
        case 'R':
            $estadoRes = 'Cerrado';
            break;
        case 'G':
            $estadoRes = 'Litigar sin gasto';
            break;
        case 'M':
            $estadoRes = 'Mediación';
            break;

        default:
            $estadoRes = 'Sin estado';
            break;
    }

    return $estadoRes;
}

function obtenerNumeroRomano($numero)
{
    switch ($numero) {
        case 1:
            $romano = 'I';
            break;
        case 2:
            $romano = 'II';
            break;
        case 3:
            $romano = 'III';
            break;
        case 4:
            $romano = 'IV';
            break;
        case 5:
            $romano = 'V';
            break;
        case 6:
            $romano = 'VI';
            break;
        case 7:
            $romano = 'VII';
            break;
        case 8:
            $romano = 'VIII';
            break;
        case 9:
            $romano = 'IX';
            break;
        case 10:
            $romano = 'X';
            break;

        default:
            $romano = '';
            break;
    }

    return $romano;
}

function obtenerDetalleIncisoEspecialistaArt8($inciso)
{
    switch ($inciso) {
        case 'a':
            $detalle = '';
            break;
        case 'b':
            $detalle = 'Universitario';
            break;
        case 'c':
            $detalle = 'CONFEMECO';
            break;
        case 'd':
            $detalle = 'Por puntaje';
            break;
        case 'e':
            $detalle = 'Curso Superior';
            break;
        case 'f':
            $detalle = 'Residencia';
            break;

        default:
            $detalle = '';
            break;
    }

    return $detalle;
}

function obtenrTipoPago($detalleTipoPago)
{
    switch ($detalleTipoPago) {
        case 1:
            $detalle = '1';
            break;
        case 2:
            $detalle = 'Plan de Pagos';
            break;
        case 3:
            $detalle = 'Cuota Colegiación';
            break;
        case 4:
            $detalle = 'Nota de deuda';
            break;
        case 5:
            $detalle = '5';
            break;
        case 6:
            $detalle = '6';
            break;
        case 7:
            $detalle = 'Cursos';
            break;
        case 8:
            $detalle = 'Pago Total';
            break;
        case 9:
            $detalle = '9';
            break;

        default:
            $detalle = $detalleTipoPago;
            break;

    }
    return $detalle;
}