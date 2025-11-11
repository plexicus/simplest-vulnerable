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

// Acceso a datos seguro usando prepared statements y validación de entrada
// Plexicus & Irvine 

if (isset($_GET['id'])) {
    $id_raw = $_GET['id']; // Input del usuario tomado desde la URL

    // Validación estricta: requerimos que el id sea un entero positivo
    if (!ctype_digit($id_raw)) {
        // Rechazar entradas no numéricas
        echo "ID inválido";
    } else {
        $id = (int) $id_raw;

        // Preparar la consulta con placeholder
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Manejo de error de preparación
            error_log("Fallo al preparar la consulta: " . $conn->error);
            echo "Error interno";
        } else {
            // Vincular parámetros y ejecutar
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Usar bind_result / fetch para compatibilidad
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($row_id, $row_nombre);
                while ($stmt->fetch()) {
                    // Escapar la salida para evitar XSS en los datos de la base
                    echo "id: " . htmlspecialchars($row_id, ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($row_nombre, ENT_QUOTES, 'UTF-8') . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        }
    }
}

// Mitigación de Cross-Site Scripting (XSS) en 'mensaje'
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    // Escapar cualquier HTML antes de imprimir
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>";
}

// Cerrar conexión
$conn->close();
?>
