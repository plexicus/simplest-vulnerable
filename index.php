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

// DEFION

// Mitigación de SQL Injection: uso de sentencias preparadas y validación del input
if (isset($_GET['id'])) {
    $id_raw = $_GET['id']; // Input del usuario tomado desde la URL

    // Validar que 'id' sea un entero positivo (solo dígitos)
    if (!ctype_digit($id_raw)) {
        // No devolver detalles de error del sistema; mensaje controlado para el usuario
        echo "ID inválido";
    } else {
        $id = (int)$id_raw;

        // Preparar la consulta para evitar inyección SQL
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Manejo de error de preparación
            error_log("Error preparando la consulta: " . $conn->error);
            echo "Error interno";
        } else {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar salida para mitigar XSS
                    $safe_id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                    $safe_nombre = htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        }
    }
}

// Mitigación de Cross-Site Scripting (XSS) para el parámetro 'mensaje'
if (isset($_GET['mensaje'])) {
    $mensaje_raw = $_GET['mensaje'];
    // Limitar longitud y escapar antes de imprimir
    $mensaje_safe = htmlspecialchars(substr($mensaje_raw, 0, 1000), ENT_QUOTES, 'UTF-8');
    echo "<div>" . $mensaje_safe . "</div>";
}

// Cerrar conexión
$conn->close();
?>
