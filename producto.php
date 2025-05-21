<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: productos.php');
    exit;
}

// Obtener el ID del producto
$id = (int)$_GET['id'];

// Obtener información del producto
$producto = getProducto($id);

// Si el producto no existe, redirigir
if (!$producto) {
    header('Location: productos.php');
    exit;
}

// Obtener productos relacionados (misma categoría)
$productosRelacionados = fetchAll("SELECT * FROM productos WHERE id != ? AND categoria = ? AND activo = 1 ORDER BY RAND() LIMIT 3", [$id, $producto['categoria']]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $producto['nombre']; ?> - Taller Mecánico</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Detalle del Producto -->
    <section class="producto-detalle">
        <div class="container">
            <div class="producto-header">
                <h1><?php echo $producto['nombre']; ?></h1>
                <p class="categoria"><?php echo $producto['categoria']; ?> | <?php echo $producto['marca']; ?></p>
            </div>
            
            <div class="producto-content">
                <div class="producto-imagen">
                    <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
                </div>
                <div class="producto-info">
                    <div class="producto-precio">
                        <span class="precio-label">Precio:</span>
                        <span class="precio-valor">$<?php echo formatPrice($producto['precio']); ?></span>
                    </div>
                    
                    <div class="producto-disponibilidad">
                        <span class="disponibilidad-label">Disponibilidad:</span>
                        <span class="disponibilidad-valor <?php echo $producto['stock'] > 0 ? 'en-stock' : 'agotado'; ?>">
                            <?php echo $producto['stock'] > 0 ? 'En Stock' : 'Agotado'; ?>
                        </span>
                    </div>
                    
                    <div class="producto-descripcion">
                        <h2>Descripción</h2>
                        <p><?php echo $producto['descripcion']; ?></p>
                    </div>
                    
                    <div class="producto-cta">
                        <a href="contacto.php?producto=<?php echo $producto['id']; ?>" class="btn btn-primary">Consultar Disponibilidad</a>
                        <a href="cita.php" class="btn btn-secondary">Agendar Instalación</a>
                    </div>
                </div>
            </div>
            
            <!-- Productos Relacionados -->
            <?php if (!empty($productosRelacionados)): ?>
            <div class="productos-relacionados">
                <h2>Productos Relacionados</h2>
                <div class="productos-grid">
                    <?php foreach($productosRelacionados as $relacionado): ?>
                    <div class="producto-card">
                        <div class="producto-img">
                            <img src="<?php echo $relacionado['imagen']; ?>" alt="<?php echo $relacionado['nombre']; ?>">
                        </div>
                        <div class="producto-info">
                            <h3><?php echo $relacionado['nombre']; ?></h3>
                            <p><?php echo $relacionado['descripcion_corta']; ?></p>
                            <p class="precio">$<?php echo formatPrice($relacionado['precio']); ?></p>
                            <a href="producto.php?id=<?php echo $relacionado['id']; ?>" class="btn btn-secondary">Ver Detalles</a>
                        </div>
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
                <h2>¿INTERESADO EN ESTE PRODUCTO?</h2>
                <p>Llámanos ahora o visita nuestro taller</p>
                <div class="contacto-opciones">
                    <a href="tel:<?php echo getSiteConfig('telefono'); ?>" class="btn btn-accent">Llamar Ahora</a>
                    <a href="contacto.php?producto=<?php echo $producto['id']; ?>" class="btn btn-secondary">Contactar</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
