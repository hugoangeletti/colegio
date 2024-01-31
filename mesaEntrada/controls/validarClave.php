<?php

session_start();
require_once '../dataAccess/conection.php';
conectar();
require_once '../dataAccess/colegiadoLogic.php';
require_once '../dataAccess/tipoMovimientoLogic.php';
require_once '../dataAccess/estadoTesoreriaLogic.php';
require_once '../dataAccess/funciones.php';
require_once '../dataAccess/mesaEntradaLogic.php';

if (isset($_POST['clave']) && ($_POST['clave'])) {
    $clave = $_POST['clave'];
} else {
    $clave = -1;
}

if (isset($_POST['continue']) && ($_POST['continue'])) {
    $continue = $_POST['continue'];
} else {
    $continue = "../";
}

if (isset($_POST['title']) && ($_POST['title'])) {
    $title = $_POST['title'];
} else {
    $title = "Formulario de Validación de Contraseña";
}

$usuario = mysqli_query(conectar(), "SELECT * FROM usuario WHERE Usuario='" . $_SESSION['user'] . "' AND Clave='" . $clave . "'");

if (mysqli_num_rows($usuario) > 0) {
    $_SESSION['intentos'] = 0;
    header("Location: " . $continue);
} else {
    if ($_SESSION['intentos'] < 2) {
        $_SESSION['intentos'] = $_SESSION['intentos'] + 1;
        header("Location: formularioValidarClave.php?title=" . $title . "&continue=" . $continue);
    } else {
        header("Location: logout.php");
    }
}



    