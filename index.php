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
    // No revelar detalles al usuario; loguear para administradores
    error_log("Conexión a BD fallida: " . $conn->connect_error);
    http_response_code(500);
    echo "Error del servidor. Intente más tarde.";
    exit;
}

// Función de acceso a datos: obtener usuario por id usando prepared statements
function get_user_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }

    // 'i' indica entero
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        return false;
    }

    $result = $stmt->get_result();
    if ($result === false) {
        error_log("get_result failed: " . $stmt->error);
        $stmt->close();
        return false;
    }

    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

// Manejo seguro del parámetro 'id'
if (isset($_GET['id'])) {
    $id_raw = $_GET['id'];

    // Validación estricta: convertir/validar a entero
    $id = filter_var($id_raw, FILTER_VALIDATE_INT);
    if ($id === false) {
        // Responder con mensaje genérico sin detalles sensibles
        echo "ID inválido.";
    } else {
        $users = get_user_by_id($conn, $id);
        if ($users === false) {
            echo "Error al obtener datos.";
        } else if (count($users) > 0) {
            foreach ($users as $row) {
                // Escapar salida para prevenir XSS
                echo "id: " . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8') . "<br>";
            }
        } else {
            echo "0 resultados";
        }
    }
}

// Mitigación XSS: escapar cualquier contenido mostrado
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>";
}

// Cerrar conexión
$conn->close();
?>
