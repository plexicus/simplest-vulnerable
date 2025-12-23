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

// ejemplo Julian

// Corrección: Uso de sentencias preparadas para evitar SQL Injection
if (isset($_GET['id'])) {
    // Validar y sanitizar el parámetro 'id'
    $id_raw = $_GET['id'];
    $id_valid = filter_var($id_raw, FILTER_VALIDATE_INT);

    if ($id_valid === false) {
        // El id no es un entero válido
        echo "ID inválido";
    } else {
        $id = (int) $id_valid;

        // Preparar la sentencia para evitar inyección SQL
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Error en la preparación
            error_log("Fallo al preparar la consulta: " . $conn->error);
            echo "Error en la consulta";
        } else {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Obtener resultados usando bind_result para mayor compatibilidad
            $stmt->bind_result($res_id, $res_nombre);

            $hasResults = false;
            while ($stmt->fetch()) {
                $hasResults = true;
                // Escapar la salida para prevenir XSS
                $safe_nombre = htmlspecialchars($res_nombre, ENT_QUOTES, 'UTF-8');
                $safe_id = htmlspecialchars((string)$res_id, ENT_QUOTES, 'UTF-8');
                echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
            }

            if (! $hasResults) {
                echo "0 resultados";
            }

            $stmt->close();
        }
    }
}

// Corregir vulnerabilidad XSS al mostrar 'mensaje'
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    $safe_mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
    echo "<div>" . $safe_mensaje . "</div>";
}

// Cerrar conexión
$conn->close();
?>
