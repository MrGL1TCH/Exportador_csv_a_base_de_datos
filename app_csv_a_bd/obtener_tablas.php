<?php
require_once 'configbd.php';
// Verificar si se ha enviado el nombre de la base de datos
if (isset($_POST['database'])) {
    $database = $_POST['database'];
    
    // Conectar a la base de datos con MySQLi
    $con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, $database);
    
    // Verificar si la conexión fue exitosa
    if ($con->connect_error) {
        // Si hay un error de conexión, devolver un mensaje de error en formato JSON
        die(json_encode(["error" => "Conexión fallida: " . $con->connect_error]));
    }

    // Obtener las tablas de la base de datos
    $result = $con->query("SHOW TABLES");
    $tables = [];
    
    // Recorrer los resultados y almacenar los nombres de las tablas en un array
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    // Devolver el array de tablas en formato JSON
    echo json_encode($tables);
}
