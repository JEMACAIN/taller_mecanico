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

// Inicializar variables
$nombre = '';
$descripcion_corta = '';
$descripcion = '';
$precio = '';
$duracion = '';
$destacado = 0;
$orden = 0;
$activo = 1;
$error = '';
$success = false;

// Verificar si existe algún usuario en la base de datos
$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
if (!$result || $result['total'] == 0) {
    header('Location: ../index.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $nombre = sanitizeInput($_POST['nombre']);
    $descripcion_corta = sanitizeInput($_POST['descripcion_corta']);
    $descripcion = $_POST['descripcion'];
    $precio = (float)$_POST['precio'];
    $duracion = (int)$_POST['duracion'];
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $orden = (int)$_POST['orden'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validar campos
    if (empty($nombre) || empty($descripcion_corta) || empty($descripcion) || empty($precio) || empty($duracion)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        // Generar slug
        $slug = generateSlug($nombre);
        
        // Verificar si el slug ya existe
        $existeSlug = fetchOne("SELECT id FROM servicios WHERE slug = ?", [$slug]);
        
        if ($existeSlug) {
            $error = 'Ya existe un servicio con un nombre similar. Por favor, elija otro nombre.';
        } else {
            // Procesar imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $resultado = uploadImage($_FILES['imagen'], '../img/servicios');
                
                if ($resultado['success']) {
                    $imagen = str_replace('../', '', $resultado['file_path']);
                    
                    // Insertar servicio
                    $data = [
                        'nombre' => $nombre,
                        'slug' => $slug,
                        'descripcion_corta' => $descripcion_corta,
                        'descripcion' => $descripcion,
                        'precio' => $precio,
                        'duracion' => $duracion,
                        'imagen' => $imagen,
                        'destacado' => $destacado,
                        'orden' => $orden,
                        'activo' => $activo
                    ];
                    
                    $insertId = insert('servicios', $data);
                    
                    if ($insertId) {
                        $success = true;
                        
                        // Limpiar formulario
                        $nombre = '';
                        $descripcion_corta = '';
                        $descripcion = '';
                        $precio = '';
                        $duracion = '';
                        $destacado = 0;
                        $orden = 0;
                        $activo = 1;
                    } else {
                        $error = 'Error al guardar el servicio. Por favor, inténtelo de nuevo.';
                    }
                } else {
                    $error = $resultado['message'];
                }
            } else {
                $error = 'Por favor, seleccione una imagen para el servicio.';
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
    <title>Nuevo Servicio - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Nuevo Servicio -->
        <div class="content-section">
            <div class="section-header">
                <h1>Nuevo Servicio</h1>
                <a href="servicios.php" class="btn btn-secondary">Volver a Servicios</a>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                Servicio creado correctamente.
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="post" action="servicio-nuevo.php" enctype="multipart/form-data" class="needs-validation">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="precio">Precio *</label>
                            <input type="number" id="precio" name="precio" value="<?php echo $precio; ?>" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="duracion">Duración (minutos) *</label>
                            <input type="number" id="duracion" name="duracion" value="<?php echo $duracion; ?>" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="orden">Orden</label>
                            <input type="number" id="orden" name="orden" value="<?php echo $orden; ?>" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="imagen">Imagen *</label>
                            <input type="file" id="imagen" name="imagen" accept="image/*" required>
                            <div class="image-preview">
                                <img id="imagePreview" src="#" alt="Vista previa" style="display: none; max-width: 100%; margin-top: 10px;">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion_corta">Descripción Corta *</label>
                            <input type="text" id="descripcion_corta" name="descripcion_corta" value="<?php echo $descripcion_corta; ?>" maxlength="255" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción Completa *</label>
                        <textarea id="descripcion" name="descripcion" rows="6" class="rich-editor" required><?php echo $descripcion; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="destacado" name="destacado" <?php echo $destacado ? 'checked' : ''; ?>>
                            <label for="destacado">Destacado</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="activo" name="activo" <?php echo $activo ? 'checked' : ''; ?>>
                            <label for="activo">Activo</label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Servicio</button>
                        <a href="servicios.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js" defer></script>
</body>
</html>
