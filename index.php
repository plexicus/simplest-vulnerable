<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Mitigación: uso de prepared statements y validación de entrada
if(isset($_GET['id'])) {
    $id_raw = $_GET['id'];
    // Validar que el id es un entero
    if (filter_var($id_raw, FILTER_VALIDATE_INT) === false) {
        echo "ID inválido";
    } else {
        $id = (int)$id_raw;
        // Preparar consulta con parámetros y seleccionar columnas específicas
        if ($stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?")) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($res_id, $res_nombre);
            $found = false;
            while ($stmt->fetch()) {
                $found = true;
                // Escapar salida para prevenir XSS
                echo "id: " . htmlspecialchars($res_id, ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($res_nombre, ENT_QUOTES, 'UTF-8') . "<br>";
            }
            if (!$found) {
                echo "0 resultados";
            }
            $stmt->close();
        } else {
            // No revelar detalles internos al usuario
            echo "Error en la consulta";
        }
    }
}

// Mitigación de Cross-Site Scripting (XSS)
// Escapar la salida del mensaje para evitar ejecución de HTML/JS
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>";
}

// Cerrar conexión
$conn->close();
?>
