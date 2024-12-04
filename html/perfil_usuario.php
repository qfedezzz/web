<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['ID_usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "TeamTactics";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id_usuario = $_SESSION['ID_usuario'];

// Obtener la información actual del usuario
$sql = "SELECT * FROM Usuarios WHERE ID_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Procesar formulario al enviar datos
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $apellidos = $conn->real_escape_string($_POST["apellidos"]);
    $fecha_nacimiento = $conn->real_escape_string($_POST["fecha_nacimiento"]);
    $pais = $conn->real_escape_string($_POST["pais"]);
    $ciudad = $conn->real_escape_string($_POST["ciudad"]);
    $idioma = $conn->real_escape_string($_POST["idioma"]);
    $equipo_favorito = $conn->real_escape_string($_POST["equipo_favorito"]);
    $telefono = $conn->real_escape_string($_POST["telefono"]);
    $foto_perfil = null;

    // Manejar subida de imagen si se proporciona
    if (isset($_FILES["foto_perfil"]) && $_FILES["foto_perfil"]["error"] === UPLOAD_ERR_OK) {
        $foto_perfil = addslashes(file_get_contents($_FILES["foto_perfil"]["tmp_name"]));
    }

    // Crear consulta SQL con o sin imagen
    if ($foto_perfil) {
        $sql_update = "UPDATE Usuarios SET Nombre = ?, Apellidos = ?, Fecha_nacimiento = ?, Foto_perfil = ?, Pais = ?, Ciudad = ?, Idioma = ?, Equipo_favorito = ?, Telefono = ? WHERE ID_usuario = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssssssssi", $nombre, $apellidos, $fecha_nacimiento, $foto_perfil, $pais, $ciudad, $idioma, $equipo_favorito, $telefono, $id_usuario);
    } else {
        $sql_update = "UPDATE Usuarios SET Nombre = ?, Apellidos = ?, Fecha_nacimiento = ?, Pais = ?, Ciudad = ?, Idioma = ?, Equipo_favorito = ?, Telefono = ? WHERE ID_usuario = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssssssi", $nombre, $apellidos, $fecha_nacimiento, $pais, $ciudad, $idioma, $equipo_favorito, $telefono, $id_usuario);
    }

    // Ejecutar la actualización
    if ($stmt->execute()) {
        echo "¡Datos actualizados correctamente!";
    } else {
        echo "Error al actualizar los datos: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <style>
        /* Estilos generales */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #000; /* Fondo negro */
            color: #fff; /* Texto blanco */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Contenedor principal */
        .menu-usuario {
            width: 1700px; /* Más ancho */
            height: 700px; /* Alto */
            background: #1a1a1a; /* Gris oscuro */
            border-radius: 10px;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5);
        }

        /* Sección de datos básicos */
        .datos-usuario {
            width: 50%; /* 50% del contenedor para los datos básicos */
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* Formulario de información básica */
        .formulario-usuario {
            width: 100%;
            margin-top: 20px;
        }

        .formulario-usuario label {
            font-size: 1.1em;
            color: #00FF00; /* Verde */
            display: block;
            margin-bottom: 10px;
            text-align: left;
        }

        .formulario-usuario input,
        .formulario-usuario select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #333;
            border-radius: 5px;
            background: #1a1a1a; /* Gris oscuro */
            color: #fff;
            font-size: 1em;
            transition: border-color 0.3s;
            max-width: 100%; /* Limitar el alargado */
        }

        .formulario-usuario input:focus,
        .formulario-usuario select:focus {
            border-color: #00FF00; /* Verde */
            outline: none;
        }

        /* Datos adicionales */
        .datos-adicionales {
            width: 45%; /* 45% del contenedor para datos adicionales */
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }

        /* Foto de perfil */
        .foto-perfil-container {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-top: 30px; /* Subir la foto */
            position: relative;
        }

        .foto-perfil {
            width: 220px; /* Más grande */
            height: 220px;
            border-radius: 50%; /* Hacer la imagen redonda */
            background: #333; /* Gris más oscuro */
            overflow: hidden;
            border: 4px solid #00FF00; /* Borde verde */
            cursor: pointer;
            margin-bottom: 20px;
        }

        .foto-perfil img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Mantiene la proporción correcta */
        }

        .foto-perfil input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        /* Botón guardar */
        .boton-guardar {
            width: 100%;
            padding: 14px;
            background: #00FF00;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            color: #000;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .boton-guardar:hover {
            background: #fff;
            color: #000000;
            transform: scale(1.05);
        }

        /* Enlace recuperar contraseña */
        .enlace-recuperar {
            margin-top: 2px; /* 2px más abajo */
            font-size: 1em;
            color: #00FF00;
            text-decoration: none;
            text-align: center;
        }

        .enlace-recuperar:hover {
            text-decoration: underline;
        }

        /* Sección de idioma, equipo favorito, teléfono */
        .seccion-adicional {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top: 30px; /* Espaciado después de la foto */
        }

        .seccion-adicional select,
        .seccion-adicional input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #333;
            border-radius: 5px;
            background: #1a1a1a; /* Gris oscuro */
            color: #fff;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .seccion-adicional select:focus,
        .seccion-adicional input:focus {
            border-color: #00FF00;
            outline: none;
        }

        /* Botón de Home */
        .boton-home {
            width: 100%;
            padding: 14px;
            background: #1a1a1a;
            border: none;
            border-radius: 5px;
            font-size: 1.2em;
            color: #00FF00;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            margin-top: 20px;
        }

        .boton-home:hover {
            background: #00FF00;
            color: #000;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="menu-usuario">
        <div class="datos-usuario">
            <h2>Editar Perfil</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="formulario-usuario">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $usuario['Nombre']; ?>" required>
                </div>
                <div class="formulario-usuario">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" value="<?php echo $usuario['Apellidos']; ?>" required>
                </div>
                <div class="formulario-usuario">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $usuario['Fecha_nacimiento']; ?>" required>
                </div>
                <div class="formulario-usuario">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" value="<?php echo $usuario['Email']; ?>" disabled>
                </div>
                <div class="formulario-usuario">
                    <label for="pais">País</label>
                    <select id="pais" name="pais">
                        <option value="España" <?php if ($usuario['Pais'] == 'España') echo 'selected'; ?>>España</option>
                        <option value="México" <?php if ($usuario['Pais'] == 'México') echo 'selected'; ?>>México</option>
                        <option value="Argentina" <?php if ($usuario['Pais'] == 'Argentina') echo 'selected'; ?>>Argentina</option>
                        <option value="Colombia" <?php if ($usuario['Pais'] == 'Colombia') echo 'selected'; ?>>Colombia</option>
                        <option value="Chile" <?php if ($usuario['Pais'] == 'Chile') echo 'selected'; ?>>Chile</option>
                        <option value="Otro" <?php if ($usuario['Pais'] == 'Otro') echo 'selected'; ?>>Otro</option>
                    </select>
                </div>
                <div class="formulario-usuario">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" id="ciudad" name="ciudad" value="<?php echo $usuario['Ciudad']; ?>" required>
                </div>
                <div class="foto-perfil-container">
                    <div class="foto-perfil">
                        <input type="file" name="foto_perfil" accept="image/*">
                        <img src="../img/campo.jpg" alt="Foto de Perfil">
                    </div>
                </div>
                <div class="formulario-usuario">
                    <label for="idioma">Idioma</label>
                    <select id="idioma" name="idioma">
                        <option value="Español" <?php if ($usuario['Idioma'] == 'Español') echo 'selected'; ?>>Español</option>
                        <option value="Inglés" <?php if ($usuario['Idioma'] == 'Inglés') echo 'selected'; ?>>Inglés</option>
                        <option value="Francés" <?php if ($usuario['Idioma'] == 'Francés') echo 'selected'; ?>>Francés</option>
                        <option value="Alemán" <?php if ($usuario['Idioma'] == 'Alemán') echo 'selected'; ?>>Alemán</option>
                        <option value="Italiano" <?php if ($usuario['Idioma'] == 'Italiano') echo 'selected'; ?>>Italiano</option>
                    </select>
                </div>
                <div class="formulario-usuario">
                    <label for="equipo">Equipo favorito</label>
                    <select id="equipo" name="equipo_favorito">
                        <option value="Deportivo Alavés" <?php if ($usuario['Equipo_favorito'] == 'Deportivo Alavés') echo 'selected'; ?>>Deportivo Alavés</option>
                        <option value="Athletic Club" <?php if ($usuario['Equipo_favorito'] == 'Athletic Club') echo 'selected'; ?>>Athletic Club</option>
                        <option value="Atlético de Madrid" <?php if ($usuario['Equipo_favorito'] == 'Atlético de Madrid') echo 'selected'; ?>>Atlético de Madrid</option>
                        <option value="FC Barcelona" <?php if ($usuario['Equipo_favorito'] == 'FC Barcelona') echo 'selected'; ?>>FC Barcelona</option>
                        <option value="Real Betis" <?php if ($usuario['Equipo_favorito'] == 'Real Betis') echo 'selected'; ?>>Real Betis</option>
                        <option value="Celta de Vigo" <?php if ($usuario['Equipo_favorito'] == 'Celta de Vigo') echo 'selected'; ?>>Celta de Vigo</option>
                        <option value="Espanyol" <?php if ($usuario['Equipo_favorito'] == 'Espanyol') echo 'selected'; ?>>Espanyol</option>
                        <option value="Getafe" <?php if ($usuario['Equipo_favorito'] == 'Getafe') echo 'selected'; ?>>Getafe</option>
                        <option value="Girona" <?php if ($usuario['Equipo_favorito'] == 'Girona') echo 'selected'; ?>>Girona</option>
                        <option value="Las Palmas" <?php if ($usuario['Equipo_favorito'] == 'Las Palmas') echo 'selected'; ?>>Las Palmas</option>
                        <option value="Leganés" <?php if ($usuario['Equipo_favorito'] == 'Leganés') echo 'selected'; ?>>Leganés</option>
                        <option value="Real Mallorca" <?php if ($usuario['Equipo_favorito'] == 'Real Mallorca') echo 'selected'; ?>>Real Mallorca</option>
                        <option value="Osasuna" <?php if ($usuario['Equipo_favorito'] == 'Osasuna') echo 'selected'; ?>>Osasuna</option>
                        <option value="Rayo Vallecano" <?php if ($usuario['Equipo_favorito'] == 'Rayo Vallecano') echo 'selected'; ?>>Rayo Vallecano</option>
                        <option value="Real Madrid" <?php if ($usuario['Equipo_favorito'] == 'Real Madrid') echo 'selected'; ?>>Real Madrid</option>
                        <option value="Real Sociedad" <?php if ($usuario['Equipo_favorito'] == 'Real Sociedad') echo 'selected'; ?>>Real Sociedad</option>
                        <option value="Sevilla" <?php if ($usuario['Equipo_favorito'] == 'Sevilla') echo 'selected'; ?>>Sevilla</option>
                        <option value="Valencia" <?php if ($usuario['Equipo_favorito'] == 'Valencia') echo 'selected'; ?>>Valencia</option>
                        <option value="Valladolid" <?php if ($usuario['Equipo_favorito'] == 'Valladolid') echo 'selected'; ?>>Valladolid</option>
                        <option value="Villarreal" <?php if ($usuario['Equipo_favorito'] == 'Villarreal') echo 'selected'; ?>>Villarreal</option>
                    </select>
                </div>
                <div class="formulario-usuario">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo $usuario['Telefono']; ?>" required>
                </div>
                <button class="boton-guardar" type="submit">Guardar Cambios</button>
            </form>
        </div>
        <div class="seccion-adicional">
            <button class="boton-home" onclick="window.location.href='index.html'">Home</button>
        </div>
    </div>
</body>

</html>
