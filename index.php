<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

// AIZOON
// Nota: la conexión a la base de datos se realiza únicamente cuando es necesaria.
// Esto facilita pruebas locales que no tienen una BD disponible y reduce la
// ventana en la que una conexión abierta existe.

// --- Manejo seguro de la entrada y consultas parametrizadas ---
// Validar y sanear el parámetro 'id' antes de usarlo en una consulta.
// Se espera que 'id' sea un entero. Usamos filter_var para validar y luego
// aplicamos una sentencia preparada para evitar SQL Injection.
if (isset($_GET['id'])) {
    $id_raw = $_GET['id']; // entrada del usuario

    // Validar que sea un entero
    if (filter_var($id_raw, FILTER_VALIDATE_INT) === false) {
        // Entrada inválida: no procesar la consulta con datos no validados
        echo "ID inválido.";
    } else {
        $id = (int)$id_raw; // casteo seguro a entero

        // Crear conexión solo cuando es estrictamente necesario
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Preparar la consulta utilizando parámetros
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Error en preparación de la consulta
            error_log("Error preparando la consulta: " . $conn->error);
            echo "Error en la consulta.";
        } else {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Obtener resultado de forma segura
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar salida para prevenir XSS
                    echo "id: " . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8') . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        }

        // Cerrar conexión cuando ya no es necesaria
        $conn->close();
    }
}

// Manejo seguro de mensaje para evitar XSS
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // entrada del usuario
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>";
}
?>
