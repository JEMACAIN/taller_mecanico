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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: promociones.php');
    exit;
}

$id = (int)$_GET['id'];
$promocion = getPromocion($id);
if (!$promocion) {
    header('Location: promociones.php');
    exit;
}

$titulo = $promocion['titulo'];
$descripcion = $promocion['descripcion'];
$descuento = $promocion['descuento'];
$fecha_inicio = $promocion['fecha_inicio'];
$fecha_fin = $promocion['fecha_fin'];
$activo = $promocion['activo'];
$imagen_actual = $promocion['imagen'];
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
        $data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'descuento' => $descuento,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'activo' => $activo
        ];
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $resultado = uploadImage($_FILES['imagen'], '../img/promociones');
            if ($resultado['success']) {
                $data['imagen'] = str_replace('../', '', $resultado['file_path']);
            } else {
                $error = $resultado['message'];
            }
        }
        if (empty($error)) {
            $actualizado = update('promociones', $data, 'id = ?', [$id]);
            if ($actualizado) {
                $success = true;
                $promocion = getPromocion($id);
                $imagen_actual = $promocion['imagen'];
            } else {
                $error = 'Error al actualizar la promoción. Por favor, inténtelo de nuevo.';
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
    <title>Editar Promoción - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include_once 'includes/header.php'; ?>
        <div class="content-section">
            <div class="section-header">
                <h1>Editar Promoción</h1>
                <a href="promociones.php" class="btn btn-secondary">Volver a Promociones</a>
            </div>
            <?php if ($success): ?>
            <div class="alert alert-success">Promoción actualizada correctamente.</div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="form-container">
                <form method="post" action="promocion-editar.php?id=<?php echo $id; ?>" enctype="multipart/form-data" class="needs-validation">
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
                            <label for="imagen">Imagen</label>
                            <input type="file" id="imagen" name="imagen" accept="image/*">
                            <p class="form-help">Deje este campo vacío si no desea cambiar la imagen actual.</p>
                            <div class="image-preview">
                                <img id="imagePreview" src="<?php echo '../' . $imagen_actual; ?>" alt="<?php echo $titulo; ?>" style="max-width: 100%; margin-top: 10px;<?php echo empty($imagen_actual) ? 'display:none;' : ''; ?>">
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
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="promociones.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="js/sidebar.js" defer></script>
    <script src="js/admin.js" defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var imageInput = document.getElementById('imagen');
        var imagePreview = document.getElementById('imagePreview');
        if(imageInput && imagePreview) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    </script>
</body>
</html> 