<?php
// Código PHP existente
session_start();
include("conexion.php");

$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : ''; // Obtener el nombre del libro
$comentario_enviado = false; // Variable para saber si el comentario fue enviado correctamente

// Verifica si el formulario de comentario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoge los datos del formulario
    $calificacion = isset($_POST['calificacion']) ? $_POST['calificacion'] : '';
    $usuario = $_SESSION['username']; // Suponiendo que el nombre de usuario está almacenado en la sesión
    $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
    $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';
    $libro = $nombre; // El nombre del libro ya fue recogido previamente
    $fecha = date('Y-m-d H:i:s'); // Fecha actual

    // Preparar la consulta SQL
    $queryInsert = $conn->prepare("INSERT INTO comentarios (calificacion, usuario, titulo, comentario, libro, fecha) 
                                    VALUES (:calificacion, :usuario, :titulo, :comentario, :libro, :fecha)");

    // Vincula los parámetros
    $queryInsert->bindParam(':calificacion', $calificacion, PDO::PARAM_INT);
    $queryInsert->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $queryInsert->bindParam(':titulo', $titulo, PDO::PARAM_STR);
    $queryInsert->bindParam(':comentario', $comentario, PDO::PARAM_STR);
    $queryInsert->bindParam(':libro', $libro, PDO::PARAM_STR);
    $queryInsert->bindParam(':fecha', $fecha, PDO::PARAM_STR);

    // Ejecuta la consulta
    if ($queryInsert->execute()) {
        // Si el comentario se ha enviado correctamente
        $comentario_enviado = true;
    } else {
        // Si hubo un error al agregar el comentario
        $comentario_enviado = false;
    }
}

// Consulta para obtener los detalles del libro
$query = $conn->prepare("SELECT * FROM `libro` WHERE `nombre` = :nombre");
$query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
$query->execute();
$libro = $query->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra el libro
if (!$libro) {
    echo "<script>
        alert('¡UPS! algo salió mal, parece que no tenemos ese libro');
        window.location.href='home.php';
    </script>";
    exit;
}

// Consulta para obtener comentarios relacionados al libro
$queryComentarios = $conn->prepare("SELECT * FROM comentarios WHERE libro = :nombre");
$queryComentarios->bindParam(':nombre', $nombre, PDO::PARAM_STR);
$queryComentarios->execute();
$comentarios = $queryComentarios->fetchAll(PDO::FETCH_ASSOC);

// Calcular promedio de calificaciones
$queryCalificaciones = $conn->prepare("SELECT AVG(calificacion) AS promedio FROM comentarios WHERE libro = :nombre");
$queryCalificaciones->bindParam(':nombre', $nombre, PDO::PARAM_STR);
$queryCalificaciones->execute();
$promedio = $queryCalificaciones->fetch(PDO::FETCH_ASSOC)['promedio'];

// Si no hay calificaciones, establecer un valor por defecto
if ($promedio === null) {
    $promedio = 0;
}

// Contar la cantidad de libros disponibles (con "SI" en la columna disponibles)
$queryDisponibles = $conn->prepare("SELECT COUNT(*) AS disponibles_count FROM libro WHERE nombre = :nombre AND disponible = 'SI'");
$queryDisponibles->bindParam(':nombre', $nombre, PDO::PARAM_STR);
$queryDisponibles->execute();
$disponibles = $queryDisponibles->fetch(PDO::FETCH_ASSOC)['disponibles_count'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';

    /*
    if ($accion === 'solicitar_intercambio') {
        $usuario = $_SESSION['username']; // Usuario actual
        $libro = $nombre; // Nombre del libro previamente recogido

        // Preparar la consulta SQL
        $querySolicitud = $conn->prepare("INSERT INTO solicitudes (usuario, libro) VALUES (:usuario, :libro)");

        // Vincular parámetros
        $querySolicitud->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $querySolicitud->bindParam(':libro', $libro, PDO::PARAM_STR);

        // Ejecutar la consulta
        if ($querySolicitud->execute()) {
            echo "<script>alert('¡Solicitud de intercambio enviada con éxito!');window.location.href='solicitudes.php';</script>";
        } else {
            echo "<script>alert('Error al enviar la solicitud. Por favor, intenta de nuevo.');window.location.href='home.php';</script>";
        }
    }*/
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Libro</title>
    <link rel="stylesheet" href="css/libro.css">
</head>

<body>
    <header>
        <div class="user-info">
            <span><?php echo $_SESSION["username"]; ?></span>
            <img src="img/usuario.png" alt="Icono de usuario">
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Inicio</a></li>
                <li><a href="solicitudes.php">Solicitudes</a></li>
                <li><a href="guardados.php">Guardados</a></li>
                <li><a href="mibiblioteca.php">Biblioteca</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="book-details">
            <div class="card-container">
                <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($libro['img'] ?? 'default.jpg'); ?>');"></div>
                <div class="card-overlay"></div>
                <div class="card-content">
                    <h3 class="card-title"><?php echo $libro["nombre"] . " - Disponibles: " . $disponibles; ?></h3>
                    <div class="card-author"><?php echo htmlspecialchars($libro['autor'] ?? 'Autor desconocido'); ?></div>
                    <div class="card-rating">
                        <span>⭐ <?php echo $promedio; ?></span>
                    </div>
                </div>
            </div>
            <p class="sinopsis"><strong>Sinopsis:</strong> <?php echo htmlspecialchars($libro['sinopsis'] ?? 'Sinopsis no disponible'); ?></p>
        </div>
        <div class="comments-section">

            <!-- Formulario de comentarios -->
            <h3>Agrega tu reseña</h3>
            <form method="POST" action="">
                <div class="star-rating">
                    <input type="radio" id="star5" name="calificacion" value="5" <?php echo (isset($_POST['calificacion']) && $_POST['calificacion'] == 5) ? 'checked' : ''; ?> />
                    <label for="star5" title="5 estrellas">⭐</label>
                    <input type="radio" id="star4" name="calificacion" value="4" <?php echo (isset($_POST['calificacion']) && $_POST['calificacion'] == 4) ? 'checked' : ''; ?> />
                    <label for="star4" title="4 estrellas">⭐</label>
                    <input type="radio" id="star3" name="calificacion" value="3" <?php echo (isset($_POST['calificacion']) && $_POST['calificacion'] == 3) ? 'checked' : ''; ?> />
                    <label for="star3" title="3 estrellas">⭐</label>
                    <input type="radio" id="star2" name="calificacion" value="2" <?php echo (isset($_POST['calificacion']) && $_POST['calificacion'] == 2) ? 'checked' : ''; ?> />
                    <label for="star2" title="2 estrellas">⭐</label>
                    <input type="radio" id="star1" name="calificacion" value="1" <?php echo (isset($_POST['calificacion']) && $_POST['calificacion'] == 1) ? 'checked' : ''; ?> />
                    <label for="star1" title="1 estrella">⭐</label>
                </div>
                <textarea id="comentario" name="comentario" placeholder="Escribe tu comentario aquí" required><?php echo isset($_POST['comentario']) ? htmlspecialchars($_POST['comentario']) : ''; ?></textarea>

                <button type="submit"></button>
            </form>
            <h3>Comentarios</h3>

            <!-- Carrusel de comentarios -->
            <div class="carousel-container">
                <button class="carousel-button prev">&#10094;</button>
                <div class="carousel">
                    <?php
                    if ($comentarios) {
                        $counter = 0;
                        foreach ($comentarios as $comentario) {
                    ?>
                            <div class="carousel-item <?php echo ($counter == 0) ? 'active' : ''; ?>">
                                <div class="comment-card">
                                    <h4><?php echo htmlspecialchars($comentario['titulo']); ?></h4>
                                    <div class="comment-author"><?php echo htmlspecialchars($comentario['usuario']); ?> - <?php echo date('d/m/Y', strtotime($comentario['fecha'])); ?></div>
                                    <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                                    <div class="comment-rating">
                                        ⭐ <?php echo $comentario['calificacion']; ?>
                                    </div>
                                </div>
                            </div>
                    <?php
                            $counter++;
                        }
                    } else {
                        echo "<p>No hay comentarios aún.</p>";
                    }
                    ?>
                </div>
                <button class="carousel-button next">&#10095;</button>
            </div>

            <a href="libro_solicitar.php?nombre=<?php echo $nombre; ?>"> papu vinculo </a>

        </div>
    </main>
</body>

</html>