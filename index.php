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

// Vulnerabilidad de SQL Injection
// Se reemplazó el código vulnerable por una consulta preparada y validación de entrada.
if(isset($_GET['id'])) {
    // Validar y castear el id a entero (allowlist de números)
    $id = intval($_GET['id']);

    // Usar statement preparado para evitar SQL Injection
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Escapar salida para prevenir XSS
                $safe_id = htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8');
                $safe_nombre = htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8');
                echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
            }
        } else {
            echo "0 resultados";
        }

        $stmt->close();
    } else {
        // Manejo de error de preparación (no revelar detalles sensibles)
        error_log("Falló la preparación de la consulta: " . $conn->error);
        echo "Ocurrió un error al procesar la solicitud.";
    }
}

// Vulnerabilidad de Cross-Site Scripting (XSS)
// Se añadió sanitización de salida para prevenir XSS al mostrar mensajes proporcionados por el usuario.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>";
}

// Cerrar conexión
$conn->close();
?>
