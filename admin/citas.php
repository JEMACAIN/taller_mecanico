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

//verifica si existe usuario crado en el sistema
include_once 'includes/verifica_usuario.php';

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

// Procesar cambio de estado si se solicita
if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id = (int)$_GET['id'];
    $estado = sanitizeInput($_GET['estado']);
    
    // Verificar si el estado es válido
    $estados_validos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
    
    if (in_array($estado, $estados_validos)) {
        // Actualizar estado
        $actualizado = update('citas', ['estado' => $estado], 'id = ?', [$id]);
        
        if ($actualizado) {
            $mensaje = "Estado de la cita actualizado correctamente.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al actualizar el estado de la cita.";
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "Estado no válido.";
        $tipo_mensaje = "danger";
    }
}

// Filtrar por estado si se proporciona
$estado_filtro = isset($_GET['estado_filtro']) ? sanitizeInput($_GET['estado_filtro']) : '';

// Filtrar por fecha si se proporciona
$fecha_filtro = isset($_GET['fecha_filtro']) ? sanitizeInput($_GET['fecha_filtro']) : '';

// Construir consulta SQL base
$sql = "SELECT c.*, s.nombre as servicio FROM citas c 
        LEFT JOIN servicios s ON c.servicio_id = s.id";
$params = [];

// Aplicar filtros
if (!empty($estado_filtro)) {
    $sql .= " WHERE c.estado = ?";
    $params[] = $estado_filtro;
}

if (!empty($fecha_filtro)) {
    if (empty($estado_filtro)) {
        $sql .= " WHERE";
    } else {
        $sql .= " AND";
    }
    $sql .= " c.fecha = ?";
    $params[] = $fecha_filtro;
}

// Ordenar por fecha y hora
$sql .= " ORDER BY c.fecha DESC, c.hora ASC";

// Obtener citas
$citas = fetchAll($sql, $params);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Gestión de Citas -->
        <div class="content-section">
            <div class="section-header">
            </div>
            
            <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
            <?php endif; ?>
            
            <!-- Filtros -->
            <div class="filters">
                <form action="citas.php" method="get" class="filter-form">
                    <div class="filter-group">
                        <label for="estado_filtro">Estado:</label>
                        <select id="estado_filtro" name="estado_filtro">
                            <option value="">Todos</option>
                            <option value="pendiente" <?php echo $estado_filtro === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="confirmada" <?php echo $estado_filtro === 'confirmada' ? 'selected' : ''; ?>>Confirmada</option>
                            <option value="completada" <?php echo $estado_filtro === 'completada' ? 'selected' : ''; ?>>Completada</option>
                            <option value="cancelada" <?php echo $estado_filtro === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="fecha_filtro">Fecha:</label>
                        <input type="date" id="fecha_filtro" name="fecha_filtro" value="<?php echo $fecha_filtro; ?>" class="date-picker">
                    </div>
                    
                    <div class="filter-group">
                        <button type="submit" class="btn btn-sm">Filtrar</button>
                        <a href="citas.php" class="btn btn-sm btn-outline">Limpiar</a>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Contacto</th>
                            <th>Servicio</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Vehículo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($citas)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay citas registradas</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($citas as $cita): ?>
                            <tr>
                                <td><?php echo $cita['id']; ?></td>
                                <td><?php echo $cita['nombre_cliente']; ?></td>
                                <td>
                                    <a href="mailto:<?php echo $cita['email_cliente']; ?>"><?php echo $cita['email_cliente']; ?></a><br>
                                    <a href="tel:<?php echo $cita['telefono_cliente']; ?>"><?php echo $cita['telefono_cliente']; ?></a>
                                </td>
                                <td><?php echo $cita['servicio']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($cita['fecha'])); ?></td>
                                <td><?php echo $cita['hora']; ?></td>
                                <td><?php echo $cita['vehiculo_marca'] . ' ' . $cita['vehiculo_modelo'] . ' (' . $cita['vehiculo_anio'] . ')'; ?></td>
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
                                        <div class="dropdown">
                                            <button class="btn-icon dropdown-toggle" title="Cambiar estado">
                                                <i class="icon-edit-status"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="citas.php?id=<?php echo $cita['id']; ?>&estado=pendiente">Pendiente</a>
                                                <a href="citas.php?id=<?php echo $cita['id']; ?>&estado=confirmada">Confirmada</a>
                                                <a href="citas.php?id=<?php echo $cita['id']; ?>&estado=completada">Completada</a>
                                                <a href="citas.php?id=<?php echo $cita['id']; ?>&estado=cancelada">Cancelada</a>
                                            </div>
                                        </div>
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
