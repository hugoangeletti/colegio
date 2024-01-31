<?php
    require_once '../dataAccess/conection.php';
    conectar();
    require_once '../dataAccess/ordenDiaLogic.php';
    require_once '../dataAccess/funciones.php';
    require_once '../dataAccess/mesaEntradaLogic.php';

    if(isset($_GET['iOrden']))
    {
        if(isset($_GET['st']))
        {
            $consultaInfoOrden = obtenerOrdenPorId($_GET['iOrden']);
            $infoOrden = $consultaInfoOrden -> fetch_assoc();
            $movimientosOrdenDia = obtenerMovimientosPorIdOrdenDiaPorPlanilla($_GET['iOrden'], $_GET['st']);
            
            switch ($_GET['st'])
            {
                case 1:
                    $titulo = "Asuntos Internos";
                    break;
                case 2:
                    $titulo = "Notas Recibidas";
                    break;
                case 3:
                    $titulo = "Archivados";
                    break;
            }
        }
    }
?>
<div id="titulo">
    <h3>Orden del Día</h3>
    <h4>Planilla <?php echo $titulo ?></h4>
    <?php
        if(isset($infoOrden) && (!is_null($infoOrden)))
        {
    ?>
    <h4>Nº de Orden: <?php echo $infoOrden['Numero'] ?></h4>
    <?php
        }
    ?>
</div>
<script type="text/javascript">
    $(function(){
        $(".volverOrdenDetalle").click(function(){
           $("#page-wrap").load("ordenDiaDetalle.php?iOrden=<?php echo $_GET['iOrden'] ?>"); 
        });
        $(".editar").click(function(){
            var href = $(this).attr("id");
            $("#page-wrap").load(href); 
        });
    });
</script>
<?php
if(!$movimientosOrdenDia)
{
    ?>
<br>
<span class="mensajeERROR">Hubo un problema en el sistema. Reportarlo.</span>
<br>
    <?php
}
else
{
    if($movimientosOrdenDia -> num_rows == 0)
    {
        ?>
<br>
<p class="mensajeWARNING">No se encontraron movimientos para dicha planilla.</p>
<br>
        <?php
    }
    else
    {
        ?>
    <table class='tablaTabs'>
        <tr>
            <td><h4>Planilla</h4></td>
            <td><h4>Nº de Trámite</h4></td>
            <td><h4>Fecha de Trámite</h4></td>
            <td><h4>Tipo de Trámite</h4></td>
            <td><h4>Colegiado/Remitente</h4></td>
            <td><h4>Tema/Observaciones</h4></td>
            <td><h4>Editar</h4></td>
        </tr>
        <?php
            while($mod = $movimientosOrdenDia -> fetch_assoc())
            {
        ?>
        <tr>
            <td>
                <?php echo $mod['TipoPlanilla']; ?>
            </td>
            <td>
                <?php echo $mod['IdMesaEntrada'] ?>
            </td>
            <td>
                <?php echo invertirFecha($mod['FechaIngreso']) ?>
            </td>
            <td>
                <?php echo utf8_encode($mod['NombreMovimiento']); ?>
            </td>
            <td>
                <?php if(!is_null($mod['Matricula'])){echo utf8_encode($mod['Apellido'])." ".utf8_encode($mod['Nombres']);}else{echo utf8_encode($mod['NombreRemitente']);} ?>
            </td>
            <td>
                <?php if(!is_null($mod['Tema'])){echo utf8_encode($mod['Tema']);}else{if(!is_null($mod['DetalleCompleto'])){echo utf8_encode($mod['DetalleCompleto']);}}?>
            </td>
            <?php
                switch ($mod['IdTipoMesaEntrada'])
                {
                    case 1: $href = "mesaEntradaFormMovimiento.php";
                        break;
                    case 2: $href = "#";
                        break;
                    case 3: $href = "mesaEntradaFormNota.php";
                        break;
                    case 4: $href = "#";
                        break;
                    case 5: $href = "#";
                        break;
                }
            ?>
            <td><a class="editar" id="<?php if(isset($href)){echo $href;} ?>?action=M&iEvento=<?php echo $mod['IdMesaEntrada'] ?>&orden=<?php echo $_GET['iOrden'] ?>&st=<?php echo $_GET['st'] ?>">Editar</a></td>
        </tr>
        <?php
            }
        ?>
    </table>

        <?php
    }
}
?>
<br /><br />
<input type="button" value="Volver" class="volverOrdenDetalle"/>
