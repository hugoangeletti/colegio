<!-- <body onload="nobackbutton();"> -->
<body>
    <div class="container-fluid">
    <?php
    if (logueado()) {
        require_once ('../dataAccess/funcionesConector.php');
        require_once ('../dataAccess/usuarioLogic.php');
        require_once ('../dataAccess/appLogic.php');
    ?>
        <!-- MENU 4d4d4f -->
           <?php
           //obtener las app del sistema
           $resApp = obtenerApps();
           if ($resApp){
            ?>
             <nav class="navbar navbar-default bg-info" role="navigation">
                 <div class="navbar-header">
                     <button type="button" class="navbar-toggle" data-toggle="collapse"
                             data-target=".navbar-ex1-collapse">
                       <span class="sr-only">Desplegar navegación</span>
                       <span class="icon-bar"></span>
                       <span class="icon-bar"></span>
                       <span class="icon-bar"></span>
                     </button>
                     <a class="navbar-brand" href="administracion.php"><img src="../public/images/logo-transp.png" alt="Imagen Encabezado" style="height: 30px;" align="left"></a>
                 </div>
                 <div class="collapse navbar-collapse navbar-ex1-collapse">
                 <ul class="nav navbar-nav">
                    <?php
                    foreach ($resApp['datos'] as $apps) {
                        //si el usuario tiene la aplicacion entonces cargo los items
                        if (verificarAppUsuario($_SESSION['user_id'], $apps['id'])){
                    ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <?php echo $apps['nombre']; ?>
<!--                                <span class="caret"></span>-->
                            </a>
                            <ul class="dropdown-menu">
                                <?php
                                //obtengo los roles del usuario
                                $resRoles = obtenerRolUsuario($_SESSION['user_id'], $apps['id']);
                                if ($resRoles['estado']){
                                    foreach ($resRoles['datos'] as $roles) {
                                        ?>
                                        <li><a href="<?php echo $roles['link'] ?>"><?php echo $roles['nombre']; ?> </a></li>
                                    <?php
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                        <?php
                        }
                     }
                     ?>
                 </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <b><?php echo $_SESSION['user_entidad']['nombreUsuario']; ?></b><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="logout.php" class="navbar-link">Salir</a></li>
                            </ul>
                        </li>
                    </ul>
               </div>
             </nav>                

         <?php
        } else {
            echo 'sin permiso';
        }
    } 
/*
<script type="text/javascript"> 
    $(document).ready(function () { $('.dropdown-toggle').dropdown(); }); 
</script>
 * 
 */
?>
 