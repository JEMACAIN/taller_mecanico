<?php
// Este archivo debe ser incluido solo si la conexión es válida y no hay usuarios
$error = '';
$nombre = '';
$username = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_admin'])) {
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
                $_SESSION['user_id'] = $insertId;
                $_SESSION['username'] = $username;
                $_SESSION['user_role'] = 'admin';
                @unlink(__DIR__ . '/setup_admin.php');
                header('Location: index.php');
                exit;
            } else {
                $error = 'Error al crear el usuario administrador.';
            }
        }
    }
}
?>
<?php if (!empty($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
<form method="post" action="" class="login-form" autocomplete="off">
    <div class="form-group">
        <label for="nombre">Nombre Completo *</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required maxlength="100" autocomplete="name">
    </div>
    <div class="form-group">
        <label for="username">Nombre de Usuario *</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required maxlength="50" pattern="^[a-zA-Z0-9_]+$" autocomplete="username">
    </div>
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required maxlength="100" autocomplete="email">
    </div>
    <div class="form-group">
        <label for="password">Contraseña *</label>
        <input type="password" id="password" name="password" required minlength="6" maxlength="64" autocomplete="new-password">
    </div>
    <div class="form-group">
        <label for="password_confirm">Confirmar Contraseña *</label>
        <input type="password" id="password_confirm" name="password_confirm" required minlength="6" maxlength="64" autocomplete="new-password">
    </div>
    <div class="form-actions">
        <button type="submit" name="crear_admin" class="btn btn-primary btn-block">Crear Administrador</button>
    </div>
</form> 