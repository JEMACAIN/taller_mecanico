<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Incluir archivos necesarios
include_once '../config/database.php';
include_once '../includes/functions.php';

// Verificar si el usuario es administrador
if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
if (!$result || $result['total'] == 0) {
    header('Location: ../index.php');
    exit;
}

// Procesar eliminación si se solicita
if (isset($_GET['eliminar']) && !empty($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    
    // Verificar si el producto existe
    $producto = getProducto($id);
    
    if ($producto) {
        // Eliminar producto
        $eliminado = delete('productos', 'id = ?', [$id]);
        
        if ($eliminado) {
            $mensaje = "Producto eliminado correctamente.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al eliminar el producto.";
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "El producto no existe.";
        $tipo_mensaje = "danger";
    }
}

// Obtener categorías para filtrar
$categorias = fetchAll("SELECT DISTINCT categoria FROM productos ORDER BY categoria ASC");

// Filtrar por categoría si se proporciona
$categoria = isset($_GET['categoria']) ? sanitizeInput($_GET['categoria']) : '';

// Obtener productos
if (!empty($categoria)) {
    $productos = fetchAll("SELECT * FROM productos WHERE categoria = ? ORDER BY nombre ASC", [$categoria]);
} else {
    $productos = fetchAll("SELECT * FROM productos ORDER BY nombre ASC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
    <style>
    .table-img {
        max-height: 50px;
        width: auto;
        display: block;
        margin: 0 auto;
    }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Gestión de Productos -->
        <div class="content-section">
            <div class="section-header">
                <a href="producto-nuevo.php" class="btn btn-primary">Nuevo Producto</a>
            </div>
            
            <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
            <?php endif; ?>
            
            <!-- Filtros -->
            <div class="filters">
                <div class="filter-group">
                    <label for="categoria">Filtrar por Categoría:</label>
                    <select id="categoria" onchange="window.location.href='productos.php?categoria='+this.value">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat['categoria']; ?>" <?php echo $categoria === $cat['categoria'] ? 'selected' : ''; ?>>
                            <?php echo $cat['categoria']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Marca</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Destacado</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="10" class="text-center">No hay productos registrados</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo $producto['id']; ?></td>
                                <td>
                                    <img src="<?php echo '../' . $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" class="table-img">
                                </td>
                                <td><?php echo $producto['nombre']; ?></td>
                                <td><?php echo $producto['categoria']; ?></td>
                                <td><?php echo $producto['marca']; ?></td>
                                <td>$<?php echo formatPrice($producto['precio']); ?></td>
                                <td><?php echo $producto['stock']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $producto['destacado'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $producto['destacado'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $producto['activo'] ? 'success' : 'danger'; ?>">
                                        <?php echo $producto['activo'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="producto-editar.php?id=<?php echo $producto['id']; ?>" class="btn-icon" title="Editar">⭕️
                                            <i class="icon-edit"></i>
                                        </a>
                                        <a href="productos.php?eliminar=<?php echo $producto['id']; ?>" class="btn-icon delete-btn" title="Eliminar">❌ 
                                            <i class="icon-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js" defer></script>
</body>
</html>
