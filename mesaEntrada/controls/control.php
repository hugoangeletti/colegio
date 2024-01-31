<?php

include ("../dataAccess/conection.php");
include('../dataAccess/funciones.php');
conectar();
$userName = $_POST['userName'];
$clave = $_POST['clave'];

/*
  $pass = stripslashes($_POST["clave"]);
  $pass = strip_tags($pass);
  $pass_encriptada4 = encriptarPass($pass);

  $usuario=mysqli_query(conectar(), "select * from usuario where Usuario='".$userName."' and Clave='".$pass_encriptada4."'");
 */
$usuario = mysqli_query(conectar(), "select * from usuario where Usuario='" . $userName . "' and Clave='" . $clave . "'");


if (mysqli_num_rows($usuario) > 0) {
    //$tupla=mysql_fetch_array($usuario) or die ("Error en la consulta del usuario");
    $tupla = mysqli_fetch_array($usuario) or die(mysqli_error());
    if ($tupla) {
        session_start();
        $_SESSION['login'] = "SI"; //Le damos el valor SI a la sesion login.
        $_SESSION['user'] = $tupla['Usuario']; //Le damos el valor del nombre de usuario a la sesion user.
        $_SESSION["autentificado"] = "SI";
        $_SESSION["idUsuario"] = $tupla['Id'];
        $_SESSION['intentos'] = 0;
        $_SESSION['user_last_activity'] = time();
        header("Location: administracion.php");
    } else {
        header("Location: login.php?error=SI");
    }
} else {
    header("Location: login.php?error=SI");
}
?>