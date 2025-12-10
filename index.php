<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
// demo para caixabank
if ($conn->connect_error) {
    // Log para administradores, sin filtrar detalles al usuario
    error_log("Conexión a la base de datos fallida: " . $conn->connect_error);
    http_response_code(500);
    die("Error del servidor. Por favor, inténtelo más tarde.");
}

// Asegurar conjunto de caracteres para evitar problemas de encoding
$conn->set_charset('utf8mb4');

// Uso seguro de consultas: validación de entrada + consultas preparadas
// Validar y sanear 'id' como entero
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id !== null && $id !== false) {
    // Preparar la consulta con parámetro enlazado para evitar SQL Injection
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
    if (!$stmt) {
        // No mostrar detalles del error al usuario; registrar para diagnóstico
        error_log("Error al preparar la consulta: " . $conn->error);
        echo "Error al procesar la solicitud.";
    } else {
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            echo "Error al procesar la solicitud.";
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar la salida para prevenir XSS
                    $safe_id = htmlspecialchars($row['id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $safe_nombre = htmlspecialchars($row['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }
        }
        $stmt->close();
    }
} else {
    // Si 'id' está presente pero no es válido, informar de forma segura
    if (isset($_GET['id'])) {
        echo "ID inválido.";
    }
}

// Mitigación de XSS: escapar cualquier salida que se muestre en HTML
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</div>";
}

// Cerrar conexión
$conn->close();
?>
