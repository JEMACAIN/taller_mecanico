<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';

// Verificar si existe la tabla usuarios
define('USERS_TABLE_SQL', __DIR__ . '/../sql/create_users_table.sql');

function tableExists($table) {
    $conn = getConnection();
    $result = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($table) . "'");
    $exists = $result && $result->num_rows > 0;
    $conn->close();
    return $exists;
}

function createUsersTable() {
    $sql = file_get_contents(USERS_TABLE_SQL);
    $conn = getConnection();
    $conn->multi_query($sql);
    // Limpiar resultados
    do { $conn->store_result(); } while ($conn->more_results() && $conn->next_result());
    $conn->close();
}

// 1. Crear tabla si no existe
if (!tableExists('usuarios')) {
    createUsersTable();
}

// 2. Verificar si existen usuarios
$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
if ($result && $result['total'] > 0) {
    // Si ya hay usuarios, redirigir al login y eliminar este archivo
    @unlink(__FILE__);
    header('Location: login.php');
    exit;
}

// 3. Procesar formulario para crear admin
$nombre = '';
$username = '';
$email = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizeInput($_POST['nombre'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($nombre) || empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, ingrese un correo electrónico válido.';
    } elseif ($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        $existeUsuario = fetchOne("SELECT id FROM usuarios WHERE username = ? OR email = ?", [$username, $email]);
        if ($existeUsuario) {
            $error = 'El nombre de usuario o correo electrónico ya está en uso.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $data = [
                'nombre' => $nombre,
                'username' => $username,
                'email' => $email,
                'password' => $password_hash,
                'rol' => 'admin'
            ];
            $insertId = insert('usuarios', $data);
            if ($insertId) {
                // Iniciar sesión automáticamente
                $_SESSION['user_id'] = $insertId;
                $_SESSION['username'] = $username;
                $_SESSION['user_role'] = 'admin';
                $success = true;
                // Eliminar este archivo por seguridad
                @unlink(__FILE__);
                header('Location: index.php');
                exit;
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
    <title>Configurar Administrador</title>
    <link rel="stylesheet" href="css/admin.css">
    <style>
    body.login-page { background: #f8f9fa; }
    .welcome-container { max-width: 400px; margin: 60px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px 28px; }
    .welcome-header { text-align: center; margin-bottom: 24px; }
    .welcome-logo { max-width: 120px; margin-bottom: 12px; }
    .welcome-header h1 { font-size: 1.6rem; margin-bottom: 8px; color: #ff0000; }
    .welcome-header p { color: #555; }
    </style>
</head>
<body class="login-page">
    <div class="welcome-container">
        <div class="welcome-header">
            <img src="../img/logo.png" alt="Logo Taller Mecánico" class="welcome-logo">
            <h1>Configurar Administrador</h1>
            <p>Crea el primer usuario administrador para tu sistema.</p>
        </div>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        <form method="post" action="" class="login-form">
            <div class="form-group">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Nombre de Usuario *</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirmar Contraseña *</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Crear Administrador</button>
            </div>
        </form>
    </div>
</body>
</html>
