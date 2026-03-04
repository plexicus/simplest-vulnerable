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
     // Tomar y validar el parámetro 'id' como entero
     $raw_id = $_GET['id']; // Input del usuario tomado directamente desde la URL
     $id = filter_var($raw_id, FILTER_VALIDATE_INT);
     if ($id === false) {
         // Parámetro no válido: no ejecutar la consulta y devolver un mensaje genérico
         echo "Parámetro 'id' inválido";
     } else {
         // Uso de prepared statement para evitar SQL Injection
         $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
         if ($stmt) {
             $stmt->bind_param("i", $id);
             $stmt->execute();
             $result = $stmt->get_result();

             if ($result && $result->num_rows > 0) {
                 while($row = $result->fetch_assoc()) {
                     echo "id: " . $row["id"]. " - Nombre: " . $row["nombre"]. "<br>";
                 }
             } else {
                 echo "0 resultados";
             }

             $stmt->close();
         } else {
             // No exponer detalles internos en caso de error
             echo "Error en la consulta.";
         }
     }
 }

// Vulnerabilidad de Cross-Site Scripting (XSS)
// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.

    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>$mensaje</div>"; // Vulnerable a XSS
}

// Cerrar conexión
$conn->close();
?>
