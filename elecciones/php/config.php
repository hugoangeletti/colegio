<?php

session_start(); //comentar esta linea si no se trabaja con sesiones
ini_set('default_charset', 'utf8');
/*
  @Maximiliano
  max.angletti@gmail.com
 */

$localhost = false; //define si se esta trabajando a modo local o no
$proyecto = "Elecciones - Colegio de Médicos Distrito I";

if (!$localhost) {
    switch ($_SERVER['HTTP_HOST']) {
        case "www.colmed1.com":
            define("URL_TOTAL", "http://www.colmed1.com/colegio/elecciones/");
            break;
        case "192.168.2.50":
            define("URL_TOTAL", "http://192.168.2.50/colegio/elecciones/");
            break;
    }

    define("DB_USER", "hugo");
    define("DB_PASS", "hugo");
    define("DB_HOST", "192.168.2.50");
    define("DB_SELECTED", "colmed1");
} else {
    define("URL_TOTAL", "http://localhost/elecciones/");
    define("DB_USER", "hugo");
    define("DB_PASS", "hugo");
    define("DB_HOST", "192.168.2.50");
    define("DB_SELECTED", "pruebas_colmed1");
}

define("PATH_HOME", URL_TOTAL);
define("PATH_CSS", URL_TOTAL . "css/");
define("PATH_PHP", URL_TOTAL . "php/");
define("PATH_HTML", URL_TOTAL . "html/");
define("PATH_JS", URL_TOTAL . "js/");
define("PATH_ADMIN", URL_TOTAL . "admin/");
define("PATH_CONTROLLER", URL_TOTAL . "controller/");
define("PATH_IMAGES", URL_TOTAL . "images/");
define("PATH_DOCUMENTS", URL_TOTAL . "documents/");

if (date("m") >= 6) {
    $periodoActual = date("Y");
} else {
    $periodoActual = date("Y") - 1;
}
define("PERIODO_ACTUAL", $periodoActual);
?>
