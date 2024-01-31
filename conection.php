<?php
function conectar()
{
//	$link = mysql_connect("localhost", "redirisc_colmed1", "kalisti");
//	if ($link && mysql_select_db("redirisc_colmed1")){

//	$link = mysql_connect("localhost", "root", "");
    
	$link = new mysqli("192.168.2.50", "hugo", "hugo", "pruebas_colmed1");
        
        if ($link -> connect_error) {
            die('Error de Conexión (' . $link -> connect_errno . ') '
                    . $link -> connect_error);
        }
        else
        {
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