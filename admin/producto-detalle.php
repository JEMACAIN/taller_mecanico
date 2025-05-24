<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once '../config/database.php';
include_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: productos.php');
    exit;
}

$id = (int)$_GET['id'];
$producto = getProducto($id);
if (!$producto) {
    header('Location: productos.php');
    exit;
}

$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
if (!$result || $result['total'] == 0) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Producto - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include_once 'includes/header.php'; ?>
        <div class="content-section">
            <div class="section-header">
                <h1>Detalle de Producto</h1>
                <a href="productos.php" class="btn btn-secondary">Volver a Productos</a>
            </div>
            <div class="product-detail-card">
                <div class="product-detail-img">
                    <img src="<?php echo '../' . $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" style="max-width: 300px; width: 100%; border-radius: 8px;">
                </div>
                <div class="product-detail-info">
                    <h2><?php echo $producto['nombre']; ?></h2>
                    <p><strong>Precio:</strong> $<?php echo formatPrice($producto['precio']); ?></p>
                    <p><strong>Stock:</strong> <?php echo $producto['stock']; ?></p>
                    <p><strong>Categoría:</strong> <?php echo $producto['categoria']; ?></p>
                    <p><strong>Marca:</strong> <?php echo $producto['marca']; ?></p>
                    <p><strong>Destacado:</strong> <?php echo $producto['destacado'] ? 'Sí' : 'No'; ?></p>
                    <p><strong>Activo:</strong> <?php echo $producto['activo'] ? 'Sí' : 'No'; ?></p>
                    <p><strong>Descripción corta:</strong> <?php echo $producto['descripcion_corta']; ?></p>
                    <p><strong>Descripción completa:</strong></p>
                    <div class="product-detail-descripcion" style="background:#f9f9f9; padding:10px; border-radius:6px; margin-bottom:10px;">
                        <?php echo nl2br($producto['descripcion']); ?>
                    </div>
                    <div class="product-detail-actions">
                        <a href="producto-editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-primary">Editar</a>
                        <a href="productos.php" class="btn btn-secondary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/sidebar.js" defer></script>
    <script src="js/admin.js" defer></script>
    <style>
    .product-detail-card {
        display: flex;
        flex-wrap: wrap;
        gap: 32px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 32px;
        margin-top: 24px;
        align-items: flex-start;
    }
    .product-detail-img {
        flex: 0 0 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-detail-info {
        flex: 1 1 300px;
    }
    .product-detail-actions {
        margin-top: 24px;
        display: flex;
        gap: 16px;
    }
    @media (max-width: 900px) {
        .product-detail-card {
            flex-direction: column;
            align-items: stretch;
        }
        .product-detail-img, .product-detail-info {
            flex: 1 1 100%;
        }
    }
    </style>
</body>
</html> 