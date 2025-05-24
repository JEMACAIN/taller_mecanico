<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once '../config/database.php';
include_once '../includes/functions.php';

$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
if (!$result || $result['total'] == 0) {
    header('Location: ../index.php');
    exit;
}

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$titulo = '';
$descripcion = '';
$descuento = '';
$fecha_inicio = '';
$fecha_fin = '';
$activo = 1;
$imagen = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = sanitizeInput($_POST['titulo']);
    $descripcion = $_POST['descripcion'];
    $descuento = (float)$_POST['descuento'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    if (empty($titulo) || empty($descripcion) || empty($descuento) || empty($fecha_inicio) || empty($fecha_fin)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        // Procesar imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $resultado = uploadImage($_FILES['imagen'], '../img/promociones');
            if ($resultado['success']) {
                $imagen = str_replace('../', '', $resultado['file_path']);
            } else {
                $error = $resultado['message'];
            }
        } else {
            $error = 'Por favor, seleccione una imagen para la promoción.';
        }
        if (empty($error)) {
            $data = [
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'descuento' => $descuento,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'activo' => $activo,
                'imagen' => $imagen
            ];
            $insertId = insert('promociones', $data);
            if ($insertId) {
                $success = true;
                $titulo = '';
                $descripcion = '';
                $descuento = '';
                $fecha_inicio = '';
                $fecha_fin = '';
                $activo = 1;
            } else {
                $error = 'Error al guardar la promoción. Por favor, inténtelo de nuevo.';
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
    <title>Nueva Promoción - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include_once 'includes/header.php'; ?>
        <div class="content-section">
            <div class="section-header">
                <h1>Nueva Promoción</h1>
                <a href="promociones.php" class="btn btn-secondary">Volver a Promociones</a>
            </div>
            <?php if ($success): ?>
            <div class="alert alert-success">Promoción creada correctamente.</div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="form-container">
                <form method="post" action="promocion-nueva.php" enctype="multipart/form-data" class="needs-validation">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="titulo">Título *</label>
                            <input type="text" id="titulo" name="titulo" value="<?php echo $titulo; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="descuento">Descuento (%) *</label>
                            <input type="number" id="descuento" name="descuento" value="<?php echo $descuento; ?>" step="0.01" min="0" max="100" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio *</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin *</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $fecha_fin; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="imagen">Imagen *</label>
                            <input type="file" id="imagen" name="imagen" accept="image/*" required>
                            <div class="image-preview">
                                <img id="imagePreview" src="#" alt="Vista previa" style="display: none; max-width: 100%; margin-top: 10px;">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción *</label>
                        <textarea id="descripcion" name="descripcion" rows="6" class="rich-editor" required><?php echo $descripcion; ?></textarea>
                    </div>
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="activo" name="activo" <?php echo $activo ? 'checked' : ''; ?>>
                            <label for="activo">Activo</label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Promoción</button>
                        <a href="promociones.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="js/sidebar.js" defer></script>
    <script src="js/admin.js" defer></script>
</body>
</html> 