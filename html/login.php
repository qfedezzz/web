<?php
session_start();

// Configuración de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "TeamTactics";

// Conectar a la base de datos
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email_login = $_POST["email_login"];
    $contraseña_login = $_POST["contraseña_login"];

    // Consulta SQL usando un marcador de posición
    $sql = "SELECT * FROM Usuarios WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email_login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();

        // Verificar la contraseña encriptada con password_verify
            if (password_verify($contraseña_login, $user_data['Contraseña'])) {
                // Si la contraseña es correcta, iniciar sesión
                $_SESSION["ID_usuario"] = $user_data["ID_usuario"];
                $_SESSION["Nombre"] = $user_data["Nombre"];
                $_SESSION["Email"] = $user_data["Email"];
                $_SESSION["Fecha_registro"] = $user_data["Fecha_registro"];

                echo "Inicio de sesión exitoso.";
                // Redirigir al usuario a la página de inicio
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Correo electrónico o contraseña incorrectos.";
            }
    } else {
        echo "Correo electrónico o contraseña incorrectos.";
    }

    $stmt->close();
}

$conn->close();
?>
