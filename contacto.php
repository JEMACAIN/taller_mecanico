<?php
// Incluir archivos de configuración y funciones
include_once 'config/database.php';
include_once 'includes/functions.php';

// Inicializar variables
$nombre = '';
$email = '';
$telefono = '';
$asunto = '';
$mensaje = '';
$producto_id = isset($_GET['producto']) ? (int)$_GET['producto'] : 0;
$servicio_id = isset($_GET['servicio']) ? (int)$_GET['servicio'] : 0;
$success = false;
$error = '';

// Si hay un producto o servicio seleccionado, obtener información
if ($producto_id > 0) {
    $producto = getProducto($producto_id);
    if ($producto) {
        $asunto = "Consulta sobre " . $producto['nombre'];
        $mensaje = "Me interesa obtener más información sobre el producto: " . $producto['nombre'];
    }
} elseif ($servicio_id > 0) {
    $servicio = getServicio($servicio_id);
    if ($servicio) {
        $asunto = "Consulta sobre " . $servicio['nombre'];
        $mensaje = "Me interesa obtener más información sobre el servicio: " . $servicio['nombre'];
    }
}

// Procesar formulario de contacto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = sanitizeInput($_POST['nombre']);
    $email = sanitizeInput($_POST['email']);
    $telefono = sanitizeInput($_POST['telefono']);
    $asunto = sanitizeInput($_POST['asunto']);
    $mensaje = sanitizeInput($_POST['mensaje']);
    
    // Validar campos
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, ingrese un correo electrónico válido.';
    } else {
        // Guardar mensaje en la base de datos
        $data = [
            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono,
            'asunto' => $asunto,
            'mensaje' => $mensaje,
            'fecha' => date('Y-m-d H:i:s')
        ];
        
        $insertId = insert('mensajes', $data);
        
        if ($insertId) {
            // Enviar correo de notificación
            $emailBody = "
                <h2>Nuevo mensaje de contacto</h2>
                <p><strong>Nombre:</strong> {$nombre}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Teléfono:</strong> {$telefono}</p>
                <p><strong>Asunto:</strong> {$asunto}</p>
                <p><strong>Mensaje:</strong> {$mensaje}</p>
                <p><strong>Fecha:</strong> " . date('d/m/Y H:i:s') . "</p>
            ";
            
            sendEmail(getSiteConfig('email'), 'Nuevo mensaje de contacto', $emailBody);
            
            // Limpiar formulario
            $nombre = '';
            $email = '';
            $telefono = '';
            $asunto = '';
            $mensaje = '';
            
            $success = true;
        } else {
            $error = 'Hubo un error al enviar el mensaje. Por favor, inténtelo de nuevo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Taller Mecánico</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!-- Cabecera -->
    <?php include_once 'includes/header.php'; ?>
    
    <!-- Banner de Contacto -->
    <section class="page-banner">
        <div class="banner-content">
            <h1>CONTÁCTANOS</h1>
            <p>Estamos aquí para ayudarte con cualquier consulta</p>
        </div>
    </section>
    
    <!-- Información de Contacto -->
    <section class="contacto-info">
        <div class="container">
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="icon-location"></i>
                    </div>
                    <h3>Dirección</h3>
                    <p><?php echo getSiteConfig('direccion'); ?></p>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="icon-phone"></i>
                    </div>
                    <h3>Teléfono</h3>
                    <p><?php echo getSiteConfig('telefono'); ?></p>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="icon-email"></i>
                    </div>
                    <h3>Email</h3>
                    <p><?php echo getSiteConfig('email'); ?></p>
                </div>
                <div class="info-card">
                    <div class="info-icon">
                        <i class="icon-clock"></i>
                    </div>
                    <h3>Horario</h3>
                    <p><?php echo getSiteConfig('horario'); ?></p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Formulario de Contacto -->
    <section class="contacto-formulario">
        <div class="container">
            <div class="formulario-container">
                <div class="formulario-header">
                    <h2>Envíanos un Mensaje</h2>
                    <p>Completa el formulario y nos pondremos en contacto contigo lo antes posible.</p>
                </div>
                
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <p>¡Gracias por contactarnos! Tu mensaje ha sido enviado correctamente. Nos pondremos en contacto contigo lo antes posible.</p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <p><?php echo $error; ?></p>
                </div>
                <?php endif; ?>
                
                <form method="post" action="contacto.php" class="contacto-form">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo *</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo $telefono; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="asunto">Asunto *</label>
                        <input type="text" id="asunto" name="asunto" value="<?php echo $asunto; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="mensaje">Mensaje *</label>
                        <textarea id="mensaje" name="mensaje" rows="5" required><?php echo $mensaje; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <!-- Mapa -->
    <section class="contacto-mapa">
        <div class="container">
            <div class="mapa-container">
                <h2>Nuestra Ubicación</h2>
                <div class="mapa">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3762.661543178223!2d-99.16869708509426!3d19.427023986887467!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff35f5bd1563%3A0x6c366f0e2de02ff7!2sZocalo%2C%20Centro%20Historico%2C%20Centro%2C%2006000%20Ciudad%20de%20M%C3%A9xico%2C%20CDMX%2C%20M%C3%A9xico!5e0!3m2!1ses-419!2sus!4v1623433739028!5m2!1ses-419!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pie de Página -->
    <?php include_once 'includes/footer.php'; ?>
</body>
</html>
