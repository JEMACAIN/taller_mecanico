<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ya está autenticado
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Incluir archivos necesarios
include_once '../config/database.php';
include_once '../includes/functions.php';

// Inicializar variables
$error = '';
$username = '';

// Procesar formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    // Validar campos
    if (empty($username) || empty($password)) {
        $error = 'Por favor, complete todos los campos.';
    } else {
        // Verificar credenciales
        $sql = "SELECT * FROM usuarios WHERE username = ?";
        $user = fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['rol'];
            
            // Redirigir al dashboard
            header('Location: index.php');
            exit;
        } else {
            $error = 'Nombre de usuario o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Panel de Administración</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="../img/logo.png" alt="Logo Taller Mecánico" class="login-logo">
                <h1>Panel de Administración</h1>
            </div>
            
            <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="login.php" class="login-form">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                </div>
            </form>
            
            <div class="login-footer">
                <a href="../index.php">Volver al sitio web</a>
            </div>
        </div>
    </div>
</body>
</html>
