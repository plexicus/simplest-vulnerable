<?php
// Conexi3n a la base de datos (modifica con tus propios parmetros de conexin)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contrasea";
$dbname = "tu_base_de_datos";

// Crear conexin
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexin
if ($conn->connect_error) {
    // No exponer detalles de la base de datos al usuario final
    error_log("Conexin fallida: " . $conn->connect_error);
    die("Conexin fallida. Intntalo de nuevo ms tarde.");
}

// Manejo seguro de la consulta por id usando sentencias preparadas y validacin estricta
if (isset($_GET['id'])) {
    $id_raw = $_GET['id']; // valor recibido desde la URL

    // Validar que solo contenga dgitos (entero no negativo) y convertirlo
    if (!ctype_digit($id_raw)) {
        // Registrar el evento sin incluir PII en los logs
        error_log("Parmetro 'id' inválido recibido");
        echo "Parmetro 'id' inválido.";
    } else {
        $id = (int)$id_raw;
        // Enforce a sensible range for IDs (example: 1 .. 1000000)
        if ($id <= 0 || $id > 1000000) {
            error_log("Parmetro 'id' fuera de rango: $id");
            echo "Parmetro 'id' fuera de rango.";
        } else {
            // Preparar la sentencia para evitar SQL Injection
            $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
            if ($stmt === false) {
                error_log("Error al preparar la consulta: " . $conn->error);
                echo "Ocurrin un error. Intntalo de nuevo ms tarde.";
            } else {
                $stmt->bind_param("i", $id);
                $stmt->execute();

                $result = $stmt->get_result();
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Escapar cualquier salida para prevenir XSS
                        $safe_id = htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8');
                        $safe_nombre = htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8');
                        echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                    }
                } else {
                    echo "0 resultados";
                }
                $stmt->close();
            }
        }
    }
}

// Mitigar XSS: escapar la salida del parmetro 'mensaje' antes de renderizar
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    $safe_mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
    echo "<div>" . $safe_mensaje . "</div>";
}

// Cerrar conexin
$conn->close();
?>
