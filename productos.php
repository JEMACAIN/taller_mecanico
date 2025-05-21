<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';

// Obtener categorías de productos
$categorias = fetchAll("SELECT DISTINCT categoria FROM productos WHERE activo = 1 ORDER BY categoria ASC");

// Filtrar por categoría si se proporciona
$categoria = isset($_GET['categoria']) ? sanitizeInput($_GET['categoria']) : '';

// Obtener productos
if (!empty($categoria)) {
    $productos = fetchAll("SELECT * FROM productos WHERE activo = 1 AND categoria = ? ORDER BY nombre ASC", [$categoria]);
} else {
    $productos = getProductos();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros Productos - Taller Mecánico</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Banner de Productos -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>NUESTROS PRODUCTOS</h1>
            <p>Llantas y aceites de las mejores marcas para tu vehículo</p>
        </div>
    </section>
    
    <!-- Filtros de Productos -->
    <section class="filtros-productos">
        <div class="container">
            <div class="filtros-container">
                <h2>Filtrar por Categoría</h2>
                <div class="filtros-opciones">
                    <a href="productos.php" class="filtro-btn <?php echo empty($categoria) ? 'active' : ''; ?>">Todos</a>
                    <?php foreach($categorias as $cat): ?>
                    <a href="productos.php?categoria=<?php echo urlencode($cat['categoria']); ?>" class="filtro-btn <?php echo $categoria === $cat['categoria'] ? 'active' : ''; ?>">
                        <?php echo $cat['categoria']; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Lista de Productos -->
    <section class="productos-lista">
        <div class="container">
            <?php if (empty($productos)): ?>
            <div class="no-productos">
                <p>No se encontraron productos en esta categoría.</p>
            </div>
            <?php else: ?>
            <div class="productos-grid">
                <?php foreach($productos as $producto): ?>
                <div class="producto-card">
                    <div class="producto-img">
                        <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
                    </div>
                    <div class="producto-info">
                        <h3><?php echo $producto['nombre']; ?></h3>
                        <p><?php echo $producto['descripcion_corta']; ?></p>
                        <p class="precio">$<?php echo formatPrice($producto['precio']); ?></p>
                        <a href="producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-secondary">Ver Detalles</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Contacto Rápido -->
    <section class="contacto-rapido">
        <div class="container">
            <div class="contacto-content">
                <h2>¿BUSCAS UN PRODUCTO ESPECÍFICO?</h2>
                <p>Llámanos ahora o visita nuestro taller</p>
                <div class="contacto-opciones">
                    <a href="tel:<?php echo getSiteConfig('telefono'); ?>" class="btn btn-accent">Llamar Ahora</a>
                    <a href="contacto.php" class="btn btn-secondary">Contactar</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
