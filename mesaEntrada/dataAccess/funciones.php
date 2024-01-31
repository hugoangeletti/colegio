<?php

function encriptarPass($pass) {
    $pass_encriptada1 = md5($pass); //Encriptacion nivel 1
    $pass_encriptada2 = crc32($pass_encriptada1); //Encriptacion nivel 1
    $pass_encriptada3 = crypt($pass_encriptada2, "xtemp"); //Encriptacion nivel 2
    $pass_encriptada4 = sha1("xtemp" . $pass_encriptada3); //Encriptacion nivel 3

    return $pass_encriptada4;
}

/*
 * ***************************************************************************
 */

function obtenerNombreDia($fecha) {
    $fecha = strtotime($fecha);

    switch (date('w', $fecha)) {
        case 0: $dia = "Domingo";
            break;
        case 1: $dia = "Lunes";
            break;
        case 2: $dia = "Martes";
            break;
        case 3: $dia = "Miércoles";
            break;
        case 4: $dia = "Jueves";
            break;
        case 5: $dia = "Viernes";
            break;
        case 6: $dia = "Sábado";
            break;
    }
    return $dia;
}

/*
 * ***************************************************************************
 */

function obtenerNombreMes($fecha) {
    $fecha = strtotime($fecha);

    switch (date('m', $fecha)) {
        case 1: $mes = "Enero";
            break;
        case 2: $mes = "Febrero";
            break;
        case 3: $mes = "Marzo";
            break;
        case 4: $mes = "Abril";
            break;
        case 5: $mes = "Mayo";
            break;
        case 6: $mes = "Junio";
            break;
        case 7: $mes = "Julio";
            break;
        case 8: $mes = "Agosto";
            break;
        case 9: $mes = "Septiembre";
            break;
        case 10: $mes = "Octubre";
            break;
        case 11: $mes = "Noviembre";
            break;
        case 12: $mes = "Diciembre";
            break;
    }
    return $mes;
}

/*
 * ***************************************************************************
 */

function mostrarSoloDia($fecha) {
    $fecha = strtotime($fecha);

    return date('d', $fecha);
}

/*
 * ***************************************************************************
 */

function mostrarSoloMes($fecha) {
    $fecha = strtotime($fecha);

    return date('m', $fecha);
}

/*
 * ***************************************************************************
 */

function mostrarSoloAnio($fecha) {
    $fecha = strtotime($fecha);

    return date('Y', $fecha);
}

/*
 * ***************************************************************************
 */

function pasarAMayuscula($string) {
    return strtoupper(strtr($string, 'éáíúęóąśłżźćń', 'ÉÁÍÚĘÓĄŚŁŻŹĆŃ'));
}

/*
 * ***************************************************************************
 */

function pasarAMinuscula($string) {
    return strtolower(strtr($string, 'ÉÁÍÚĘÓĄŚŁŻŹĆŃ', 'éáíúęóąśłżźćń'));
}

/*
 * ***************************************************************************
 */

function calcularDiferenciaFecha($fecha) {
    $dias = explode('-', $fecha, 3);
    $dias = mktime(0, 0, 0, $dias[1], $dias[0], $dias[2]);
    $diferencia = (int) ((time() - $dias) / 31556926 );
    return $diferencia;
}

function calcularEdad($fecha) {
    return floor((time() - strtotime($fecha)) / 31556926);
}

/*
 * ***************************************************************************
 */

function calcularAntiguedad($fecha) {
    list($Y, $m, $d) = explode("-", $fecha);
    return( "0601" < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y );
}

function obtenerPeriodo() {
    if ((date("Y") . "-06-01" <= date("Y-m-d")) && (date("Y-m-d") <= (date("Y") . "-12-31"))) {
        return date("Y");
    } elseif ((date("Y") . "-01-01" <= date("Y-m-d")) && (date("Y-m-d") <= (date("Y") . "-05-31"))) {
        return (date("Y") - 1);
    } else {
        return -1;
    }
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

function rellenarCerosAtras($entero, $largo) {
    // Limpiamos por si se encontraran errores de tipo en las variables
    $entero = $entero;
    $largo = (int) $largo;

    $relleno = '';

    /**
     * Determinamos la cantidad de caracteres utilizados por $entero
     * Si este valor es mayor o igual que $largo, devolvemos el $entero
     * De lo contrario, rellenamos con ceros a la izquierda del número
     * */
    if (strlen($entero) < $largo) {
        $relleno = str_pad($entero, $largo, "0", STR_PAD_RIGHT);
        return $relleno;
    }
    return $entero . $relleno;
}

/*
 * ***************************************************************************
 */

function sumarFecha($fecha, $cantidad, $operacion, $tipo) {
    $nuevafecha = strtotime($operacion . $cantidad . " " . $tipo, strtotime($fecha));
    $nuevafecha = date('Y-m-d', $nuevafecha);

    return $nuevafecha;
}

function sumarFechaCompleto($fecha, $cantidad, $operacion, $tipo) {
    $nuevafecha = strtotime($operacion . $cantidad . " " . $tipo, strtotime($fecha));
    $nuevafecha = date('Y-m-d', $nuevafecha);

    return $nuevafecha;
}

function invertirFecha($fecha) {
    // Invierte las que vienen con el formato dd-mm-aaaa
    $fechaInvertir = explode("-", $fecha);
    $fechaInvertida = $fechaInvertir[2] . "-" . $fechaInvertir[1] . "-" . $fechaInvertir[0];
    return $fechaInvertida;
}

function mostrarLeyendaEspecialidad($codigo, $texto = null) {
    switch ($codigo) {
        case 1:
            return "<p class='codError'>Ya tiene título de especialista jerarquizado.</p>";
            break;
        case 2:
            return "<p class='codError'>Ya tiene título de especialista consultor.</p>";
            break;
        case 3:
            return "<p class='codError'>No cumple con el mínimo de 5 años de vigencia.</p>";
            break;
        case 4:
            return "<p class='codError'>No cumple con el mínimo de 15 años de vigencia.</p>";
            break;
        case 5:
            return "<p class='codError'>No tiene título de especialista jerarquizado.</p>";
            break;
        case 6:
            return "<p class='codError'>Esta especialidad está vencida.</p>";
            break;
        case 7:
            return "<p class='codVerde'>Ya no es necesario recertificar esta especialidad.</p>";
            break;
        case 8:
            return "<p class='codError'>Esta especialidad ya está en trámite.</p>";
            break;
        case 9:
            return "<p class='codVerde'>Ya tiene esta calificación agregada.</p>";
            break;
        case 10:
            return "<p class='codError'>No tiene la especialidad cabecera.</p>";
            break;
        case 11:
            return "<p class='codError'>Título otorgado por el Ministerio de Salud de la Nación.</p>";
            break;
        case 12:
            return "<p class='codError'>La caducidad del Título Especialista es " . $texto . ".</p>";
            break;
    }
}

function mostrarNombreTramiteEspecialidad($tipoEspecialidad) {
    switch ($tipoEspecialidad) {
        case "E":
            return "Nueva Especialidad.";
            break;
        case "X":
            return "Nueva Especialidad Exceptuado Art.8";
            break;
        case "J":
            return "Jerarquizado.";
            break;
        case "C":
            return "Consultor.";
            break;
        case "A":
            return "Nueva Calificación Agregada.";
            break;
        case "R":
            return "Recertificación.";
            break;
        case "O":
            return "Nueva Especialidad de Otro Distrito.";
            break;
        case "N":
            return "Expedido por Ministerio de Salud de la Nación.";
            break;
    }
}

function obtenerPresidenteDistrito($distrito) {
    $sql = "SELECT *
            FROM distritos
            WHERE Distrito = " . $distrito;
    $res = conectar()->query($sql);

    return $res;
}

function pasarARomano($distrito) {
    switch ($distrito) {
        case 1:
            return 'I';
            break;
        case 2:
            return 'II';
            break;
        case 3:
            return 'III';
            break;
        case 4:
            return 'IV';
            break;
        case 5:
            return 'V';
            break;
        case 6:
            return 'VI';
            break;
        case 7:
            return 'VII';
            break;
        case 8:
            return 'VIII';
            break;
        case 9:
            return 'IX';
            break;
        case 10:
            return 'X';
            break;
    }
}

/*
 * Función para validación correcta de fecha, en base al un formato
 * pasado por parámetro o por defecto Y-m-d
 */

function validateDate($date, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

?>