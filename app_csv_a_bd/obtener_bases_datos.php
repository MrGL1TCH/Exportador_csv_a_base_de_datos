<?php
require_once 'configbd.php';
// Conectar a MySQL sin especificar una base de datos inicialmente
$con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Verificar si la conexión fue exitosa
if ($con->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $con->connect_error]));
}

// Obtener todas las bases de datos disponibles
$result = $con->query("SHOW DATABASES");
$databases = [];

// Recorrer los resultados y almacenar los nombres de las bases de datos en un array
while ($row = $result->fetch_array()) {
    $databases[] = $row[0];
}

// Devolver el array de bases de datos en formato JSON
echo json_encode($databases);
?>
