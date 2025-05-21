<?php
// Incluir archivo de base de datos
include_once 'config/database.php';

// Función para obtener servicios
function getServicios($destacados = false) {
    $sql = "SELECT * FROM servicios";
    
    if ($destacados) {
        $sql .= " WHERE destacado = 1";
    }
    
    $sql .= " ORDER BY orden ASC";
    
    return fetchAll($sql);
}

// Función para obtener un servicio específico
function getServicio($id) {
    $sql = "SELECT * FROM servicios WHERE id = ?";
    return fetchOne($sql, [$id]);
}

// Función para obtener productos
function getProductos($destacados = false) {
    $sql = "SELECT * FROM productos";
    
    if ($destacados) {
        $sql .= " WHERE destacado = 1";
    }
    
    $sql .= " ORDER BY nombre ASC";
    
    return fetchAll($sql);
}

// Función para obtener un producto específico
function getProducto($id) {
    $sql = "SELECT * FROM productos WHERE id = ?";
    return fetchOne($sql, [$id]);
}

// Función para obtener promociones
function getPromociones() {
    $sql = "SELECT * FROM promociones WHERE fecha_fin >= CURDATE() ORDER BY fecha_fin ASC";
    return fetchAll($sql);
}

// Función para obtener una promoción específica
function getPromocion($id) {
    $sql = "SELECT * FROM promociones WHERE id = ?";
    return fetchOne($sql, [$id]);
}

// Función para obtener configuración del sitio
function getSiteConfig($key = null) {
    if ($key) {
        $sql = "SELECT valor FROM configuracion WHERE clave = ?";
        $result = fetchOne($sql, [$key]);
        return $result ? $result['valor'] : null;
    } else {
        $sql = "SELECT clave, valor FROM configuracion";
        $results = fetchAll($sql);
        
        $config = [];
        foreach ($results as $row) {
            $config[$row['clave']] = $row['valor'];
        }
        
        return $config;
    }
}

// Función para verificar si un usuario está autenticado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para verificar si un usuario es administrador
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin';
}

// Función para sanitizar entrada
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para generar slug
function generateSlug($text) {
    // Reemplazar caracteres no alfanuméricos con guiones
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    
    // Transliterar
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    
    // Eliminar caracteres no deseados
    $text = preg_replace('~[^-\w]+~', '', $text);
    
    // Trim
    $text = trim($text, '-');
    
    // Eliminar guiones duplicados
    $text = preg_replace('~-+~', '-', $text);
    
    // Convertir a minúsculas
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

// Función para subir imágenes
function uploadImage($file, $directory = 'uploads') {
    // Verificar si el directorio existe, si no, crearlo
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    
    $targetDir = $directory . '/';
    $fileName = basename($file['name']);
    $targetFile = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Verificar si el archivo es una imagen real
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return ['success' => false, 'message' => 'El archivo no es una imagen.'];
    }
    
    // Verificar tamaño del archivo
    if ($file['size'] > 5000000) { // 5MB
        return ['success' => false, 'message' => 'El archivo es demasiado grande.'];
    }
    
    // Permitir ciertos formatos de archivo
    if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
        return ['success' => false, 'message' => 'Solo se permiten archivos JPG, JPEG, PNG y GIF.'];
    }
    
    // Generar nombre único para evitar sobrescrituras
    $newFileName = uniqid() . '.' . $imageFileType;
    $targetFile = $targetDir . $newFileName;
    
    // Intentar subir el archivo
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'file_path' => $targetFile];
    } else {
        return ['success' => false, 'message' => 'Hubo un error al subir el archivo.'];
    }
}

// Función para formatear precio
function formatPrice($price) {
    return number_format($price, 2, '.', ',');
}

// Función para enviar correo
function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . getSiteConfig('email_from') . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}
