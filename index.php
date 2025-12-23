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
// example BIP
// Manejo seguro de la entrada 'id' usando statements preparados y validación
if (isset($_GET['id'])) {
    $id_raw = $_GET['id']; // Entrada del usuario

    // Validar que 'id' es un entero
    if (filter_var($id_raw, FILTER_VALIDATE_INT) === false) {
        // Rechazar valores no válidos
        echo "ID inválido";
    } else {
        $id = (int)$id_raw;

        // Preparar la consulta para evitar SQL Injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Manejo de error en la preparación
            error_log("Error al preparar la consulta: " . $conn->error);
            echo "Error en la consulta";
        } else {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar datos antes de imprimir para prevenir XSS
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

// Mitigar XSS al imprimir 'mensaje'
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Entrada del usuario susceptible a XSS
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>"; // Escapar antes de imprimir
}

// Cerrar conexión
$conn->close();
?>
