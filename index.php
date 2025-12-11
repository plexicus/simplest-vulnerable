<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
// mi personalizacuin
if ($conn->connect_error) {
    // No exponer detalles de la conexión al usuario. Registrar el error y mostrar un mensaje genérico.
    error_log("Conexión fallida: " . $conn->connect_error);
    die("Error de conexión a la base de datos.");
}

// Manejo seguro de entrada para evitar SQL Injection
if (isset($_GET['id'])) {
    $id_raw = $_GET['id']; // Entrada del usuario

    // Validar que el id sea numérico (whitelist): solo dígitos permitidos
    if (!preg_match('/^\d+$/', $id_raw)) {
        // Entrada no válida
        echo "ID inválido";
    } else {
        $id = (int)$id_raw;

        // Preparar la consulta usando sentencias preparadas y enlazado de parámetros
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar la salida para prevenir XSS también en datos provenientes de la BD
                    $safe_id = htmlspecialchars($row['id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $safe_nombre = htmlspecialchars($row['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // Registrar el error para auditoría sin exponer detalles al usuario
            error_log("Prepare failed: " . $conn->error);
            echo "Ocurrió un error al procesar la solicitud.";
        }
    }
}

// Evitar XSS en parámetros de texto simples: escapar antes de imprimir
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</div>";
}

// Cerrar conexión
$conn->close();
?>
