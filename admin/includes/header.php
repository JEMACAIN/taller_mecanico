<header class="admin-header">
    <div class="header-left">
        <h2 class="page-title">
            <?php
            $page = basename($_SERVER['PHP_SELF'], '.php');
            switch ($page) {
                case 'index':
                    echo 'Dashboard';
                    break;
                case 'servicios':
                    echo 'Gestión de Servicios';
                    break;
                case 'productos':
                    echo 'Gestión de Productos';
                    break;
                case 'promociones':
                    echo 'Gestión de Promociones';
                    break;
                case 'citas':
                    echo 'Gestión de Citas';
                    break;
                case 'usuarios':
                    echo 'Gestión de Usuarios';
                    break;
                case 'configuracion':
                    echo 'Configuración del Sitio';
                    break;
                default:
                    echo 'Panel de Administración';
            }
            ?>
        </h2>
    </div>
    <div class="header-right">
        <div class="user-dropdown">
            <button class="dropdown-toggle">
                <div class="user-avatar">
                    <img src="../img/avatar.png" alt="Avatar">
                </div>
                <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                <i class="icon-chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <a href="perfil.php">
                    <i class="icon-user"></i>
                    <span>Mi Perfil</span>
                </a>
                <a href="configuracion.php">
                    <i class="icon-settings"></i>
                    <span>Configuración</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php">
                    <i class="icon-logout"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </div>
</header>
