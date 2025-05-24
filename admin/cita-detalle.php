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

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: citas.php');
    exit;
}

$id = (int)$_GET['id'];

// Obtener información de la cita
$cita = fetchOne("SELECT c.*, s.nombre as servicio FROM citas c 
                LEFT JOIN servicios s ON c.servicio_id = s.id 
                WHERE c.id = ?", [$id]);

// Si la cita no existe, redirigir
if (!$cita) {
    header('Location: citas.php');
    exit;
}

// Función para obtener clase de estado
function getStatusClass($estado) {
    switch ($estado) {
        case 'pendiente':
            return 'warning';
        case 'confirmada':
            return 'info';
        case 'completada':
            return 'success';
        case 'cancelada':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Verificar si existen usuarios
include_once 'includes/verifica_usuario.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Cita - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Detalle de Cita -->
        <div class="content-section">
            <div class="section-header">
                <h1>Detalle de Cita #<?php echo $cita['id']; ?></h1>
                <a href="citas.php" class="btn btn-secondary">Volver a Citas</a>
            </div>
            
            <div class="detail-container">
                <div class="detail-header">
                    <div class="detail-status">
                        <span class="badge badge-<?php echo getStatusClass($cita['estado']); ?>">
                            <?php echo $cita['estado']; ?>
                        </span>
                    </div>
                    <div class="detail-actions">
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle">
                                Cambiar Estado
                            </button>
                            <div class="dropdown-menu">
                                <a href="citas.php?id=<?php echo $cita['id']; ?>&estado=pendiente">Pendiente</a>
                                <a href="citas.php?id=<?php echo $cita['id']; ?>&estado=confirmada">Confirmada</a>
                                <a href="citas.php?id=<?php echo $cita['id']; ?>&estado=completada">Completada</a>
                                <a href="citas.php?id=<?php echo $cita['id']; ?>&estado=cancelada">Cancelada</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="detail-content">
                    <div class="detail-section">
                        <h2>Información del Cliente</h2>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Nombre:</span>
                                <span class="detail-value"><?php echo $cita['nombre_cliente']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">
                                    <a href="mailto:<?php echo $cita['email_cliente']; ?>"><?php echo $cita['email_cliente']; ?></a>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Teléfono:</span>
                                <span class="detail-value">
                                    <a href="tel:<?php echo $cita['telefono_cliente']; ?>"><?php echo $cita['telefono_cliente']; ?></a>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h2>Información de la Cita</h2>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Servicio:</span>
                                <span class="detail-value"><?php echo $cita['servicio']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Fecha:</span>
                                <span class="detail-value"><?php echo date('d/m/Y', strtotime($cita['fecha'])); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Hora:</span>
                                <span class="detail-value"><?php echo $cita['hora']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Fecha de Creación:</span>
                                <span class="detail-value"><?php echo date('d/m/Y H:i', strtotime($cita['fecha_creacion'])); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h2>Información del Vehículo</h2>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Marca:</span>
                                <span class="detail-value"><?php echo $cita['vehiculo_marca']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Modelo:</span>
                                <span class="detail-value"><?php echo $cita['vehiculo_modelo']; ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Año:</span>
                                <span class="detail-value"><?php echo $cita['vehiculo_anio']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($cita['comentarios'])): ?>
                    <div class="detail-section">
                        <h2>Comentarios</h2>
                        <div class="detail-text">
                            <?php echo nl2br($cita['comentarios']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js" defer></script>
</body>
</html>
