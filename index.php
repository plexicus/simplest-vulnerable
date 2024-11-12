<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?"); // Usar declaraciones preparadas para prevenir SQL Injection
    $stmt->bind_param("i", $id); // "i" indica que el parámetro es un entero
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "id: " . htmlspecialchars($row["id"]) . " - Nombre: " . htmlspecialchars($row["nombre"]) . "<br>"; // Usar htmlspecialchars para prevenir XSS
        }
    } else {
        echo "0 resultados";
    }
    $stmt->close();
}
// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>$mensaje</div>"; // Vulnerable a XSS
}

// Cerrar conexión
$conn->close();
?>
