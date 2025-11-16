 <?php

// --- Datos de tu "Cocina" (XAMPP) ---
$servidor = "localhost:3307"; 
$usuario = "root";
$contrasena = "";
$base_de_datos = "gestion_empleados";

// --- Crear la conexión ---
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_de_datos);

// --- Verificar la conexión ---
if ($conexion->connect_error) {
    die("Conexión fallida. Error: " . $conexion->connect_error);
}

// Opcional: Configurar para que acepte tildes y eñes
$conexion->set_charset("utf8");

?>