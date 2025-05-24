<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../img/logo.png" alt="Logo Taller Mecánico" class="sidebar-logo">
        <button id="sidebarToggle" class="sidebar-toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="icon-dashboard"></i>
                    <span>Panel de Administración</span>
                </a>
            </li>
            <li>
                <a href="servicios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'servicios.php' ? 'active' : ''; ?>">
                    <i class="icon-services"></i>
                    <span>Servicios</span>
                </a>
            </li>
            <li>
                <a href="productos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'productos.php' ? 'active' : ''; ?>">
                    <i class="icon-products"></i>
                    <span>Productos</span>
                </a>
            </li>
            <li>
                <a href="promociones.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'promociones.php' ? 'active' : ''; ?>">
                    <i class="icon-promotions"></i>
                    <span>Promociones</span>
                </a>
            </li>
            <li>
                <a href="citas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'citas.php' ? 'active' : ''; ?>">
                    <i class="icon-appointments"></i>
                    <span>Citas</span>
                </a>
            </li>
            <li>
                <a href="usuarios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>">
                    <i class="icon-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <li>
                <a href="configuracion.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'configuracion.php' ? 'active' : ''; ?>">
                    <i class="icon-settings"></i>
                    <span>Configuración</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <i class="icon-logout"></i>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</aside>
