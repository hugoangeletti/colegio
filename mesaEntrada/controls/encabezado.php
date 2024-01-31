<div id="login" style="font-size: 12px; background-color: #4d4d4f; padding-right: 5px" align="right">
    <?php
        if (isset($_SESSION['user'])){ ?>
     <div style="color: white; text-transform:uppercase"><?php echo $_SESSION['user'];?>
            <a href="logout.php" style="color: white; text-transform: capitalize">Logout</a></div>
    <?php
        }else{ ?>
            <a href="login.php" style="color: white">Login</a>
    <?php
        } ?>
</div>
<div id="Encabezado" style="height:90px;width:800px;">
    <table width="100%">
        <tr>
            <td><img src="../images/logosh.gif" width="70" height="70" longdesc="Colegio de Medicos - Distrito I" /></td>
            <td>
                <h3> Colegio de M&eacute;dicos <br /></h3>
                <h4>Provincia  de Buenos Aires <br />
                    Distrito I </h4>
            </td>
	</tr>
    </table>
</div>
<div>
    <?php
    include '../html/menu.html';
    ?>
</div>