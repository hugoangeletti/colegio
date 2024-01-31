<?php

function obtenerTipoTramitePorId($idTipoTramite)
{
    $sql = "SELECT *
            FROM tipomesaentrada
            WHERE IdTipoMesaEntrada = ".$idTipoTramite;
    $res = conectar() -> query($sql);
    
    return $res;
}
?>
