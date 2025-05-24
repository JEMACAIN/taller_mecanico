<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="<?php echo getSiteConfig('logo'); ?>" alt="<?php echo getSiteConfig('nombre_sitio'); ?>">
                <p><?php echo getSiteConfig('descripcion_corta'); ?></p>
            </div>
            <div class="footer-links">
                <h3>Enlaces Rápidos</h3>
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="servicios.php">Servicios</a></li>
                    <li><a href="productos.php?categoria=Llantas">Llantas</a></li>
                    <li><a href="promociones.php">Promociones</a></li>
                    <li><a href="nosotros.php">Nosotros</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-services">
                <h3>Nuestros Servicios</h3>
                <ul>
                    <li><a href="servicio.php?id=1">Cambio de Aceite</a></li>
                    <li><a href="productos.php?categoria=Llantas">Venta de Llantas</a></li>
                    <li><a href="servicio.php?id=4">Alineación y Balanceo</a></li>
                    <li><a href="servicio.php?id=5">Frenos</a></li>
                    <li><a href="servicio.php?id=3">Venta e Instalación de Llantas</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h3>Contáctanos</h3>
                <p><i class="icon-location"></i> <?php echo getSiteConfig('direccion'); ?></p>
                <p><i class="icon-phone"></i> <?php echo getSiteConfig('telefono'); ?></p>
                <p><i class="icon-email"></i> <?php echo getSiteConfig('email'); ?></p>
                <div class="footer-social">
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
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo getSiteConfig('nombre_sitio'); ?>. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>
