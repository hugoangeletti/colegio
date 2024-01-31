<?php

//obtiene el detalle del estado en tesoreria
function estadoTesoreria($codigo){
    $sql = "SELECT Nombre, Codigo 
            FROM estadotesoreria 
            WHERE Codigo = ".$codigo;
    
    $res = conectar() -> query($sql);

    return $res;
}


//obtiene si es deudor
function estadoTesoreriaPorColegiado($idColegiado, $periodoActual){
    $sql = "SELECT COUNT(colegiadodeudaanualcuotas.id) as cantidad
            FROM colegiadodeudaanualcuotas 
            INNER JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
            INNER JOIN colegiado ON(colegiado.Id = colegiadodeudaanual.IdColegiado)
            LEFT JOIN agremiacionesdebito ON(agremiacionesdebito.IdColegiado = colegiado.Id)
            WHERE colegiado.Id = ".$idColegiado."
            AND colegiadodeudaanual.Periodo = ".$periodoActual."
            AND colegiadodeudaanualcuotas.Estado = 1
            AND colegiadodeudaanualcuotas.SegundoVencimiento < '".  sumarFechaCompleto(date("Y-m-d"), 7, '-', 'day')."' AND agremiacionesdebito.IdColegiado is null";
    
    $res = conectar() -> query($sql);

     if (!$res)
     {
        $retorno = -1;
     }
     else
     {
        if ($res -> num_rows == 0)
        {
            $retorno = 0;
        }
        else
        {
            $row = $res -> fetch_assoc();
            if ($row['cantidad'] > 1)
            {
                //es deudor
                $retorno = 1;
            }
            else
            {
                $retorno = 0;
            }
        //    return $retorno;
        }
    }
    
    //obtiene estado de periodos anteriores
    $sql = "SELECT COUNT(colegiadodeudaanualcuotas.id) as cantidad
            FROM colegiadodeudaanualcuotas 
            INNER JOIN colegiadodeudaanual ON(colegiadodeudaanual.Id = colegiadodeudaanualcuotas.IdColegiadoDeudaAnual)
            INNER JOIN colegiado ON(colegiado.Id = colegiadodeudaanual.IdColegiado)
            LEFT JOIN agremiacionesdebito ON(agremiacionesdebito.IdColegiado = colegiado.Id)
            WHERE colegiado.Id = ".$idColegiado."
            AND colegiadodeudaanual.Periodo < ".$periodoActual."
            AND colegiadodeudaanualcuotas.Estado = 1";
    
    $res = conectar() -> query($sql);

     if (!$res)
     {
        $retorno = -1;
     }
     else
     {
        if ($res -> num_rows == 0)
        {
            $retorno = 0;
        }
        else
        {
            $row = $res -> fetch_assoc();
            if ($row['cantidad'] >= 1)
            {
                //es deudor
                if($retorno == 1)
                {
                    $retorno = 2;
                }
                else
                {
                    $retorno = 3;
                }
            //    return $retorno;
            }
        }
    }
    
    //me fijo si debe cuotas de plan de pagos
    $sql = "SELECT COUNT(planpagoscuotas.id) as cantidad
            FROM planpagoscuotas
            INNER JOIN planpagos ON(planpagos.id = planpagoscuotas.idplanpagos)
            INNER JOIN colegiado ON(colegiado.Id = planpagos.IdColegiado)
            WHERE colegiado.Id = ".$idColegiado."
            AND planpagoscuotas.IdTipoEstadoCuota=1
            AND planpagoscuotas.Vencimiento <= '".date("Y-m-d")."'";
    
     $res = conectar() -> query($sql);

     if (!$res)
     {
        $retorno = -1;
     }
     else
     {
        if ($res -> num_rows == 0)
        {
            $retorno = 0;
        }
        else
        {
            $row = $res -> fetch_assoc();
            if($row['cantidad'] >= 1)
            {
                switch ($retorno){
                    case 0: $retorno = 7;
                        break;
                    case 1: $retorno = 4;
                        break;
                    case 2: $retorno = 5;
                        break;
                    case 3: $retorno = 6;
                        break;
                    default : $retorno = 8;
                        break;
                }
            }
        //    return $retorno;
        }
    }

    return($retorno);
}
?>
