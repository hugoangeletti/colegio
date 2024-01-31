<?php

//Inicio la sesión 
session_start();
//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO 
if ($_SESSION["autentificado"] != "SI") {
    //si no existe, envio a la página de autentificacion 
    header("Location: login.php");
    //ademas salgo de este script 
    exit();
}
/*
if (isset($_SESSION['user_last_activity']) && (time() - $_SESSION['user_last_activity'] > 1200)) {
    // last request was more than 20 minutes ago
    header("Location: logout.php");

    exit();
}
 * 
 */
$_SESSION['user_last_activity'] = time(); // update last activity time stamp