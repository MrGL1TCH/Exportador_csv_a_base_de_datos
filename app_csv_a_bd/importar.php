<?php
require_once 'configbd.php';

// Verificar si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si se han subido archivos
    if (isset($_FILES['archivos'])) {
        $archivos = $_FILES['archivos'];
        $database = $_POST['database'];
        $tabla = $_POST['table'] ?? '';

        // Verificar si se ha seleccionado una tabla
        if (!empty($tabla)) {
            // Conectar a la base de datos
            $con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, $database);

            // Verificar si la conexión fue exitosa
            if (!$con->connect_error) {
                // Obtener el nombre de la clave primaria de la tabla
                $queryPK = "SELECT COLUMN_NAME 
                            FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = '$database' 
                              AND TABLE_NAME = '$tabla' 
                              AND COLUMN_KEY = 'PRI'";
                
                $resultPK = $con->query($queryPK);
                
                if ($resultPK && $rowPK = $resultPK->fetch_assoc()) {
                    $primaryKey = $rowPK['COLUMN_NAME'];
                } else {
                    die("No se encontró una clave primaria para la tabla especificada.");
                }

                // Procesar cada archivo subido
                for ($i = 0; $i < count($archivos['name']); $i++) {
                    // Verificar si el archivo fue subido correctamente y es de tipo CSV
                    if ($archivos['error'][$i] == UPLOAD_ERR_OK && $archivos['type'][$i] == 'text/csv') {
                        $archivo = $archivos['tmp_name'][$i];
                        $csv = array_map('str_getcsv', file($archivo, FILE_SKIP_EMPTY_LINES));
                        $cabecera = array_shift($csv); // Obtener y eliminar la cabecera del CSV

                        // Dividir la cabecera si está en una sola celda
                        if (count($cabecera) == 1) {
                            $cabecera = str_getcsv($cabecera[0], ';', '"');
                        }

                        $columnasDB = [];
                        $result = $con->query("SHOW COLUMNS FROM $tabla");

                        // Obtener las columnas de la tabla
                        while ($row = $result->fetch_assoc()) {
                            $columnasDB[] = $row['Field'];
                        }

                        // Procesar cada fila del archivo CSV
                        foreach ($csv as $fila) {
                            // Si la fila de datos está en una sola celda, dividirla usando ";"
                            if (count($fila) == 1) {
                                $fila = str_getcsv($fila[0], ';', '"');
                            }

                            // Comprobar si el número de columnas coincide con el encabezado
                            if (count($fila) !== count($cabecera)) {
                                die("Error: la cantidad de datos en la fila no coincide con la cantidad de columnas.");
                            }

                            $datos = array_combine($cabecera, $fila);
                            $columnas = array_intersect($cabecera, $columnasDB);

                            // Verificar si existe una clave primaria en los datos y si ya está en la base de datos
                            if (isset($datos[$primaryKey]) && !empty($datos[$primaryKey])) {
                                $primaryKeyValue = $con->real_escape_string($datos[$primaryKey]);
                                $queryCheck = "SELECT COUNT(*) as count FROM $tabla WHERE $primaryKey = '$primaryKeyValue'";
                                $resultCheck = $con->query($queryCheck);
                                $rowCheck = $resultCheck->fetch_assoc();

                                // Actualizar registro existente
                                if ($rowCheck['count'] > 0) {
                                    $set = [];
                                    foreach ($columnas as $columna) {
                                        if ($columna != $primaryKey) { // No actualizar la clave primaria
                                            $set[] = "$columna='" . $con->real_escape_string($datos[$columna]) . "'";
                                        }
                                    }
                                    $query = "UPDATE $tabla SET " . implode(", ", $set) . " WHERE $primaryKey = '$primaryKeyValue'";
                                } else {
                                    // Insertar nuevo registro
                                    $valores = array_map(function($col) use ($datos, $con) {
                                        return "'" . $con->real_escape_string($datos[$col]) . "'";
                                    }, $columnas);
                                    $query = "INSERT INTO $tabla (" . implode(", ", $columnas) . ") VALUES (" . implode(", ", $valores) . ")";
                                }
                            } else {
                                // Insertar nuevo registro si no hay valor para la clave primaria
                                if (array_filter($datos)) {
                                    $valores = array_map(function($col) use ($datos, $con) {
                                        return "'" . $con->real_escape_string($datos[$col]) . "'";
                                    }, $columnas);
                                    $query = "INSERT INTO $tabla (" . implode(", ", $columnas) . ") VALUES (" . implode(", ", $valores) . ")";
                                }
                            }

                            // Ejecutar la consulta SQL y manejar posibles errores
                            if (isset($query)) {
                                if (!$con->query($query)) {
                                    http_response_code(500);
                                    die("Error al ejecutar la consulta: " . $con->error);
                                }
                            }
                        }
                    } else {
                        // Manejar errores de tipo de archivo o de carga
                        http_response_code(400);
                        die("Tipo de archivo no permitido o error en la carga.");
                    }
                }
                echo "Archivos importados con éxito.";
            } else {
                // Manejar errores de conexión
                http_response_code(500);
                die("Conexión fallida: " . $con->connect_error);
            }
        } else {
            // Manejar error si no se ha seleccionado una tabla
            http_response_code(400);
            die("Tabla no seleccionada.");
        }
    } else {
        // Manejar error si no se han adjuntado archivos
        http_response_code(400);
        die("No se han adjuntado archivos o hay un error en la carga.");
    }
} else {
    // Manejar error si el método de solicitud no es POST
    http_response_code(405);
    die("Método no permitido.");
}
