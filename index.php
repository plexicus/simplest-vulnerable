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
// Solución: Usar una consulta preparada para evitar SQL Injection
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL
    
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id); // "i" indica que el tipo de dato es un entero
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "id: " . htmlspecialchars($row["id"]). " - Nombre: " . htmlspecialchars($row["nombre"]). "<br>";
        }
    } else {
        echo "0 resultados";
    }
    $stmt->close();
}

// Vulnerabilidad de Cross-Site Scripting (XSS)
// Solución: Sanitizar el input del usuario antes de imprimirlo en el HTML
if(isset($_GET['mensaje'])) {
    $mensaje = htmlspecialchars($_GET['mensaje']); // Sanitizar el input para prevenir XSS
    echo "<div>$mensaje</div>";
}

// Cerrar conexión
$conn->close();
?>
