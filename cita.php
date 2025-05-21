<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';

// Inicializar variables
$nombre = '';
$email = '';
$telefono = '';
$servicio_id = isset($_GET['servicio']) ? (int)$_GET['servicio'] : 0;
$fecha = '';
$hora = '';
$vehiculo_marca = '';
$vehiculo_modelo = '';
$vehiculo_anio = '';
$comentarios = '';
$promocion_id = isset($_GET['promocion']) ? (int)$_GET['promocion'] : 0;
$success = false;
$error = '';

// Obtener servicios disponibles
$servicios = fetchAll("SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC");

// Obtener horas disponibles
$horas_disponibles = [
    '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', 
    '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
    '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
    '17:00', '17:30'
];

// Si hay una promoción seleccionada, obtener información
if ($promocion_id > 0) {
    $promocion = getPromocion($promocion_id);
    if ($promocion) {
        $comentarios = "Aplicar promoción: " . $promocion['titulo'] . " (ID: " . $promocion['id'] . ")";
        if (!empty($promocion['codigo'])) {
            $comentarios .= " - Código: " . $promocion['codigo'];
        }
    }
}

// Procesar formulario de cita
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = sanitizeInput($_POST['nombre']);
    $email = sanitizeInput($_POST['email']);
    $telefono = sanitizeInput($_POST['telefono']);
    $servicio_id = (int)$_POST['servicio_id'];
    $fecha = sanitizeInput($_POST['fecha']);
    $hora = sanitizeInput($_POST['hora']);
    $vehiculo_marca = sanitizeInput($_POST['vehiculo_marca']);
    $vehiculo_modelo = sanitizeInput($_POST['vehiculo_modelo']);
    $vehiculo_anio = (int)$_POST['vehiculo_anio'];
    $comentarios = sanitizeInput($_POST['comentarios']);
    
    // Validar campos
    if (empty($nombre) || empty($email) || empty($telefono) || empty($servicio_id) || 
        empty($fecha) || empty($hora) || empty($vehiculo_marca) || 
        empty($vehiculo_modelo) || empty($vehiculo_anio)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, ingrese un correo electrónico válido.';
    } elseif (strtotime($fecha) < strtotime(date('Y-m-d'))) {
        $error = 'La fecha de la cita no puede ser anterior a hoy.';
    } else {
        // Verificar disponibilidad de la cita
        $citas_existentes = fetchAll(
            "SELECT * FROM citas WHERE fecha = ? AND hora = ? AND estado != 'cancelada'", 
            [$fecha, $hora]
        );
        
        if (count($citas_existentes) > 0) {
            $error = 'Lo sentimos, la hora seleccionada ya no está disponible. Por favor, elija otra hora.';
        } else {
            // Guardar cita en la base de datos
            $data = [
                'nombre_cliente' => $nombre,
                'email_cliente' => $email,
                'telefono_cliente' => $telefono,
                'servicio_id' => $servicio_id,
                'fecha' => $fecha,
                'hora' => $hora,
                'vehiculo_marca' => $vehiculo_marca,
                'vehiculo_modelo' => $vehiculo_modelo,
                'vehiculo_anio' => $vehiculo_anio,
                'comentarios' => $comentarios,
                'estado' => 'pendiente'
            ];
            
            $insertId = insert('citas', $data);
            
            if ($insertId) {
                // Obtener información del servicio
                $servicio = getServicio($servicio_id);
                
                // Enviar correo de confirmación al cliente
                $emailBody = "
                    <h2>Confirmación de Cita</h2>
                    <p>Estimado/a {$nombre},</p>
                    <p>Gracias por agendar una cita con nosotros. A continuación, encontrarás los detalles de tu cita:</p>
                    <p><strong>Servicio:</strong> {$servicio['nombre']}</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y', strtotime($fecha)) . "</p>
                    <p><strong>Hora:</strong> {$hora}</p>
                    <p><strong>Vehículo:</strong> {$vehiculo_marca} {$vehiculo_modelo} ({$vehiculo_anio})</p>
                    <p>Si necesitas modificar o cancelar tu cita, por favor contáctanos al " . getSiteConfig('telefono') . ".</p>
                    <p>¡Esperamos verte pronto!</p>
                    <p>Atentamente,<br>El equipo de " . getSiteConfig('nombre_sitio') . "</p>
                ";
                
                sendEmail($email, 'Confirmación de Cita - ' . getSiteConfig('nombre_sitio'), $emailBody);
                
                // Enviar notificación al administrador
                $adminEmailBody = "
                    <h2>Nueva Cita Agendada</h2>
                    <p><strong>Cliente:</strong> {$nombre}</p>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Teléfono:</strong> {$telefono}</p>
                    <p><strong>Servicio:</strong> {$servicio['nombre']}</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y', strtotime($fecha)) . "</p>
                    <p><strong>Hora:</strong> {$hora}</p>
                    <p><strong>Vehículo:</strong> {$vehiculo_marca} {$vehiculo_modelo} ({$vehiculo_anio})</p>
                    <p><strong>Comentarios:</strong> {$comentarios}</p>
                ";
                
                sendEmail(getSiteConfig('email'), 'Nueva Cita Agendada', $adminEmailBody);
                
                // Limpiar formulario
                $nombre = '';
                $email = '';
                $telefono = '';
                $servicio_id = 0;
                $fecha = '';
                $hora = '';
                $vehiculo_marca = '';
                $vehiculo_modelo = '';
                $vehiculo_anio = '';
                $comentarios = '';
                
                $success = true;
            } else {
                $error = 'Hubo un error al agendar la cita. Por favor, inténtelo de nuevo.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita - Taller Mecánico</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Banner de Cita -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>AGENDAR CITA</h1>
            <p>Programa tu servicio de manera rápida y sencilla</p>
        </div>
    </section>
    
    <!-- Formulario de Cita -->
    <section class="cita-formulario">
        <div class="container">
            <div class="formulario-container">
                <div class="formulario-header">
                    <h2>Agenda tu Cita</h2>
                    <p>Completa el formulario para programar tu servicio. Te confirmaremos tu cita a la brevedad.</p>
                </div>
                
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <p>¡Gracias por agendar tu cita! Hemos enviado un correo de confirmación con los detalles. Te esperamos en la fecha y hora programada.</p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <p><?php echo $error; ?></p>
                </div>
                <?php endif; ?>
                
                <form method="post" action="cita.php" class="cita-form">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo *</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">Teléfono *</label>
                            <input type="tel" id="telefono" name="telefono" value="<?php echo $telefono; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="servicio_id">Servicio *</label>
                            <select id="servicio_id" name="servicio_id" required>
                                <option value="">Seleccione un servicio</option>
                                <?php foreach($servicios as $servicio): ?>
                                <option value="<?php echo $servicio['id']; ?>" <?php echo $servicio_id == $servicio['id'] ? 'selected' : ''; ?>>
                                    <?php echo $servicio['nombre']; ?> - $<?php echo formatPrice($servicio['precio']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha">Fecha *</label>
                            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="hora">Hora *</label>
                            <select id="hora" name="hora" required>
                                <option value="">Seleccione una hora</option>
                                <?php foreach($horas_disponibles as $hora_disponible): ?>
                                <option value="<?php echo $hora_disponible; ?>" <?php echo $hora == $hora_disponible ? 'selected' : ''; ?>>
                                    <?php echo $hora_disponible; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="vehiculo_marca">Marca del Vehículo *</label>
                            <input type="text" id="vehiculo_marca" name="vehiculo_marca" value="<?php echo $vehiculo_marca; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="vehiculo_modelo">Modelo del Vehículo *</label>
                            <input type="text" id="vehiculo_modelo" name="vehiculo_modelo" value="<?php echo $vehiculo_modelo; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="vehiculo_anio">Año del Vehículo *</label>
                            <input type="number" id="vehiculo_anio" name="vehiculo_anio" value="<?php echo $vehiculo_anio; ?>" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="comentarios">Comentarios Adicionales</label>
                        <textarea id="comentarios" name="comentarios" rows="4"><?php echo $comentarios; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Agendar Cita</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <!-- Información Adicional -->
    <section class="cita-info">
        <div class="container">
            <div class="info-container">
                <h2>Información Importante</h2>
                <ul>
                    <li>Te recomendamos agendar tu cita con al menos 2 días de anticipación.</li>
                    <li>Llega 10 minutos antes de tu cita programada.</li>
                    <li>Si necesitas cancelar o reprogramar tu cita, por favor hazlo con al menos 24 horas de anticipación.</li>
                    <li>El tiempo estimado para cada servicio puede variar dependiendo de las condiciones de tu vehículo.</li>
                    <li>Para cualquier duda o consulta, no dudes en contactarnos al <?php echo getSiteConfig('telefono'); ?>.</li>
                </ul>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
