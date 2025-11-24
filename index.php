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

// mapfre

// Mitigación de SQL Injection: usar sentencias preparadas y validación de tipo
if (isset($_GET['id'])) {
    // Validar que el id sea un entero
    $id = $_GET['id'];
    if (filter_var($id, FILTER_VALIDATE_INT) === false) {
        // ID inválido -> no procesar la consulta
        echo "ID inválido";
    } else {
        // Convertir a entero para mayor seguridad
        $id = (int)$id;

        // Preparar la consulta evitando concatenación directa
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Manejo de error en preparación de la consulta
            error_log('Preparación de statement fallida: ' . $conn->error);
            echo "Error en la consulta";
        } else {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar la salida para prevenir XSS
                    $safe_nombre = htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8');
                    echo "id: " . $row["id"] . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        }
    }
}

// Mitigación de XSS: escapar cualquier contenido impreso en HTML
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>";
}

// Cerrar conexión
$conn->close();
?>
