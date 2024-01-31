<?php

require_once '../php/config.php';
require_once '../php/configPhpTwig.php';
require_once '../php/dataAccess/funcionConector.php';
require_once '../php/funciones/funcionesPhp.php';
require_once '../php/funciones/funcionesEleccion.php';

/* SECCION CORRESPONDIENTE */
switch ($_GET['seccion']) {
    case "resultados":

        $datosEleccion = obtenerEleccionVigente();
        $anioH = $datosEleccion['Anio'] + 4;
        /* Localidad La Plata: '081' */
        $localidad = '081';

        $eleccionesLocalidad = obtenerEleccionLocalidadPorIdEleccionPorLocalidad($datosEleccion['IdElecciones'], $localidad);

        $datosTabla = obtenerEleccionesResultado($datosEleccion['Anio'], $localidad);

        $maximo = 0;
        if ($datosTabla != null) {
            foreach ($datosTabla as $ElemArray) {
                $maximo += $ElemArray['Cantidad'];
            }
        }

        $positivos = obtenerVotosPorTipo($datosEleccion['Anio'], "C", $localidad);
        $anulados = obtenerVotosPorTipo($datosEleccion['Anio'], "A", $localidad);
        $blancos = obtenerVotosPorTipo($datosEleccion['Anio'], "B", $localidad);
        $cociente = number_format($positivos['Cantidad'] / $eleccionesLocalidad['CantidadDelegados'], 2, ',', ' ');

        /* SETEANDO ARRAY INFO */

        $info['anio'] = $datosEleccion['Anio'];
        $info['anioH'] = $anioH;

        $info['delegados'] = $eleccionesLocalidad['CantidadDelegados'];
        $info['maximo'] = $maximo;
        $info['datosTabla'] = $datosTabla;

        $info['positivos'] = $positivos['Cantidad'];
        $info['anulados'] = $anulados['Cantidad'];
        $info['blancos'] = $blancos['Cantidad'];
        $info['cociente'] = $cociente;

        $url = "/fake.twig";
        break;
    case "fake":
        $datosEleccion = obtenerEleccionVigente();
        $anioH = $datosEleccion['Anio'] + 4;
        /* Localidad La Plata: '081' */
        $localidad = '081';

        $eleccionesLocalidad = obtenerEleccionLocalidadPorIdEleccionPorLocalidad($datosEleccion['IdElecciones'], $localidad);

        $datosTabla = obtenerEleccionesResultado($datosEleccion['Anio'], $localidad);

        $maximo = 0;
        if ($datosTabla != null) {
            foreach ($datosTabla as $ElemArray) {
                $maximo += $ElemArray['Cantidad'];
            }
        }

        $positivos = obtenerVotosPorTipo($datosEleccion['Anio'], "C", $localidad);
        $anulados = obtenerVotosPorTipo($datosEleccion['Anio'], "A", $localidad);
        $blancos = obtenerVotosPorTipo($datosEleccion['Anio'], "B", $localidad);
        $cociente = $positivos['Cantidad'] / $eleccionesLocalidad['CantidadDelegados'];

        /* SETEANDO ARRAY INFO */

        $info['anio'] = $datosEleccion['Anio'];
        $info['anioH'] = $anioH;

        $info['delegados'] = $eleccionesLocalidad['CantidadDelegados'];
        $info['maximo'] = $maximo;
        $info['datosTabla'] = $datosTabla;

        $info['positivos'] = $positivos['Cantidad'];
        $info['anulados'] = $anulados['Cantidad'];
        $info['blancos'] = $blancos['Cantidad'];
        $info['cociente'] = $cociente;

        $url = "/resultados.twig";
        break;
    default :
        $url = "/paginaError.twig";
        break;
}

/* SECCION SELECCIONADA */
$template = $twigHtml->loadTemplate($url);

$template->display($info);
