<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "coordinadoraDB"; // CAMBIA si tu BD tiene otro nombre

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
