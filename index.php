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
// Este bloque ha sido corregido: se valida/castea el id, se usa sentencia preparada y se codifica la salida para mitigar SQLi y XSS.
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL

    // Validar que el id contiene sólo dígitos (evita inyección y coerción de tipo)
    if (!ctype_digit($id)) {
        echo "Parámetro inválido.";
    } else {
        // Usar prepared statement para evitar SQL Injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        $int_id = (int)$id;
        $stmt->bind_param("i", $int_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Codificar la salida para prevenir XSS
                echo "id: " . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8') . "<br>";
            }
        } else {
            echo "0 resultados";
        }
        $stmt->close();
    }
}


// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>$mensaje</div>"; // Vulnerable a XSS
}

// Cerrar conexión
$conn->close();
?>
