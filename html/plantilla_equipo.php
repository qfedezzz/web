<?php
// Configuración de conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "TeamTactics";

// Conectar a la base de datos usando mysqli
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Obtener el nombre del equipo desde el parámetro GET
$equipo = isset($_GET['equipo']) ? $_GET['equipo'] : null;
if (!$equipo) {
    die("No se ha especificado un equipo.");
}

// Paso 1: Buscar el ID del equipo
$sql_equipo = "SELECT ID_equipo_liga FROM Equipos_Liga_Real WHERE Nombre = ?";
$stmt_equipo = $conn->prepare($sql_equipo);
$stmt_equipo->bind_param("s", $equipo);
$stmt_equipo->execute();
$result_equipo = $stmt_equipo->get_result();

if ($result_equipo->num_rows === 0) {
    die("El equipo especificado no existe.");
}

$equipo_data = $result_equipo->fetch_assoc();
$id_equipo = $equipo_data['ID_equipo_liga'];

// Paso 2: Obtener los jugadores del equipo
$sql_jugadores = "SELECT nombre, posicion, Imagen_URL FROM jugadores WHERE Equipo_real = ?";
$stmt_jugadores = $conn->prepare($sql_jugadores);
$stmt_jugadores->bind_param("i", $id_equipo);
$stmt_jugadores->execute();
$result_jugadores = $stmt_jugadores->get_result();

if ($result_jugadores->num_rows === 0) {
    die("No se encontraron jugadores para el equipo especificado.");
}

$jugadores = $result_jugadores->fetch_all(MYSQLI_ASSOC);

// Paso 3: Obtener el escudo del equipo
$sql_escudo = "SELECT Escudo FROM Equipos_Liga_Real WHERE Nombre = ?";
$stmt_escudo = $conn->prepare($sql_escudo);
$stmt_escudo->bind_param("s", $equipo); // Usamos el nombre del equipo seleccionado
$stmt_escudo->execute();
$result_escudo = $stmt_escudo->get_result();

// Verificamos si se obtuvo el escudo
if ($result_escudo->num_rows > 0) {
    $escudo_data = $result_escudo->fetch_assoc();
    $escudo_url = $escudo_data['Escudo']; // Guardamos la URL del escudo
} else {
    $escudo_url = "../img/default_escudo.png"; // Imagen por defecto si no hay escudo
}


// Clasificar jugadores por posición
$portero = array_filter($jugadores, fn($j) => $j['posicion'] == 1);
$defensas = array_filter($jugadores, fn($j) => $j['posicion'] == 2);
$mediocampistas = array_filter($jugadores, fn($j) => $j['posicion'] == 3);
$delanteros = array_filter($jugadores, fn($j) => $j['posicion'] == 4);
$suplentes = array_filter($jugadores, fn($j) => $j['posicion'] == 5);

// Asegurémonos de que tenemos exactamente 4 defensas
$defensas = array_values($defensas); // Reindexar el arreglo para asegurarnos de que las claves sean continuas

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Plantilla <?php echo htmlspecialchars($equipo); ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400&display=swap" rel="stylesheet">
    <script src="../js/main.js"></script>
    <link rel="icon" href="../img/favicon.png" type="image/x-icon">
</head>
<body>
    <!-- Encabezado con logo y menú -->
    <header>
        <div class="logo">
            <img src="../img/logo_teamtactics.png" alt="Logo">
        </div>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="plantilla.html">Plantillas</a></li>
                <li><a href="stats.html">Estadísticas</a></li>
                <li><a href="contact.html">Contacto</a></li>
                <li><a href="#login" id="login-btn">Login</a></li>
            </ul>
        </nav>
    </header>

    <!-- Línea blanca que separa las secciones -->
    <div class="section-divider-2-white"></div>

    <div class="field-container">
        <div class="field-background"></div>
        
        <!-- Escudo del equipo -->
        <div class="team-logo" style="top: 5%; left: 5%; position: absolute;">
            <img src="<?php echo htmlspecialchars($escudo_url); ?>" alt="Escudo <?php echo htmlspecialchars($equipo); ?>" title="Escudo <?php echo htmlspecialchars($equipo); ?>">
        </div>

        <!-- Jugadores titulares -->
        <div class="players">
            <!-- Portero -->
            <?php foreach ($portero as $jugador): ?>
                <div class="player" style="top: 5%; left: 45%;" title="Portero">
                    <img src="<?php echo htmlspecialchars($jugador['Imagen_URL']); ?>" alt="Portero">
                    <p><?php echo htmlspecialchars($jugador['nombre']); ?></p>
                </div>
            <?php endforeach; ?>

            <!-- Defensas -->
            <?php 
            // Definimos las posiciones para los defensas
            
            $posiciones_defensas = [
                ['left' => '20%', 'nombre' => $defensas[0]['nombre'], 'imagen' => $defensas[0]['Imagen_URL']], 
                ['left' => '37%', 'nombre' => $defensas[1]['nombre'], 'imagen' => $defensas[1]['Imagen_URL']], 
                ['left' => '53%', 'nombre' => $defensas[2]['nombre'], 'imagen' => $defensas[2]['Imagen_URL']], 
                ['left' => '70%', 'nombre' => $defensas[3]['nombre'], 'imagen' => $defensas[3]['Imagen_URL']]
            ];
            ?>

            <?php foreach ($posiciones_defensas as $defensa): ?>
                <div class="player" style="top: 25%; left: <?php echo $defensa['left']; ?>;" title="Defensa">
                    <img src="<?php echo htmlspecialchars($defensa['imagen']); ?>" alt="Defensa">
                    <p><?php echo htmlspecialchars($defensa['nombre']); ?></p>
                </div>
            <?php endforeach; ?>

            <!-- Mediocampistas -->
            <?php
            // Clasificar jugadores por posición
            $portero = array_filter($jugadores, fn($j) => $j['posicion'] == 1);
            $defensas = array_filter($jugadores, fn($j) => $j['posicion'] == 2);
            $mediocampistas = array_filter($jugadores, fn($j) => $j['posicion'] == 3);
            $delanteros = array_filter($jugadores, fn($j) => $j['posicion'] == 4);
            $suplentes = array_filter($jugadores, fn($j) => $j['posicion'] == 5);

            // Reindexamos los arreglos para evitar problemas con las claves del array
            $defensas = array_values($defensas);
            $mediocampistas = array_values($mediocampistas); // Reindexamos mediocampistas

            // Verificamos si hay mediocampistas suficientes
            if (count($mediocampistas) < 3) {
                die("Error: No se encontraron mediocampistas suficientes.");
            }
            ?>

            <div class="player" style="top: 45%; left: 25%;" title="Mediocampista">
                <img src="<?php echo htmlspecialchars($mediocampistas[0]['Imagen_URL']); ?>" alt="Medio 1">
                <p><?php echo htmlspecialchars($mediocampistas[0]['nombre']); ?></p>
            </div>
            <div class="player" style="top: 45%; left: 45%;" title="Mediocampista">
                <img src="<?php echo htmlspecialchars($mediocampistas[1]['Imagen_URL']); ?>" alt="Medio 2">
                <p><?php echo htmlspecialchars($mediocampistas[1]['nombre']); ?></p>
            </div>
            <div class="player" style="top: 45%; left: 65%;" title="Mediocampista">
                <img src="<?php echo htmlspecialchars($mediocampistas[2]['Imagen_URL']); ?>" alt="Medio 3">
                <p><?php echo htmlspecialchars($mediocampistas[2]['nombre']); ?></p>
            </div>


            <!-- Delanteros -->
            <?php
            // Clasificar jugadores por posición
            $portero = array_filter($jugadores, fn($j) => $j['posicion'] == 1);
            $defensas = array_filter($jugadores, fn($j) => $j['posicion'] == 2);
            $mediocampistas = array_filter($jugadores, fn($j) => $j['posicion'] == 3);
            $delanteros = array_filter($jugadores, fn($j) => $j['posicion'] == 4);
            $suplentes = array_filter($jugadores, fn($j) => $j['posicion'] == 5);

            // Reindexamos los arreglos para evitar problemas con las claves del array
            $defensas = array_values($defensas);
            $mediocampistas = array_values($mediocampistas); // Reindexamos mediocampistas
            $delanteros = array_values($delanteros); // Reindexamos delanteros

            // Verificamos si hay delanteros suficientes
            if (count($delanteros) < 3) {
                die("Error: No se encontraron delanteros suficientes.");
            }
            ?>

            <!-- Delanteros -->
            <div class="player" style="top: 65%; left: 25%;" title="Delantero">
                <img src="<?php echo htmlspecialchars($delanteros[0]['Imagen_URL']); ?>" alt="Delantero 1">
                <p><?php echo htmlspecialchars($delanteros[0]['nombre']); ?></p>
            </div>
            <div class="player" style="top: 70%; left: 45%;" title="Delantero">
                <img src="<?php echo htmlspecialchars($delanteros[1]['Imagen_URL']); ?>" alt="Delantero 2">
                <p><?php echo htmlspecialchars($delanteros[1]['nombre']); ?></p>
            </div>
            <div class="player" style="top: 65%; left: 65%;" title="Delantero">
                <img src="<?php echo htmlspecialchars($delanteros[2]['Imagen_URL']); ?>" alt="Delantero 3">
                <p><?php echo htmlspecialchars($delanteros[2]['nombre']); ?></p>
            </div>

        </div>

        <!-- Apartado de suplentes -->
        <div class="bench">
            <h3>BANQUILLO DE SUPLENTES</h3>
            <div class="bench-seats">
                <?php foreach ($suplentes as $jugador): ?>
                    <div class="seat" title="No Suplente">
                        <img src="<?php echo htmlspecialchars($jugador['Imagen_URL']); ?>" alt="Suplente">
                        <p><?php echo htmlspecialchars($jugador['nombre']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Pie de página -->
    <footer class="footer-plantillas">
        <p>&copy; 2024 - Tu Web de Fútbol. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
