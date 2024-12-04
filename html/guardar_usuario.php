<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "TeamTactics";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Verificar si los datos están definidos
    $id_usuario = isset($_POST["id_usuario"]) ? $_POST["id_usuario"] : null;
    $nombre = isset($_POST["nombre"]) ? $conn->real_escape_string($_POST["nombre"]) : null;
    $apellidos = isset($_POST["apellidos"]) ? $conn->real_escape_string($_POST["apellidos"]) : null;
    $fecha_nacimiento = isset($_POST["fecha_nacimiento"]) ? $conn->real_escape_string($_POST["fecha_nacimiento"]) : null;
    $email = isset($_POST["email"]) ? $_POST["email"] : null;

    // Validar campos obligatorios
    if (empty($nombre) || empty($apellidos) || empty($fecha_nacimiento)) {
        echo "Por favor, completa todos los campos obligatorios.";
        exit();
    }

    // Manejo de la imagen de perfil (opcional)
    if (isset($_FILES["foto_perfil"]) && $_FILES["foto_perfil"]["error"] === UPLOAD_ERR_OK) {
        $foto = addslashes(file_get_contents($_FILES["foto_perfil"]["tmp_name"]));
        // Si se sube foto, se incluirá en la consulta
        $sql_update = "UPDATE Usuarios SET Nombre = ?, Apellidos = ?, Fecha_nacimiento = ?, Foto_perfil = ? WHERE ID_usuario = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssi", $nombre, $apellidos, $fecha_nacimiento, $foto, $id_usuario);
    } else {
        // Si no se sube foto, no se incluye en la consulta
        $sql_update = "UPDATE Usuarios SET Nombre = ?, Apellidos = ?, Fecha_nacimiento = ? WHERE ID_usuario = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $nombre, $apellidos, $fecha_nacimiento, $id_usuario);
    }

    // Ejecutar la consulta
    if ($stmt_update->execute()) {
        echo "Datos actualizados correctamente.";
    } else {
        echo "Error: " . $stmt_update->error;
    }

    // Cerrar la conexión
    $stmt_update->close();
    $conn->close();
}
?>

