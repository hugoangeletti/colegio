<?php

function conectar() {
//	$link = mysql_connect("localhost", "redirisc_colmed1", "kalisti");
//	if ($link && mysql_select_db("redirisc_colmed1")){
//	$link = mysql_connect("localhost", "root", "");

    $link = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_SELECTED);

    if ($link->connect_error) {
        return $link->connect_error;
    } else {
        return ($link);
    }

//      if ($link){
//		return ($link);
//	}
//	else{
//		return(false);
//	}
}

?>