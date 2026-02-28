<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/conexion.php";

// Al inicio del archivo, después de require_once
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Crear un log de errores
ini_set('error_log', __DIR__ . '/error.log');

// Función para depurar
function debug_log($msg, $data = null) {
    $log = date('Y-m-d H:i:s') . " - " . $msg;
    if ($data) {
        $log .= " - " . print_r($data, true);
    }
    $log .= "\n";
    file_put_contents(__DIR__ . '/debug.log', $log, FILE_APPEND);
}


/* =========================
   INSERTAR GUÍA
========================= */
function insertarGuia($data) {
    global $conexion;

    $sql = "INSERT INTO guias 
        (sucursal, numero_guia, destinatario, descripcion, tipo_pago, valor_cobro, estado, numero_paquetes)
        VALUES (?, ?, ?, ?, ?, ?, 'ACTIVA', ?)";

    $stmt = $conexion->prepare($sql);
    
    if (!$stmt) {
        debug_log("Error en prepare: " . $conexion->error);
        return false;
    }

    $valor = $data['valor_cobro'] ?? 0;
    $paquetes = isset($data['numero_paquetes']) ? intval($data['numero_paquetes']) : 1;

    $stmt->bind_param(
        "sisssdi",  // string, int, string, string, string, double, int
        $data['sucursal'],
        $data['numero_guia'],
        $data['destinatario'],
        $data['descripcion'],
        $data['tipo_pago'],
        $valor,
        $paquetes
    );

    $resultado = $stmt->execute();
    
    if (!$resultado) {
        debug_log("Error en execute: " . $stmt->error);
    }
    
    return $resultado;
}

/* =========================
   ACTUALIZAR GUÍA COMPLETA (con voucher) - ACTUALIZACIÓN PARCIAL
========================= */
function actualizarGuia($data, $files) {
    global $conexion;
    
    // Primero obtener los datos actuales de la guía
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
    
    // Sucursal (solo si se envió y es válida)
    if (isset($data['sucursal']) && !empty($data['sucursal'])) {
        $campos_actualizar[] = "sucursal = ?";
        $params[] = $data['sucursal'];
        $types .= "s";
    }
    
    // Estado (solo si se envió y es válido)
    if (isset($data['estado']) && !empty($data['estado'])) {
        $campos_actualizar[] = "estado = ?";
        $params[] = $data['estado'];
        $types .= "s";
    }
    
    // Tipo de pago (solo si se envió y es válido)
    if (isset($data['tipo_pago']) && !empty($data['tipo_pago'])) {
        $campos_actualizar[] = "tipo_pago = ?";
        $params[] = $data['tipo_pago'];
        $types .= "s";
    }
    
    // Valor a cobrar (solo si se envió y es válido, y si es COBRO)
    if (isset($data['valor_cobro']) && $data['valor_cobro'] !== '') {
        // Si el tipo de pago es COBRO, usar el valor enviado, si no, 0
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

    if (!empty($data['fecha_desde']) && !empty($data['fecha_hasta'])) {
        $sql .= " AND fecha_creacion BETWEEN ? AND ?";
        $params[] = $data['fecha_desde'] . " 00:00:00";
        $params[] = $data['fecha_hasta'] . " 23:59:59";
        $types .= "ss";
    }

    $sql .= " ORDER BY fecha_creacion DESC";

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