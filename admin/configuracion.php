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

// Obtener configuración actual
$config = getSiteConfig();

// Inicializar variables
$error = '';
$success = false;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre_sitio = sanitizeInput($_POST['nombre_sitio']);
    $descripcion_corta = sanitizeInput($_POST['descripcion_corta']);
    $telefono = sanitizeInput($_POST['telefono']);
    $email = sanitizeInput($_POST['email']);
    $direccion = sanitizeInput($_POST['direccion']);
    $horario = sanitizeInput($_POST['horario']);
    $facebook = sanitizeInput($_POST['facebook']);
    $instagram = sanitizeInput($_POST['instagram']);
    $twitter = sanitizeInput($_POST['twitter']);
    $email_from = sanitizeInput($_POST['email_from']);
    
    // Validar campos
    if (empty($nombre_sitio) || empty($descripcion_corta) || empty($telefono) || 
        empty($email) || empty($direccion) || empty($horario) || empty($email_from)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        // Actualizar configuración
        $actualizaciones = [
            'nombre_sitio' => $nombre_sitio,
            'descripcion_corta' => $descripcion_corta,
            'telefono' => $telefono,
            'email' => $email,
            'direccion' => $direccion,
            'horario' => $horario,
            'facebook' => $facebook,
            'instagram' => $instagram,
            'twitter' => $twitter,
            'email_from' => $email_from
        ];
        
        $errores = false;
        
        foreach ($actualizaciones as $clave => $valor) {
            $actualizado = update('configuracion', ['valor' => $valor], 'clave = ?', [$clave]);
            if ($actualizado === false) {
                $errores = true;
            }
        }
        
        // Procesar logo si se proporciona uno nuevo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $resultado = uploadImage($_FILES['logo'], '../img');
            
            if ($resultado['success']) {
                $logo = str_replace('../', '', $resultado['file_path']);
                $actualizado = update('configuracion', ['valor' => $logo], 'clave = ?', ['logo']);
                
                if ($actualizado === false) {
                    $errores = true;
                }
            } else {
                $error = $resultado['message'];
                $errores = true;
            }
        }
        
        if (!$errores && empty($error)) {
            $success = true;
            
            // Actualizar configuración
            $config = getSiteConfig();
        } else if (empty($error)) {
            $error = 'Error al actualizar la configuración. Por favor, inténtelo de nuevo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sitio - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Configuración del Sitio -->
        <div class="content-section">
            <div class="section-header">
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                Configuración actualizada correctamente.
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="post" action="configuracion.php" enctype="multipart/form-data" class="needs-validation">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre_sitio">Nombre del Sitio *</label>
                            <input type="text" id="nombre_sitio" name="nombre_sitio" value="<?php echo $config['nombre_sitio']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion_corta">Descripción Corta *</label>
                            <input type="text" id="descripcion_corta" name="descripcion_corta" value="<?php echo $config['descripcion_corta']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">Teléfono *</label>
                            <input type="text" id="telefono" name="telefono" value="<?php echo $config['telefono']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo $config['email']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="direccion">Dirección *</label>
                            <input type="text" id="direccion" name="direccion" value="<?php echo $config['direccion']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="horario">Horario *</label>
                            <input type="text" id="horario" name="horario" value="<?php echo $config['horario']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="facebook">Facebook</label>
                            <input type="url" id="facebook" name="facebook" value="<?php echo $config['facebook']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="instagram">Instagram</label>
                            <input type="url" id="instagram" name="instagram" value="<?php echo $config['instagram']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter">Twitter</label>
                            <input type="url" id="twitter" name="twitter" value="<?php echo $config['twitter']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email_from">Email para envío de correos *</label>
                            <input type="email" id="email_from" name="email_from" value="<?php echo $config['email_from']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <input type="file" id="logo" name="logo" accept="image/*">
                            <p class="form-help">Deje este campo vacío si no desea cambiar el logo actual.</p>
                            <div class="image-preview">
                                <img src="<?php echo '../' . $config['logo']; ?>" alt="Logo" style="max-width: 200px; margin-top: 10px;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js" defer></script>
</body>
</html>
