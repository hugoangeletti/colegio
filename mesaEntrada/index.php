<?php

//	include('controls/login.php');

if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
    $uri = 'https://';
} else {
    $uri = 'http://';
}
$uri .= $_SERVER['HTTP_HOST'];
//header('Location: '.$uri.'/administracion/controls/login.php');
//header('Location: '.$uri.'/colegio/adminColMed1/controls/login.php');
header('Location: controls/login.php');
exit;
?>
