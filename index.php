<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contrasea";
$dbname = "tu_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
//ejemplo
// Mitigación de SQL Injection
// Se valida explícitamente que 'id' es un entero y se utiliza una consulta preparada.
if (isset($_GET['id'])) {
    $id_raw = $_GET['id']; // Input del usuario tomado desde la URL

    // Validación: debe ser un entero (opcionalmente podrías comprobar un rango)
    $id = filter_var($id_raw, FILTER_VALIDATE_INT);
    if ($id === false) {
        // Id inválido: no procesar la consulta
        echo "Identificador inválido.";
    } else {
        // Preparar la consulta para evitar inyección SQL
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Error al preparar la consulta
            error_log("Error al preparar la consulta: " . $conn->error);
            echo "Error en el servidor.";
        } else {
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Obtener resultados (usar get_result si está disponible)
            $result = null;
            if (method_exists($stmt, 'get_result')) {
                $result = $stmt->get_result();
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Escapar salida para prevenir XSS
                        $safe_id = htmlspecialchars($row['id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $safe_nombre = htmlspecialchars($row['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                    }
                } else {
                    echo "0 resultados";
                }
            } else {
                // Fallback si get_result no está disponible: usar bind_result
                $stmt->bind_result($col_id, $col_nombre);
                $fetched = false;
                while ($stmt->fetch()) {
                    $fetched = true;
                    $safe_id = htmlspecialchars($col_id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $safe_nombre = htmlspecialchars($col_nombre, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
                if (!$fetched) {
                    echo "0 resultados";
                }
            }

            $stmt->close();
        }
    }
}

// Mitigación de Cross-Site Scripting (XSS)
// Sanitizar/escapar cualquier contenido que se refleje en HTML
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    // Escapar antes de imprimir
    $safe_mensaje = htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo "<div>" . $safe_mensaje . "</div>";
}

// Cerrar conexión
$conn->close();
?>
