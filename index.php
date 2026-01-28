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

/*
Brief analysis - ALEX:
This change fixes a SQL Injection vulnerability by using a parameterized prepared statement and strict validation of the 'id' parameter.
I will replace the concatenated SQL with a mysqli prepared statement binding the id as an integer; only index.php is modified.
*/
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL

    // Whitelist/validate: allow only digits to prevent injection
    if (!preg_match('/^\d+$/', $id)) {
        echo "Invalid id";
    } else {
        $id_int = (int)$id;

        // Use a prepared statement to avoid SQL injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_int);
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
            // Do not execute an unsafe query if prepare() failed
            echo "Query preparation failed";
        }
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
