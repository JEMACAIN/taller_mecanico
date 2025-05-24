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
    header('Location: productos.php');
    exit;
}

$id = (int)$_GET['id'];

// Obtener información del producto
$producto = getProducto($id);

// Si el producto no existe, redirigir
if (!$producto) {
    header('Location: productos.php');
    exit;
}

// Inicializar variables
$nombre = $producto['nombre'];
$descripcion_corta = $producto['descripcion_corta'];
$descripcion = $producto['descripcion'];
$precio = $producto['precio'];
$stock = $producto['stock'];
$categoria = $producto['categoria'];
$marca = $producto['marca'];
$destacado = $producto['destacado'];
$activo = $producto['activo'];
$imagen_actual = $producto['imagen'];
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
    $stock = (int)$_POST['stock'];
    $categoria = sanitizeInput($_POST['categoria']);
    $marca = sanitizeInput($_POST['marca']);
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validar campos
    if (empty($nombre) || empty($descripcion_corta) || empty($descripcion) || 
        empty($precio) || empty($categoria) || empty($marca)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        // Generar slug
        $slug = generateSlug($nombre);
        
        // Verificar si el slug ya existe (excluyendo el producto actual)
        $existeSlug = fetchOne("SELECT id FROM productos WHERE slug = ? AND id != ?", [$slug, $id]);
        
        if ($existeSlug) {
            $error = 'Ya existe un producto con un nombre similar. Por favor, elija otro nombre.';
        } else {
            // Preparar datos para actualizar
            $data = [
                'nombre' => $nombre,
                'slug' => $slug,
                'descripcion_corta' => $descripcion_corta,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'stock' => $stock,
                'categoria' => $categoria,
                'marca' => $marca,
                'destacado' => $destacado,
                'activo' => $activo
            ];
            
            // Procesar imagen si se proporciona una nueva
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $resultado = uploadImage($_FILES['imagen'], '../img/productos');
                
                if ($resultado['success']) {
                    $data['imagen'] = str_replace('../', '', $resultado['file_path']);
                } else {
                    $error = $resultado['message'];
                }
            }
            
            if (empty($error)) {
                // Actualizar producto
                $actualizado = update('productos', $data, 'id = ?', [$id]);
                
                if ($actualizado) {
                    $success = true;
                    
                    // Actualizar información del producto
                    $producto = getProducto($id);
                    $imagen_actual = $producto['imagen'];
                } else {
                    $error = 'Error al actualizar el producto. Por favor, inténtelo de nuevo.';
                }
            }
        }
    }
}

// Obtener categorías existentes para sugerencias
$categorias = fetchAll("SELECT DISTINCT categoria FROM productos ORDER BY categoria ASC");
$marcas = fetchAll("SELECT DISTINCT marca FROM productos ORDER BY marca ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include_once 'includes/header.php'; ?>
        <div class="content-section">
            <div class="section-header">
                <h1>Editar Producto</h1>
                <a href="productos.php" class="btn btn-secondary">Volver a Productos</a>
            </div>
            <?php if ($success): ?>
            <div class="alert alert-success">Producto actualizado correctamente.</div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="form-container">
                <form method="post" action="producto-editar.php?id=<?php echo $id; ?>" enctype="multipart/form-data" class="needs-validation">
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
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" value="<?php echo $stock; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoría *</label>
                            <input type="text" id="categoria" name="categoria" value="<?php echo $categoria; ?>" list="categorias-list" required>
                            <datalist id="categorias-list">
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['categoria']; ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="marca">Marca *</label>
                            <input type="text" id="marca" name="marca" value="<?php echo $marca; ?>" list="marcas-list" required>
                            <datalist id="marcas-list">
                                <?php foreach ($marcas as $m): ?>
                                <option value="<?php echo $m['marca']; ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="imagen">Imagen</label>
                            <input type="file" id="imagen" name="imagen" accept="image/*">
                            <p class="form-help">Deje este campo vacío si no desea cambiar la imagen actual.</p>
                            <div class="image-preview">
                                <img id="imagePreview" src="<?php echo '../' . $imagen_actual; ?>" alt="<?php echo $nombre; ?>" style="max-width: 100%; margin-top: 10px;<?php echo empty($imagen_actual) ? 'display:none;' : ''; ?>">
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
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="productos.php" class="btn btn-secondary">Cancelar</a>
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
