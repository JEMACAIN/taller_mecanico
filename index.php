<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';

// Obtener servicios destacados
$servicios = getServicios(true);
$productos = getProductos(true);
$promociones = getPromociones();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taller Mecánico - Especialistas en Cambio de Aceite y Llantas</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Banner Principal -->
    <section class="banner">
        <div class="banner-content">
            <h1>EXPERTOS EN CAMBIO DE ACEITE Y LLANTAS</h1>
            <p>Servicio rápido y profesional para tu vehículo</p>
            <a href="#servicios" class="btn btn-primary">Ver Servicios</a>
        </div>
    </section>
    
    <!-- Servicios Destacados -->
    <section id="servicios" class="servicios">
        <div class="container">
            <h2 class="section-title">NUESTROS SERVICIOS</h2>
            <div class="servicios-grid">
                <?php foreach($servicios as $servicio): ?>
                <div class="servicio-card">
                    <img src="<?php echo $servicio['imagen']; ?>" alt="<?php echo $servicio['nombre']; ?>">
                    <h3><?php echo $servicio['nombre']; ?></h3>
                    <p><?php echo $servicio['descripcion_corta']; ?></p>
                    <p class="precio">Desde $<?php echo $servicio['precio']; ?></p>
                    <a href="servicio.php?id=<?php echo $servicio['id']; ?>" class="btn btn-secondary">Más Información</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Promociones -->
    <section class="promociones">
        <div class="container">
            <h2 class="section-title">PROMOCIONES ESPECIALES</h2>
            <div class="promociones-slider" id="promocionesSlider">
                <?php foreach($promociones as $promo): ?>
                <div class="promo-slide">
                    <div class="promo-content">
                        <h3><?php echo $promo['titulo']; ?></h3>
                        <p><?php echo $promo['descripcion']; ?></p>
                        <p class="descuento"><?php echo $promo['descuento']; ?>% DESCUENTO</p>
                        <a href="contacto.php" class="btn btn-accent">¡Aprovecha Ya!</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="slider-controls">
                <button id="prevPromo" class="slider-btn">&lt;</button>
                <button id="nextPromo" class="slider-btn">&gt;</button>
            </div>
        </div>
    </section>
    
    <!-- Productos Destacados -->
    <section class="productos">
        <div class="container">
            <h2 class="section-title">LLANTAS DE CALIDAD</h2>
            <div class="productos-grid">
                <?php foreach($productos as $producto): ?>
                <div class="producto-card">
                    <div class="producto-img">
                        <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
                    </div>
                    <div class="producto-info">
                        <h3><?php echo $producto['nombre']; ?></h3>
                        <p><?php echo $producto['descripcion_corta']; ?></p>
                        <p class="precio">$<?php echo $producto['precio']; ?></p>
                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-secondary">Ver Detalles</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="ver-mas">
                <a href="productos.php" class="btn btn-primary">Ver Todos los Productos</a>
            </div>
        </div>
    </section>
    
    <!-- Por qué Elegirnos -->
    <section class="por-que-elegirnos">
        <div class="container">
            <h2 class="section-title">¿POR QUÉ ELEGIRNOS?</h2>
            <div class="razones-grid">
                <div class="razon">
                    <div class="icono">
                        <img src="img/icono-calidad.png" alt="Calidad">
                    </div>
                    <h3>Calidad Garantizada</h3>
                    <p>Utilizamos productos de las mejores marcas para asegurar el óptimo funcionamiento de tu vehículo.</p>
                </div>
                <div class="razon">
                    <div class="icono">
                        <img src="img/icono-tiempo.png" alt="Tiempo">
                    </div>
                    <h3>Servicio Rápido</h3>
                    <p>Valoramos tu tiempo. Nuestros servicios están diseñados para ser eficientes sin sacrificar la calidad.</p>
                </div>
                <div class="razon">
                    <div class="icono">
                        <img src="img/icono-precio.png" alt="Precio">
                    </div>
                    <h3>Precios Competitivos</h3>
                    <p>Ofrecemos los mejores precios del mercado con promociones exclusivas para nuestros clientes.</p>
                </div>
                <div class="razon">
                    <div class="icono">
                        <img src="img/icono-experiencia.png" alt="Experiencia">
                    </div>
                    <h3>Experiencia</h3>
                    <p>Contamos con técnicos certificados con años de experiencia en el mantenimiento automotriz.</p>
                </div>
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
                    <a href="tel:+123456789" class="btn btn-accent">Llamar Ahora</a>
                    <a href="cita.php" class="btn btn-secondary">Agendar Cita</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
