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
// Resolviendo la vulnerabilidad de SQL Injection
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Validar que el ID sea un entero positivo
    if (filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        // Usar una declaración preparada para evitar SQL Injection
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "id: " . htmlspecialchars($row["id"]) . " - Nombre: " . htmlspecialchars($row["nombre"]) . "<br>";
            }
        }

        $stmt->close();
    } else {
        echo "ID inválido.";
    }
}
        }
    } else {
        echo "0 resultados";
    }
}

// Vulnerabilidad de Cross-Site Scripting (XSS)
// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>$mensaje</div>"; // Vulnerable a XSS
}

// Cerrar conexión
$conn->close();
?>
