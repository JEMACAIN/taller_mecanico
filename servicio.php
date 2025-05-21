<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: servicios.php');
    exit;
}

// Obtener el ID del servicio
$id = (int)$_GET['id'];

// Obtener información del servicio
$servicio = getServicio($id);

// Si el servicio no existe, redirigir
if (!$servicio) {
    header('Location: servicios.php');
    exit;
}

// Obtener servicios relacionados
$serviciosRelacionados = fetchAll("SELECT * FROM servicios WHERE id != ? ORDER BY RAND() LIMIT 3", [$id]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $servicio['nombre']; ?> - Taller Mecánico</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Detalle del Servicio -->
    <section class="servicio-detalle">
        <div class="container">
            <div class="servicio-header">
                <h1><?php echo $servicio['nombre']; ?></h1>
                <p class="precio-grande">Desde $<?php echo formatPrice($servicio['precio']); ?></p>
            </div>
            
            <div class="servicio-content">
                <div class="servicio-imagen">
                    <img src="<?php echo $servicio['imagen']; ?>" alt="<?php echo $servicio['nombre']; ?>">
                </div>
                <div class="servicio-info">
                    <div class="servicio-descripcion">
                        <h2>Descripción del Servicio</h2>
                        <p><?php echo $servicio['descripcion']; ?></p>
                    </div>
                    
                    <div class="servicio-detalles">
                        <div class="detalle">
                            <span class="detalle-label">Duración:</span>
                            <span class="detalle-valor"><?php echo $servicio['duracion']; ?> minutos</span>
                        </div>
                        <div class="detalle">
                            <span class="detalle-label">Precio:</span>
                            <span class="detalle-valor">$<?php echo formatPrice($servicio['precio']); ?></span>
                        </div>
                    </div>
                    
                    <div class="servicio-cta">
                        <a href="cita.php?servicio=<?php echo $servicio['id']; ?>" class="btn btn-primary">Agendar Cita</a>
                        <a href="contacto.php" class="btn btn-secondary">Más Información</a>
                    </div>
                </div>
            </div>
            
            <!-- Servicios Relacionados -->
            <?php if (!empty($serviciosRelacionados)): ?>
            <div class="servicios-relacionados">
                <h2>Servicios Relacionados</h2>
                <div class="servicios-grid">
                    <?php foreach($serviciosRelacionados as $relacionado): ?>
                    <div class="servicio-card">
                        <img src="<?php echo $relacionado['imagen']; ?>" alt="<?php echo $relacionado['nombre']; ?>">
                        <h3><?php echo $relacionado['nombre']; ?></h3>
                        <p><?php echo $relacionado['descripcion_corta']; ?></p>
                        <p class="precio">Desde $<?php echo formatPrice($relacionado['precio']); ?></p>
                        <a href="servicio.php?id=<?php echo $relacionado['id']; ?>" class="btn btn-secondary">Más Información</a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Contacto Rápido -->
    <section class="contacto-rapido">
        <div class="container">
            <div class="contacto-content">
                <h2>¿NECESITAS ESTE SERVICIO?</h2>
                <p>Llámanos ahora o agenda tu cita en línea</p>
                <div class="contacto-opciones">
                    <a href="tel:<?php echo getSiteConfig('telefono'); ?>" class="btn btn-accent">Llamar Ahora</a>
                    <a href="cita.php?servicio=<?php echo $servicio['id']; ?>" class="btn btn-secondary">Agendar Cita</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
