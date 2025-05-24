<?php
include_once __DIR__ . '/database.php';

// Intentar conexión a la base de datos
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn->connect_error) {
        header('Location: ../index.php');
        exit();
    }
} catch (Throwable $e) {
    // Si falla, continúa mostrando la página de instrucciones
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Base de Datos Requerida</title>
    <style>
        body { background: #f8f9fa; font-family: Arial, sans-serif; }
        .container { max-width: 500px; margin: 60px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px 28px; }
        h1 { color: #d4380d; font-size: 1.5rem; margin-bottom: 16px; }
        .alert { background: #fffbe6; border: 1px solid #ffe58f; color: #ad8b00; padding: 16px; border-radius: 8px; margin-bottom: 20px; font-size: 1rem; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 6px; font-size: 0.95em; white-space: pre-wrap; word-break: break-all; overflow-x: auto; }
        code { color: #d4380d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Atención! No se pudo conectar a la base de datos</h1>
        <div class="alert">
            <strong>¿Qué hacer?</strong><br>
            1. Verifica que la base de datos, usuario y contraseña sean correctos en <code>config/database.php</code>.<br>
            2. Si no tienes la base de datos creada, ejecuta estos comandos en tu consola de MySQL o phpMyAdmin:<br>
            <pre>CREATE DATABASE nombre_de_tu_base_de_datos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'usuario'@'localhost' IDENTIFIED BY 'contraseña';
GRANT ALL PRIVILEGES ON nombre_de_tu_base_de_datos.* TO 'usuario'@'localhost';
FLUSH PRIVILEGES;</pre>
            3. Asegúrate de que el usuario tenga permisos suficientes y que el servidor MySQL esté en funcionamiento.<br>
            4. Edita <code>config/database.php</code> con los datos correctos:<br>
            <pre>define('DB_HOST', 'localhost');
define('DB_USER', 'usuario');
define('DB_PASS', 'contraseña');
define('DB_NAME', 'nombre_de_tu_base_de_datos');</pre>
            5. Guarda los cambios y recarga esta página.<br><br>
            Si el problema persiste, consulta con tu proveedor de hosting o soporte técnico.<br>
        </div>
    </div>
</body>
</html> 