<?php
// Verificar si hay usuarios creados
$sql = "SELECT COUNT(*) as total FROM usuarios";
$result = fetchOne($sql);
if (!$result || $result['total'] == 0) {
    header('Location: setup_admin.php');
    exit;
}
?>