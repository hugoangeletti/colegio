<?php
//require_once ('../dataAccess/config.php');
//permisoLogueado();
$borrado = 0;
//unlink('../archivos/recibos/2023/03/RE_90320.pdf');

foreach(glob("../archivos/chequera/") as $archivos_carpeta){             
    print_r($archivos_carpeta);
    echo '<br>';

    if (is_dir($archivos_carpeta)){
        foreach(glob($archivos_carpeta."/*") as $archivos_carpeta2){             
            print_r($archivos_carpeta2);
            echo '<br>';

            if (is_dir($archivos_carpeta2)){
                foreach(glob($archivos_carpeta2."/*") as $archivos_carpeta3){
                    print_r($archivos_carpeta3);
                    echo '<br>';
                    if (is_dir($archivos_carpeta3)){
                        foreach(glob($archivos_carpeta3."/*") as $archivos_carpeta4){
                            print_r($archivos_carpeta4);
                            echo '<br>';
                            if (is_dir($archivos_carpeta4)){
                                rmdir($archivos_carpeta4);
                            } else {
                                unlink($archivos_carpeta4);
                            }
                            $borrado++;
                        }            
                        rmdir($archivos_carpeta3);
                    } else {
                        unlink($archivos_carpeta3);
                    }
                    $borrado++;
                }            
                rmdir($archivos_carpeta2);
            } else {
                unlink($archivos_carpeta2);
            }
            $borrado++;
        }            
        rmdir($archivos_carpeta);
    } else {
        unlink($archivos_carpeta);
    }
    $borrado++;
}            

echo 'borrados->'.$borrado.'<br>';

