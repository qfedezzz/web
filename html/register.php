<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";  // Cambia esto si tienes un usuario distinto para la base de datos
$password = "";      // Cambia esto si tienes una contraseña configurada
$dbname = "TeamTactics"; // Asegúrate de que la base de datos esté creada

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("La conexión ha fallado: " . $conn->connect_error);
}

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $contraseña = $_POST['contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];
    $fecha_registro = date('Y-m-d'); // Fecha de registro actual

    // Verificar que las contraseñas coinciden
    if ($contraseña != $confirmar_contraseña) {
        echo "Las contraseñas no coinciden. Por favor, intenta de nuevo.";
        exit();
    }

    // Encriptar la contraseña
    $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

    // Verificar si el email ya está registrado
    $sql_check_email = "SELECT * FROM Usuarios WHERE Email = '$email'";
    $result = $conn->query($sql_check_email);

    if ($result->num_rows > 0) {
        echo "El email ya está registrado. Por favor, utiliza otro.";
        exit();
    }

    // Insertar el nuevo usuario en la base de datos
    $sql = "INSERT INTO Usuarios (Nombre, Email, Contraseña, Fecha_registro) 
            VALUES ('$usuario', '$email', '$contraseña_encriptada', '$fecha_registro')";

    if ($conn->query($sql) === TRUE) {
        echo "¡Registro exitoso!";
        // Redirigir al login después de un registro exitoso
        header("Location: login.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
