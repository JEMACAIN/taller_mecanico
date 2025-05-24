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
    
    // Verificar si el servicio existe
    $servicio = getServicio($id);
    
    if ($servicio) {
        // Eliminar servicio
        $eliminado = delete('servicios', 'id = ?', [$id]);
        
        if ($eliminado) {
            $mensaje = "Servicio eliminado correctamente.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al eliminar el servicio.";
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "El servicio no existe.";
        $tipo_mensaje = "danger";
    }
}

// Obtener todos los servicios
$servicios = fetchAll("SELECT * FROM servicios ORDER BY orden ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .table-img {
            max-height: 50px;
            width: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Gestión de Servicios -->
        <div class="content-section">
            <div class="section-header">
                <a href="servicio-nuevo.php" class="btn btn-primary">Nuevo Servicio</a>
            </div>
            
            <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Duración</th>
                            <th>Destacado</th>
                            <th>Orden</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($servicios)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay servicios registrados</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td><?php echo $servicio['id']; ?></td>
                                <td>
                                    <img src="<?php echo '../' . $servicio['imagen']; ?>" alt="<?php echo $servicio['nombre']; ?>" class="table-img">
                                </td>
                                <td><?php echo $servicio['nombre']; ?></td>
                                <td>$<?php echo formatPrice($servicio['precio']); ?></td>
                                <td><?php echo $servicio['duracion']; ?> min</td>
                                <td>
                                    <span class="badge badge-<?php echo $servicio['destacado'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $servicio['destacado'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </td>
                                <td><?php echo $servicio['orden']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $servicio['activo'] ? 'success' : 'danger'; ?>">
                                        <?php echo $servicio['activo'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="servicio-editar.php?id=<?php echo $servicio['id']; ?>" class="btn-icon" title="Editar">⭕️
                                            <i class="icon-edit"></i>
                                        </a>
                                        <a href="servicios.php?eliminar=<?php echo $servicio['id']; ?>" class="btn-icon delete-btn" title="Eliminar">❌
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
