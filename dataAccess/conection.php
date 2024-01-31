<?php
function conectar()
{
//    $link = mysql_connect("192.168.2.50", "hugo", "hugo");
//    if ($link && mysql_select_db("colmed1")){
//            return ($link);
//    }
//    else{
//            return(false);
//    }
    
    $link = new mysqli("192.168.2.50", "hugo", "hugo", "colmed1");
    if ($link->connect_error) {
        return $link->connect_error;
    } else {
        return ($link);
    }    
}
