<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña2";
$dbname = "tu_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejo seguro de parámetro 'id' usando sentencia preparada para evitar SQL Injection
if (isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado desde la URL

    // Validación estricta: aceptar solo enteros
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        echo "ID inválido";
    } else {
        // Preparar la consulta usando parámetros
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            // Vincular el parámetro como entero
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Obtener el resultado de manera segura
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar la salida para prevenir XSS
                    echo "id: " . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8') . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // No exponer errores de SQL al usuario; registrar para depuración
            error_log("Prepare failed: " . $conn->error);
            echo "Error al procesar la solicitud";
        }
    }
}

// Manejo seguro de parámetro 'mensaje' para prevenir XSS
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    // Escapar y limitar la longitud para evitar abusos
    $safe_mensaje = htmlspecialchars(substr($mensaje, 0, 1024), ENT_QUOTES, 'UTF-8');
    echo "<div>$safe_mensaje</div>";
}

// Cerrar conexión
$conn->close();
?>
