<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '@ltatA.1987');
define('DB_NAME', 'taller_mecanico');

// Conexión a la base de datos
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Establecer charset
    $conn->set_charset("utf8");
    
    return $conn;
}

// Función para ejecutar consultas
function executeQuery($sql, $params = []) {
    $conn = getConnection();
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $types = '';
        $bindParams = [];
        
        // Determinar tipos de parámetros
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $bindParams[] = $param;
        }
        
        // Preparar array para bind_param
        $bindValues = array_merge([$types], $bindParams);
        $bindRef = [];
        
        for ($i = 0; $i < count($bindValues); $i++) {
            $bindRef[$i] = &$bindValues[$i];
        }
        
        call_user_func_array([$stmt, 'bind_param'], $bindRef);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Función para obtener un solo registro
function fetchOne($sql, $params = []) {
    $result = executeQuery($sql, $params);
    return $result->fetch_assoc();
}

// Función para obtener múltiples registros
function fetchAll($sql, $params = []) {
    $result = executeQuery($sql, $params);
    $rows = [];
    
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    
    return $rows;
}

// Función para insertar registros
function insert($table, $data) {
    $conn = getConnection();
    
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $conn->prepare($sql);
    
    $types = '';
    $values = [];
    
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } elseif (is_string($value)) {
            $types .= 's';
        } else {
            $types .= 'b';
        }
        $values[] = $value;
    }
    
    $bindValues = array_merge([$types], $values);
    $bindRef = [];
    
    for ($i = 0; $i < count($bindValues); $i++) {
        $bindRef[$i] = &$bindValues[$i];
    }
    
    call_user_func_array([$stmt, 'bind_param'], $bindRef);
    $stmt->execute();
    
    $insertId = $stmt->insert_id;
    
    $stmt->close();
    $conn->close();
    
    return $insertId;
}

// Función para actualizar registros
function update($table, $data, $condition, $conditionParams = []) {
    $conn = getConnection();
    
    $setClause = [];
    foreach (array_keys($data) as $column) {
        $setClause[] = "$column = ?";
    }
    $setClause = implode(', ', $setClause);
    
    $sql = "UPDATE $table SET $setClause WHERE $condition";
    $stmt = $conn->prepare($sql);
    
    $types = '';
    $values = [];
    
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } elseif (is_string($value)) {
            $types .= 's';
        } else {
            $types .= 'b';
        }
        $values[] = $value;
    }
    
    foreach ($conditionParams as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } elseif (is_string($value)) {
            $types .= 's';
        } else {
            $types .= 'b';
        }
        $values[] = $value;
    }
    
    $bindValues = array_merge([$types], $values);
    $bindRef = [];
    
    for ($i = 0; $i < count($bindValues); $i++) {
        $bindRef[$i] = &$bindValues[$i];
    }
    
    call_user_func_array([$stmt, 'bind_param'], $bindRef);
    $stmt->execute();
    
    $affectedRows = $stmt->affected_rows;
    
    $stmt->close();
    $conn->close();
    
    return $affectedRows;
}

// Función para eliminar registros
function delete($table, $condition, $params = []) {
    $conn = getConnection();
    
    $sql = "DELETE FROM $table WHERE $condition";
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $types = '';
        $bindParams = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $bindParams[] = $param;
        }
        
        $bindValues = array_merge([$types], $bindParams);
        $bindRef = [];
        
        for ($i = 0; $i < count($bindValues); $i++) {
            $bindRef[$i] = &$bindValues[$i];
        }
        
        call_user_func_array([$stmt, 'bind_param'], $bindRef);
    }
    
    $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    
    $stmt->close();
    $conn->close();
    
    return $affectedRows;
}
