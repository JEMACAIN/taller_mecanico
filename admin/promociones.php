<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files with error checking
$config_file = '../config/database.php';
$functions_file = '../includes/functions.php';

if (!file_exists($config_file)) {
    die("Error: Database configuration file not found");
}

if (!file_exists($functions_file)) {
    die("Error: Functions file not found");
}

include_once $config_file;
include_once $functions_file;

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

// Try to establish database connection with error handling
try {
    $conn = getConnection();
    if (!$conn) {
        throw new Exception("Failed to establish database connection");
    }
    
    // Obtener todas las promociones
    $promociones = fetchAll("SELECT * FROM promociones ORDER BY fecha_inicio DESC");
    
    if ($promociones === false) {
        throw new Exception("Error fetching promotions from database");
    }
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}

include_once '../config/database.php';
include_once '../includes/functions.php';
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
    <title>Promociones - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Contenedor del contenido -->
        <div class="content-section">
            <div class="section-header">
                <a href="promocion-nueva.php" class="btn btn-primary">Nueva Promoción</a>
            </div>

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?php echo $_SESSION['mensaje_tipo']; ?>">
                    <?php 
                    echo $_SESSION['mensaje'];
                    unset($_SESSION['mensaje']);
                    unset($_SESSION['mensaje_tipo']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descuento</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($promociones)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay promociones registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($promociones as $promo): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($promo['titulo']); ?></td>
                                    <td><?php echo htmlspecialchars($promo['descuento']); ?>%</td>
                                    <td><?php echo date('d/m/Y', strtotime($promo['fecha_inicio'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($promo['fecha_fin'])); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $promo['activo'] ? 'success' : 'danger'; ?>">
                                            <?php echo $promo['activo'] ? 'Activa' : 'Inactiva'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="promocion-editar.php?id=<?php echo $promo['id']; ?>" class="btn-icon" title="Editar">⭕️
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="promocion-eliminar.php?id=<?php echo $promo['id']; ?>" class="btn-icon delete-btn" title="Eliminar">❌
                                                <i class="fas fa-trash"></i>
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