<?php
session_start(); //comentar esta linea si no se trabaja con sesiones
//require_once 'dataAccess/sessionControl.php';
//ini_set('default_charset', 'utf8');

date_default_timezone_set("America/Argentina/Buenos_Aires");
//error_reporting(0);
//if (!isset($_SESSION['user_id'])) 
//{
//    $_SESSION['user_id'] = 1;
//}

$localhost = TRUE; //define si se esta trabajando a modo local o no
$proyecto = "Portal del Colegio de Medicos Distrito I";

if ($localhost) 
{
   switch ($_SERVER['HTTP_HOST']) 
   {
       case "www.colmed1.com":
       case "webservices.colmed1.com.ar":
            define("URL_TOTAL", "http://www.colmed1.com/colegio/");
            define("DB_USER", "hugo");
            define("DB_PASS", "hugo");
            define("DB_HOST", "192.168.2.50");
            define("DB_SELECTED", "pruebas_colmed1");
            define("PATH_PDF", "/colegio/");
           break;
       case "www.colmed1.com/desarrollo":
       case "www.colmed1.com/desarrollo8":
            define("URL_TOTAL", "http://www.colmed1.com/desarrollo/colegio/");
            define("DB_USER", "hugo");
            define("DB_PASS", "hugo");
            define("DB_HOST", "192.168.2.50");
            define("DB_SELECTED", "pruebas_colmed1");
            define("PATH_PDF", "/desarrollo8/colegio");
           break;
       case "localhost":
           define("URL_TOTAL", "/colegio/");
            define("DB_USER", "hugo");
            define("DB_PASS", "hugo");
            define("DB_HOST", "192.168.2.50");
            define("DB_SELECTED", "pruebas_colmed1");
            define("PATH_PDF", "/desarrollo8/colegio");
           break;
   }

} 
else 
{
    define("URL_TOTAL", "http://www.colmed1.com/desarrollo8/colegio/");
    define("DB_USER", "hugo");
    define("DB_PASS", "hugo");
    define("DB_HOST", "192.168.2.50");
    define("DB_SELECTED", "pruebas_colmed1");
    define("PATH_PDF", "/desarrollo8/colegio");
}
/*
 * paths para utilizar absoluto y permitir
 * url amigable a traves de .htaccess
define("PATH_HOME", URL_TOTAL);
define("PATH_CSS", URL_TOTAL . "css/");
define("PATH_CONTROLS", URL_TOTAL . "controles/");
define("PATH_HTML", URL_TOTAL . "html/");
define("PATH_JS", URL_TOTAL . "js/");
define("PATH_DATAACCESS", URL_TOTAL . "dataAccess/");
define("PATH_IMAGES", URL_TOTAL . "images/");
 */

define("FTP_ARCHIVOS", "ftp://webcolmed:web.2017@192.168.2.50:21");
define("MAIL_MASIVO", "noreply@colmed1.org.ar");
//define("MAIL_MASIVO_PASS", "YWY1NDE4OTMwNGZlODE2NDRhNzQzMjI3");
define("MAIL_MASIVO_PASS", "ColMed@NRPL3214??");

define("LIBRERIA_TCPDF", "TCPDF-php8-main/tcpdf.php");
//define("LIBRERIA_TCPDF", "tcpdf/tcpdf.php");

//define("MAIL_MASIVO", "sistemas@colmed1.org.ar");
//define("MAIL_MASIVO_PASS", "@sistem@s_1965");

require_once (__DIR__) . '/funcionesSeguridad.php';

