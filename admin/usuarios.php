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
    
    // No permitir eliminar al usuario actual
    if ($id == $_SESSION['user_id']) {
        $mensaje = "No puede eliminar su propio usuario.";
        $tipo_mensaje = "danger";
    } else {
        // Verificar si el usuario existe
        $usuario = fetchOne("SELECT * FROM usuarios WHERE id = ?", [$id]);
        
        if ($usuario) {
            // Eliminar usuario
            $eliminado = delete('usuarios', 'id = ?', [$id]);
            
            if ($eliminado) {
                $mensaje = "Usuario eliminado correctamente.";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al eliminar el usuario.";
                $tipo_mensaje = "danger";
            }
        } else {
            $mensaje = "El usuario no existe.";
            $tipo_mensaje = "danger";
        }
    }
}

// Obtener todos los usuarios
$usuarios = fetchAll("SELECT * FROM usuarios ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Gestión de Usuarios -->
        <div class="content-section">
            <div class="section-header">
                <a href="usuario-nuevo.php" class="btn btn-primary">Nuevo Usuario</a>
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
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Fecha de Creación</th>
                            <th>Último Acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay usuarios registrados</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo $usuario['nombre']; ?></td>
                                <td><?php echo $usuario['username']; ?></td>
                                <td><?php echo $usuario['email']; ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $usuario['rol'] == 'admin' ? 'primary' : 'secondary'; ?>">
                                        <?php echo $usuario['rol']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?></td>
                                <td><?php echo $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'Nunca'; ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="usuario-editar.php?id=<?php echo $usuario['id']; ?>" class="btn-icon" title="Editar">⭕️
                                            <i class="icon-edit"></i>
                                        </a>
                                        <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                        <a href="usuarios.php?eliminar=<?php echo $usuario['id']; ?>" class="btn-icon delete-btn" title="Eliminar">
                                            <i class="icon-trash"></i>
                                        </a>
                                        <?php endif; ?>
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
