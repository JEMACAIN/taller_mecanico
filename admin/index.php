<?php
// Iniciar sesión
session_start();
try {
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

// Verificar si existen usuarios
include_once 'includes/verifica_usuario.php';

// Obtener estadísticas
$totalServicios = fetchOne("SELECT COUNT(*) as total FROM servicios")['total'];
$totalProductos = fetchOne("SELECT COUNT(*) as total FROM productos")['total'];
$totalPromociones = fetchOne("SELECT COUNT(*) as total FROM promociones")['total'];
$totalCitas = fetchOne("SELECT COUNT(*) as total FROM citas")['total'];

// Obtener últimas citas
$ultimasCitas = fetchAll("SELECT c.*, s.nombre as servicio FROM citas c LEFT JOIN servicios s ON c.servicio_id = s.id ORDER BY c.fecha DESC LIMIT 5");

// Obtener últimos productos
$ultimosProductos = fetchAll("SELECT * FROM productos ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Taller Mecánico</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
    .stat-link {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: box-shadow 0.2s, transform 0.2s;
        border-radius: 12px;
    }
    .stat-link:focus, .stat-link:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        background: #f5f7fa;
        transform: translateY(-2px) scale(1.03);
    }
    .stat-card {
        cursor: pointer;
    }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <a href="servicios.php" class="stat-link">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="icon-services"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Servicios</h3>
                        <p><?php echo $totalServicios; ?></p>
                    </div>
                </div>
            </a>
            <a href="productos.php" class="stat-link">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="icon-products"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Productos</h3>
                        <p><?php echo $totalProductos; ?></p>
                    </div>
                </div>
            </a>
            <a href="promociones.php" class="stat-link">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="icon-promotions"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Promociones</h3>
                        <p><?php echo $totalPromociones; ?></p>
                    </div>
                </div>
            </a>
            <a href="citas.php" class="stat-link">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="icon-appointments"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Citas</h3>
                        <p><?php echo $totalCitas; ?></p>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Últimas citas -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Últimas Citas</h2>
                <a href="citas.php" class="btn btn-sm">Ver Todas</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ultimasCitas)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay citas registradas</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($ultimasCitas as $cita): ?>
                            <tr>
                                <td><?php echo $cita['nombre_cliente']; ?></td>
                                <td><?php echo $cita['servicio']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($cita['fecha'])); ?></td>
                                <td><?php echo $cita['hora']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo getStatusClass($cita['estado']); ?>">
                                        <?php echo $cita['estado']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="cita-detalle.php?id=<?php echo $cita['id']; ?>" class="btn-icon" title="Ver detalle">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <a href="cita-editar.php?id=<?php echo $cita['id']; ?>" class="btn-icon" title="Editar">
                                            <i class="icon-edit"></i>
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
        
        <!-- Últimos productos -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Últimos Productos</h2>
                <a href="productos.php" class="btn btn-sm">Ver Todos</a>
            </div>
            <div class="products-grid">
                <?php if (empty($ultimosProductos)): ?>
                <p class="text-center">No hay productos registrados</p>
                <?php else: ?>
                    <?php foreach ($ultimosProductos as $producto): ?>
                    <div class="product-card">
                        <div class="product-img">
                            <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
                        </div>
                        <div class="product-info">
                            <h3><?php echo $producto['nombre']; ?></h3>
                            <p class="price">$<?php echo formatPrice($producto['precio']); ?></p>
                            <div class="product-actions">
                                <a href="producto-editar.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm">Editar</a>
                                <a href="producto-detalle.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-outline">Ver</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js" defer></script>
    <script src="js/admin.js" defer></script>
</body>
</html>
<?php
} catch (Throwable $e) {
    header('Location: ../config/instrucciones_db.php');
    exit();
}
?>
