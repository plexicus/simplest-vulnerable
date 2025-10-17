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
// El siguiente código es vulnerable a SQL Injection ya que el input del usuario se concatena directamente en la consulta SQL sin validación o sanitización.
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL
    $id = intval($_GET['id']); // Validar el input como entero
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id); // Enlazar el parámetro id como entero
    $stmt->execute();
    $result = $stmt->get_result();
        }
    } else {
        echo "0 resultados";
    }
}

// Vulnerabilidad de Cross-Site Scripting (XSS)
// El siguiente código ha sido corregido para evitar XSS mediante sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = htmlspecialchars($_GET['mensaje'], ENT_QUOTES, 'UTF-8'); // Sanitizar el input del usuario
    echo "<div>$mensaje</div>"; // Output seguro
}

// Cerrar conexión
$conn->close();
?>
