<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/conexion.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Crear un log de errores
ini_set('error_log', __DIR__ . '/error.log');

function debug_log($msg, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $msg;
    if ($data) {
        $log .= " - " . print_r($data, true);
    }
    $log .= "\n";
    file_put_contents(__DIR__ . '/debug.log', $log, FILE_APPEND);
}



function insertarGuia($data) {
    global $conexion;

    // Verificar si la guía ya existe
    $sql_check = "SELECT id, estado FROM guias WHERE numero_guia = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $data['numero_guia']);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    $valor = $data['valor_cobro'] ?? 0;
    $paquetes = isset($data['numero_paquetes']) ? intval($data['numero_paquetes']) : 1;
    
    if ($result->num_rows > 0) {
        // LA GUÍA YA EXISTE - Actualizar TODO incluyendo estado
        $guia = $result->fetch_assoc();
        
        $sql_update = "UPDATE guias 
                       SET fecha_creacion = CURRENT_TIMESTAMP(),
                           destinatario = ?,
                           descripcion = ?,
                           tipo_pago = ?,
                           valor_cobro = ?,
                           numero_paquetes = ?,
                           estado = ?,
                           sucursal = ?
                       WHERE id = ?";
        
        $stmt_update = $conexion->prepare($sql_update);
        
        $stmt_update->bind_param(
            "sssdiisi", 
            $data['destinatario'],
            $data['descripcion'],
            $data['tipo_pago'],
            $valor,
            $paquetes,
            $data['estado'],      // <-- ESTADO DEL FORMULARIO
            $data['sucursal'],
            $guia['id']
        );
        
        $resultado = $stmt_update->execute();
        
        if ($resultado) {
            debug_log("Guía existente actualizada: " . $data['numero_guia'] . " - Estado: " . $data['estado']);
        }
        
        return $resultado;
        
    } else {
        // GUÍA NUEVA - Insertar con el estado del formulario
        $sql_insert = "INSERT INTO guias 
            (sucursal, numero_guia, destinatario, descripcion, tipo_pago, valor_cobro, estado, numero_paquetes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_insert = $conexion->prepare($sql_insert);
        
        if (!$stmt_insert) {
            debug_log("Error en prepare: " . $conexion->error);
            return false;
        }

        $stmt_insert->bind_param(
            "sisssdsi",  // string, int, string, string, string, double, string, int
            $data['sucursal'],
            $data['numero_guia'],
            $data['destinatario'],
            $data['descripcion'],
            $data['tipo_pago'],
            $valor,
            $data['estado'],      // <-- ESTADO DEL FORMULARIO
            $paquetes
        );

        $resultado = $stmt_insert->execute();
        
        if (!$resultado) {
            debug_log("Error en execute: " . $stmt_insert->error);
        } else {
            debug_log("Nueva guía insertada: " . $data['numero_guia'] . " - Estado: " . $data['estado']);
        }
        
        return $resultado;
    }
}


function actualizarGuia($data, $files) {
    global $conexion;
    
    $sql_select = "SELECT * FROM guias WHERE numero_guia = ?";
    $stmt_select = $conexion->prepare($sql_select);
    $stmt_select->bind_param("i", $data['numero_guia']);
    $stmt_select->execute();
    $resultado = $stmt_select->get_result();
    $guia_actual = $resultado->fetch_assoc();
    
    if (!$guia_actual) {
        return false; // La guía no existe
    }
    
    // Construir la actualización dinámicamente
    $campos_actualizar = [];
    $params = [];
    $types = "";
    
    if (isset($data['sucursal']) && !empty($data['sucursal'])) {
        $campos_actualizar[] = "sucursal = ?";
        $params[] = $data['sucursal'];
        $types .= "s";
    }
    
    if (isset($data['estado']) && !empty($data['estado'])) {
        $campos_actualizar[] = "estado = ?";
        $params[] = $data['estado'];
        $types .= "s";
    }
    
    if (isset($data['tipo_pago']) && !empty($data['tipo_pago'])) {
        $campos_actualizar[] = "tipo_pago = ?";
        $params[] = $data['tipo_pago'];
        $types .= "s";
    }
    
    if (isset($data['valor_cobro']) && $data['valor_cobro'] !== '') {
        $tipo_pago = $data['tipo_pago'] ?? $guia_actual['tipo_pago'];
        $valor = ($tipo_pago === 'COBRO') ? floatval($data['valor_cobro']) : 0;
        $campos_actualizar[] = "valor_cobro = ?";
        $params[] = $valor;
        $types .= "d";
    }

        
    if (isset($data['numero_paquetes']) && $data['numero_paquetes'] !== '') {
        $campos_actualizar[] = "numero_paquetes = ?";
        $params[] = intval($data['numero_paquetes']);
        $types .= "i";
    }
    
    
    $voucherRuta = null;
    if (isset($files['voucher']) && $files['voucher']['error'] === 0) {
        
        
        $carpeta = __DIR__ . "/../vouchers/";
        
        
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
        
        
        $extension = pathinfo($files['voucher']['name'], PATHINFO_EXTENSION);
        $nombreArchivo = time() . "_" . uniqid() . "." . $extension;
        
        
        $rutaCompleta = $carpeta . $nombreArchivo;
        
        
        if (move_uploaded_file($files['voucher']['tmp_name'], $rutaCompleta)) {
            $voucherRuta = $nombreArchivo;
            $campos_actualizar[] = "voucher = ?";
            $params[] = $voucherRuta;
            $types .= "s";
        }
    }
    
    
    if (empty($campos_actualizar)) {
        return true;
    }
    
    
    $sql = "UPDATE guias SET " . implode(", ", $campos_actualizar) . " WHERE numero_guia = ?";
    
    
    $params[] = $data['numero_guia'];
    $types .= "i";
    
    
    $stmt = $conexion->prepare($sql);
    
    
    $stmt->bind_param($types, ...$params);
    
    
    return $stmt->execute();
}


function buscarGuias($data) {
    global $conexion;

    $sql = "SELECT * FROM guias WHERE 1=1";
    $params = [];
    $types = "";


    if (!empty($data['numero_guia'])) {
        $longitud = strlen($data['numero_guia']);
        $sql .= " AND RIGHT(CAST(numero_guia AS CHAR), ?) = ?";
        $params[] = $longitud;
        $params[] = $data['numero_guia'];
        $types .= "is";
    }

    if (!empty($data['sucursal'])) {
        $sql .= " AND sucursal = ?";
        $params[] = $data['sucursal'];
        $types .= "s";
    }

    if (!empty($data['estado'])) {
        $sql .= " AND estado = ?";
        $params[] = $data['estado'];
        $types .= "s";
    }

    if (!empty($data['tipo_pago'])) {
        $sql .= " AND tipo_pago = ?";
        $params[] = $data['tipo_pago'];
        $types .= "s";
    }

    // =============================================
    // FILTROS RÁPIDOS NUEVOS (sin afectar los anteriores)
    // =============================================
    
    // FILTRO: Guías de HOY (cualquier sucursal)
    if (!empty($data['hoy']) && $data['hoy'] == '1') {
        $sql .= " AND DATE(fecha_creacion) = CURDATE()";
    }
    
    // FILTRO: Guías de TRINIDAD de HOY
    if (!empty($data['trinidad_hoy']) && $data['trinidad_hoy'] == '1') {
        $sql .= " AND sucursal = 'TRINIDAD' AND DATE(fecha_creacion) = CURDATE()";
    }
    
    // FILTRO: Guías de SAN LUIS de HOY
    if (!empty($data['sanluis_hoy']) && $data['sanluis_hoy'] == '1') {
        $sql .= " AND sucursal = 'SAN_LUIS' AND DATE(fecha_creacion) = CURDATE()";
    }

    // =============================================
    // FILTRO DE FECHAS (rango personalizado)
    // =============================================
    if (!empty($data['fecha_desde']) && !empty($data['fecha_hasta'])) {
        $sql .= " AND fecha_creacion BETWEEN ? AND ?";
        $params[] = $data['fecha_desde'] . " 00:00:00";
        $params[] = $data['fecha_hasta'] . " 23:59:59";
        $types .= "ss";
    }

    // =============================================
    // ORDENAMIENTO
    // =============================================
    $sql .= " ORDER BY fecha_creacion DESC";

    // =============================================
    // EJECUTAR
    // =============================================
    $stmt = $conexion->prepare($sql);

    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}



function buscarGuiaAuto($numero) {
    global $conexion;

    $longitud = strlen($numero);

    $sql = "SELECT numero_guia, sucursal, destinatario, estado, tipo_pago, valor_cobro, voucher, fecha_creacion, numero_paquetes
            FROM guias
            WHERE RIGHT(CAST(numero_guia AS CHAR), ?) = ?
            ORDER BY fecha_creacion DESC
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        return ['error' => 'Error en la consulta: ' . $conexion->error];
    }
    
    $stmt->bind_param("is", $longitud, $numero);
    
    if (!$stmt->execute()) {
        return ['error' => 'Error al ejecutar: ' . $stmt->error];
    }
    
    $resultado = $stmt->get_result()->fetch_assoc();
    

    if (!$resultado) {
        return null;
    }
    

    if ($resultado['voucher']) {
        $resultado['voucher'] = basename($resultado['voucher']);
    }
    
    return $resultado;
}


function eliminarGuia($numero_guia, $sucursal) {
    global $conexion;

    $sql = "DELETE FROM guias WHERE numero_guia = ? AND sucursal = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("is", $numero_guia, $sucursal);

    return $stmt->execute();
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accion = $_POST['accion'] ?? '';

    
    
if ($accion === 'buscar_auto') {
    
    ob_clean();
    
    header('Content-Type: application/json');
    header('X-Content-Type-Options: nosniff');
    
    try {
        $guia = buscarGuiaAuto($_POST['numero_guia']);
        
       
        if ($guia && !isset($guia['error'])) {
            echo json_encode($guia, JSON_PRETTY_PRINT);
        } else if (isset($guia['error'])) {
            echo json_encode(['error' => $guia['error']]);
        } else {
            echo json_encode(null);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

    
    if ($accion === 'insertar') {
        insertarGuia($_POST);
        header("Location: ../index.php?msg=Guía guardada");
        exit;
    }



if ($accion === 'actualizar') {
    if (actualizarGuia($_POST, $_FILES)) {
        header("Location: ../index.php?msg=Guía actualizada correctamente");
    } else {
        header("Location: ../index.php?msg=Error al actualizar");
    }
    exit;
}

  
    if ($accion === 'eliminar') {
        eliminarGuia($_POST['numero_guia'], $_POST['sucursal']);
        header("Location: ../index.php?msg=Guía eliminada");
        exit;
    }

   
   /* BUSCAR / EXPORTAR */
if ($accion === 'buscar') {

    // Obtener resultados UNA SOLA VEZ
    $resultadosBusqueda = buscarGuias($_POST);

    // Si hay error o no hay resultados, inicializar como array vacío
    if (!$resultadosBusqueda) {
        $resultadosBusqueda = [];
    }

    // EXPORTAR CSV
    if (isset($_POST['exportar'])) {

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="guias.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Guía','Sucursal','Destinatario','Estado','Pago','Valor','Paquetes','Fecha']);

        foreach ($resultadosBusqueda as $g) {
            fputcsv($out, [
                $g['numero_guia'],
                $g['sucursal'],
                $g['destinatario'],
                $g['estado'],
                $g['tipo_pago'],
                $g['valor_cobro'],
                $g['numero_paquetes'] ?? 1,
                $g['fecha_creacion']
            ]);
        }

        fclose($out);
        exit;
    }

    // CALCULAR TOTALES
    $totalActivo = 0;
    $totalCancelado = 0;
    $totalPaquetes = 0;

    foreach ($resultadosBusqueda as $g) {
        // Sumar paquetes
        $paquetes = isset($g['numero_paquetes']) ? intval($g['numero_paquetes']) : 1;
        $totalPaquetes += $paquetes;
        
        // Totales monetarios
        if ($g['estado'] === 'ACTIVA') {
            $totalActivo += $g['valor_cobro'];
        } else if ($g['estado'] === 'CANCELADA' || $g['estado'] === 'DEVOLUCION') {
            $totalCancelado += $g['valor_cobro'];
        }
    }

    // GUARDAR EN SESIÓN
    $_SESSION['resultados'] = $resultadosBusqueda;
    $_SESSION['resumen'] = [
        'cantidad' => count($resultadosBusqueda),
        'valor_activo' => $totalActivo,
        'valor_cancelado' => $totalCancelado,
        'total_paquetes' => $totalPaquetes
    ];

    header("Location: ../index.php");
    exit;

        }
}
?>