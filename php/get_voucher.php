<?php


// Configure errors
ini_set('display_errors', 0);
error_reporting(0);

// ===== LOGS =====
$log_file = __DIR__ . '/voucher_debug.log';

function log_debug($msg) {
    global $log_file;
    $fecha = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$fecha] $msg\n", FILE_APPEND);
}

log_debug("=== get_voucher.php INICIADO ===");
log_debug("GET: " . print_r($_GET, true));

// ===== voucher =====
$voucher = isset($_GET['voucher']) ? $_GET['voucher'] : '';

if (empty($voucher)) {
    log_debug("ERROR: voucher vacío");
    http_response_code(404);
    exit;
}

$voucher_original = $voucher;
$voucher = basename($voucher);
log_debug("Voucher original: $voucher_original");
log_debug("Voucher limpio: $voucher");

$ruta_archivo = __DIR__ . '/../vouchers/' . $voucher;
log_debug("Ruta archivo: $ruta_archivo");

if (!file_exists($ruta_archivo)) {
    log_debug("ERROR: Archivo NO existe");
    http_response_code(404);
    echo "Archivo no encontrado";
    exit;
}

log_debug("Archivo ENCONTRADO");
log_debug("Tamaño: " . filesize($ruta_archivo));

// ===== MIME =====
$extension = strtolower(pathinfo($ruta_archivo, PATHINFO_EXTENSION));
$mime_types = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'bmp' => 'image/bmp'
];

$mime_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';

if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $finfo_mime = finfo_file($finfo, $ruta_archivo);
    finfo_close($finfo);
    if ($finfo_mime) {
        $mime_type = $finfo_mime;
    }
}

log_debug("MIME type: $mime_type");

header('Content-Type: ' . $mime_type);
header('Content-Length: ' . filesize($ruta_archivo));
header('Cache-Control: public, max-age=86400'); // 24h
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
header('Pragma: public');
header('X-Content-Type-Options: nosniff'); // for Firefox

readfile($ruta_archivo);
log_debug("=== get_voucher.php COMPLETADO ===");
exit;
?>