<?php
echo "<h2>Debug Log</h2>";
echo "<pre>";
if (file_exists('debug.log')) {
    echo file_get_contents('debug.log');
} else {
    echo "No hay archivo de log aún";
}
echo "</pre>";
?>