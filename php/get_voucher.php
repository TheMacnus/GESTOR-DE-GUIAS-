<?php
/**
 * get_voucher.php
 * Sirve imágenes de vouchers de manera segura y compatible con todos los navegadores
 */

// Configurar errores
ini_set('display_errors', 0);
error_reporting(0);

// Obtener el nombre del voucher
$voucher = isset($_GET['voucher']) ? $_GET['voucher'] : '';
$voucher = basename($voucher); // Eliminar rutas

if (empty($voucher)) {
    http_response_code(404);
    exit;
}

// Ruta completa al archivo
$ruta_archivo = __DIR__ . '/../vouchers/' . $voucher;

// Verificar que el archivo existe
if (!file_exists($ruta_archivo)) {
    http_response_code(404);
    exit;
}

// Obtener el tipo MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $ruta_archivo);
finfo_close($finfo);

// Si no se pudo determinar, usar tipo genérico
if (!$mime_type) {
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
}

// Headers para forzar caché y compatibilidad
header('Content-Type: ' . $mime_type);
header('Content-Length: ' . filesize($ruta_archivo));
header('Cache-Control: public, max-age=86400'); // Cache por 24 horas
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
header('Pragma: public');

// Para Firefox, asegurar que no hay caché problemática
header('X-Content-Type-Options: nosniff');

// Leer y enviar el archivo
readfile($ruta_archivo);
exit;
?>