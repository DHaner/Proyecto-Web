<?php
// Código PHP existente
session_start();
include("conexion.php");

$nombre = isset($_GET['nombre']) ? $_GET['nombre'] : ''; // Obtener el nombre del libro
$comentario_enviado = false; // Variable para saber si el comentario fue enviado correctamente

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
$queryUsuarios = $conn->prepare("SELECT DISTINCT dueño AS usuario FROM libro WHERE nombre = :nombre AND disponible = 'sí'");
$queryUsuarios->bindParam(':nombre', $nombre, PDO::PARAM_STR);
$queryUsuarios->execute();
$usuarios = $queryUsuarios->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'enviar_solicitud') {
    // Validar los datos del formulario
    $usuario_sol = $_SESSION['username']; // Usuario actual
    $usuario_pres = isset($_POST['usuarios']) ? $_POST['usuarios'] : '';
    $mensaje = isset($_POST['mensaje']) ? $_POST['mensaje'] : '';

    // Validar que no estén vacíos
    if (!empty($usuario_sol) && !empty($usuario_pres) && !empty($nombre)) {
        // Insertar en la tabla `solicitudes`
        $queryInsert = $conn->prepare("
            INSERT INTO solicitudes (usuario_sol, libro, usuario_pres, estado)
            VALUES (:usuario_sol, :libro, :usuario_pres, 'disponible')
        ");
        $queryInsert->bindParam(':usuario_sol', $usuario_sol, PDO::PARAM_STR);
        $queryInsert->bindParam(':libro', $nombre, PDO::PARAM_STR); // Libro actual
        $queryInsert->bindParam(':usuario_pres', $usuario_pres, PDO::PARAM_STR);

        if ($queryInsert->execute()) {
            echo "<script>alert('¡Solicitud enviada exitosamente!');</script>";
        } else {
            echo "<script>alert('Hubo un error al enviar la solicitud. Inténtalo de nuevo.');</script>";
        }
    } else {
        echo "<script>alert('Por favor, completa todos los campos del formulario.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Libro</title>
    <link rel="stylesheet" href="css/libro_solicitar.css">
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

            </div>
            <p class="sinopsis"><strong>Sinopsis:</strong> <?php echo htmlspecialchars($libro['sinopsis'] ?? 'Sinopsis no disponible'); ?></p>
        </div>

        <!-- SOLITIAR LIBRO -->
        <h1>Usuarios que poseen el libro "<?php echo htmlspecialchars($nombre); ?>"</h1>
        <div class="comments-section">
            <form method="POST" action="" class="solicitar-form">
                <h3>Solicitar</h3>
                <label for="mensaje">Usuarios con el libro:</label>
                <select name="usuarios" required>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo htmlspecialchars($usuario['usuario']); ?>">
                                <?php echo htmlspecialchars($usuario['usuario']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No hay usuarios con este libro</option>
                    <?php endif; ?>
                </select>

                <label for="mensaje">Mensaje:</label>
                <textarea name="mensaje" id="mensaje" rows="4" cols="50" placeholder="Escribe tu mensaje aquí..." required></textarea>
                <button type="submit" name="accion" value="enviar_solicitud">Enviar Solicitud</button>
            </form>


            <style>
                .solicitar-form {
                    background-color: #f9f9f9;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    margin: 20px auto;
                }

                .solicitar-form h3 {
                    margin-bottom: 15px;
                    font-size: 1.5em;
                    color: #333;
                }

                .solicitar-form select,
                .solicitar-form textarea,
                .solicitar-form button {
                    width: 100%;
                    padding: 10px;
                    margin-bottom: 10px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }

                .solicitar-form button {
                    background-color: #007bff;
                    color: white;
                    border: none;
                    cursor: pointer;
                }

                .solicitar-form button:hover {
                    background-color: #0056b3;
                }
            </style>
        </div>
    </main>
</body>

</html>