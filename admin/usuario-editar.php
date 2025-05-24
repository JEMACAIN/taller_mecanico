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
    header('Location: usuarios.php');
    exit;
}

$id = (int)$_GET['id'];

// Obtener información del usuario
$usuario = fetchOne("SELECT * FROM usuarios WHERE id = ?", [$id]);

// Si el usuario no existe, redirigir
if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

// Inicializar variables
$nombre = $usuario['nombre'];
$username = $usuario['username'];
$email = $usuario['email'];
$rol = $usuario['rol'];
$error = '';
$success = false;

date_default_timezone_set('Etc/GMT+7');

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
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $rol = sanitizeInput($_POST['rol']);
    
    // Validar campos
    if (empty($nombre) || empty($username) || empty($email)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, ingrese un correo electrónico válido.';
    } elseif (!empty($password) && $password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Verificar si el usuario ya existe (excluyendo el usuario actual)
        $existeUsuario = fetchOne("SELECT id FROM usuarios WHERE (username = ? OR email = ?) AND id != ?", [$username, $email, $id]);
        
        if ($existeUsuario) {
            $error = 'El nombre de usuario o correo electrónico ya está en uso.';
        } else {
            // Preparar datos para actualizar
            $data = [
                'nombre' => $nombre,
                'username' => $username,
                'email' => $email,
                'rol' => $rol
            ];
            
            // Actualizar contraseña si se proporciona
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            
            // Actualizar usuario
            $actualizado = update('usuarios', $data, 'id = ?', [$id]);
            
            if ($actualizado) {
                $success = true;
                
                // Actualizar información del usuario
                $usuario = fetchOne("SELECT * FROM usuarios WHERE id = ?", [$id]);
                $nombre = $usuario['nombre'];
                $username = $usuario['username'];
                $email = $usuario['email'];
                $rol = $usuario['rol'];
            } else {
                $error = 'Error al actualizar el usuario. Por favor, inténtelo de nuevo.';
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
    <title>Editar Usuario - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Editar Usuario -->
        <div class="content-section">
            <div class="section-header">
                <h1>Editar Usuario</h1>
                <a href="usuarios.php" class="btn btn-secondary">Volver a Usuarios</a>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                Usuario actualizado correctamente.
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="post" action="usuario-editar.php?id=<?php echo $id; ?>" class="needs-validation">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre Completo *</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Nombre de Usuario *</label>
                            <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password">
                            <p class="form-help">Deje este campo vacío si no desea cambiar la contraseña.</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Confirmar Contraseña</label>
                            <input type="password" id="password_confirm" name="password_confirm">
                        </div>
                        
                        <div class="form-group">
                            <label for="rol">Rol *</label>
                            <select id="rol" name="rol" required>
                                <option value="admin" <?php echo $rol === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                <option value="editor" <?php echo $rol === 'editor' ? 'selected' : ''; ?>>Editor</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js" defer></script>
</body>
</html>
