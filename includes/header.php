<header class="header">
    <div class="container">
        <div class="header-top">
            <div class="contact-info">
                <span><i class="icon-phone"></i> <?php echo getSiteConfig('telefono'); ?></span>
                <span><i class="icon-email"></i> <?php echo getSiteConfig('email'); ?></span>
                <span><i class="icon-location"></i> <?php echo getSiteConfig('direccion'); ?></span>
            </div>
            <div class="social-links">
                <?php if(getSiteConfig('facebook')): ?>
                <a href="<?php echo getSiteConfig('facebook'); ?>" target="_blank"><i class="icon-facebook"></i></a>
                <?php endif; ?>
                
                <?php if(getSiteConfig('instagram')): ?>
                <a href="<?php echo getSiteConfig('instagram'); ?>" target="_blank"><i class="icon-instagram"></i></a>
                <?php endif; ?>
                
                <?php if(getSiteConfig('twitter')): ?>
                <a href="<?php echo getSiteConfig('twitter'); ?>" target="_blank"><i class="icon-twitter"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="header-main">
            <div class="logo">
                <a href="index.php">
                    <img src="<?php echo getSiteConfig('logo'); ?>" alt="<?php echo getSiteConfig('nombre_sitio'); ?>">
                </a>
            </div>
            <nav class="main-nav">
                <button class="menu-toggle" id="menuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="servicios.php">Servicios</a></li>
                    <li><a href="productos.php">Llantas</a></li>
                    <li><a href="promociones.php">Promociones</a></li>
                    <li><a href="nosotros.php">Nosotros</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </nav>
            <div class="cta-button">
                <a href="cita.php" class="btn btn-primary">Agendar Cita</a>
            </div>
        </div>
    </div>
</header>
