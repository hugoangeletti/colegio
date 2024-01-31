<?php

require_once 'php/config.php';


if ($localhost) {
    header("Location:" . PATH_CONTROLLER . "controladorVista.php?seccion=resultados");
} else {
    header("Location:" . PATH_HOME . "resultados");
}


