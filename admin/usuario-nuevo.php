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

$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
if (!$result || $result['total'] == 0) {
    header('Location: ../index.php');
    exit;
}

// Inicializar variables
$nombre = '';
$username = '';
$email = '';
$rol = 'editor';
$error = '';
$success = false;

date_default_timezone_set('Etc/GMT+7');

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
    if (empty($nombre) || empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, ingrese un correo electrónico válido.';
    } elseif ($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        // Verificar si el usuario ya existe
        $existeUsuario = fetchOne("SELECT id FROM usuarios WHERE username = ? OR email = ?", [$username, $email]);
        
        if ($existeUsuario) {
            $error = 'El nombre de usuario o correo electrónico ya está en uso.';
        } else {
            // Encriptar contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar usuario
            $data = [
                'nombre' => $nombre,
                'username' => $username,
                'email' => $email,
                'password' => $password_hash,
                'rol' => $rol
            ];
            
            $insertId = insert('usuarios', $data);
            
            if ($insertId) {
                $success = true;
                
                // Limpiar formulario
                $nombre = '';
                $username = '';
                $email = '';
                $rol = 'editor';
            } else {
                $error = 'Error al crear el usuario. Por favor, inténtelo de nuevo.';
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
    <title>Nuevo Usuario - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/admin.js" defer></script>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <?php include_once 'includes/header.php'; ?>
        
        <!-- Nuevo Usuario -->
        <div class="content-section">
            <div class="section-header">
                <h1>Nuevo Usuario</h1>
                <a href="usuarios.php" class="btn btn-secondary">Volver a Usuarios</a>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                Usuario creado correctamente.
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="post" action="usuario-nuevo.php" class="needs-validation">
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
                            <label for="password">Contraseña *</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Confirmar Contraseña *</label>
                            <input type="password" id="password_confirm" name="password_confirm" required>
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
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                        <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js" defer></script>
</body>
</html>
