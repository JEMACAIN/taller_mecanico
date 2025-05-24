<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include_once '../config/database.php';
include_once '../includes/functions.php';

if (!isAdmin() && !isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
if (!$result || $result['total'] == 0) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$user = fetchOne("SELECT * FROM usuarios WHERE id = ?", [$userId]);
if (!$user) {
    header('Location: logout.php');
    exit;
}

$nombre = $user['nombre'];
$username = $user['username'];
$email = $user['email'];
$error = '';
$success = false;

date_default_timezone_set('Etc/GMT+7');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = sanitizeInput($_POST['nombre']);
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($nombre) || empty($username) || empty($email)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } elseif (!empty($password) && $password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!empty($password) && strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        $data = [
            'nombre' => $nombre,
            'username' => $username,
            'email' => $email
        ];
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $updated = update('usuarios', $data, 'id = ?', [$userId]);
        if ($updated) {
            $success = true;
            // Refrescar datos
            $user = fetchOne("SELECT * FROM usuarios WHERE id = ?", [$userId]);
            $nombre = $user['nombre'];
            $username = $user['username'];
            $email = $user['email'];
        } else {
            $error = 'Error al actualizar el perfil. Inténtelo de nuevo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Panel de Administración</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include_once 'includes/header.php'; ?>
        <div class="content-section">
            <div class="section-header">
                <h1>Mi Perfil</h1>
            </div>
            <?php if ($success): ?>
            <div class="alert alert-success">Perfil actualizado correctamente.</div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="form-container" style="max-width: 500px; margin: 0 auto;">
                <form method="post" action="perfil.php" class="needs-validation">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Usuario *</label>
                        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo electrónico *</label>
                        <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Nueva Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiar">
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Confirmar Nueva Contraseña</label>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="Dejar en blanco para no cambiar">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="js/sidebar.js" defer></script>
    <script src="js/admin.js" defer></script>
</body>
</html> 