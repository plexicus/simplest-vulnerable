<?php
// Database connection parameters
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contrasena";
$dbname = "tu_base_de_datos";

// Do not display errors to users in production. Log errors.
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Ensure correct charset
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    // Generic message to user
    die("Error connecting to service. Try again later.");
}

// Secure handling of the 'id' parameter using strict validation and prepared statements
if (isset($_GET['id'])) {
    // Validate that 'id' is an integer
    $id = $_GET['id'];
    if (filter_var($id, FILTER_VALIDATE_INT) === false) {
        // Log malformed or malicious attempts
        error_log("Parameter 'id' rejected by validation: " . htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        echo "Invalid parameter.";
    } else {
        // Cast to int for extra safety
        $id = (int)$id;

        // Prepare statement to avoid SQL Injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            error_log("Error preparing statement: " . $conn->error);
            echo "Internal error.";
        } else {
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                error_log("Error executing statement: " . $stmt->error);
                echo "Internal error.";
            } else {
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Escape any data rendered into HTML to prevent XSS
                        $safe_id = htmlspecialchars($row['id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $safe_nombre = htmlspecialchars($row['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                    }
                } else {
                    echo "0 results";
                }
                $stmt->close();
            }
        }
    }
}

// Prevent XSS: sanitize the 'mensaje' parameter before printing
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $safe_mensaje = htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo "<div>" . $safe_mensaje . "</div>";
}

// Close connection
$conn->close();
?>
