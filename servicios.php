<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';

// Obtener todos los servicios
$servicios = getServicios();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros Servicios - Taller Mecánico</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Banner de Servicios -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>NUESTROS SERVICIOS</h1>
            <p>Soluciones profesionales para el mantenimiento de tu vehículo</p>
        </div>
    </section>
    
    <!-- Lista de Servicios -->
    <section class="servicios-lista">
        <div class="container">
            <div class="servicios-grid">
                <?php foreach($servicios as $servicio): ?>
                <div class="servicio-card">
                    <img src="<?php echo $servicio['imagen']; ?>" alt="<?php echo $servicio['nombre']; ?>">
                    <h3><?php echo $servicio['nombre']; ?></h3>
                    <p><?php echo $servicio['descripcion_corta']; ?></p>
                    <p class="precio">Desde $<?php echo formatPrice($servicio['precio']); ?></p>
                    <a href="servicio.php?id=<?php echo $servicio['id']; ?>" class="btn btn-secondary">Más Información</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Contacto Rápido -->
    <section class="contacto-rapido">
        <div class="container">
            <div class="contacto-content">
                <h2>¿NECESITAS UN SERVICIO URGENTE?</h2>
                <p>Llámanos ahora o agenda tu cita en línea</p>
                <div class="contacto-opciones">
                    <a href="tel:<?php echo getSiteConfig('telefono'); ?>" class="btn btn-accent">Llamar Ahora</a>
                    <a href="cita.php" class="btn btn-secondary">Agendar Cita</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
